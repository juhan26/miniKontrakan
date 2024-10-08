<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeaseRequest;
use App\Http\Requests\UpdateLeaseRequest;
use App\Models\Lease;
use App\Models\PaymentPerMonth;
use App\Models\Property;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Lease::with(['user', 'properties']);

        // Get input values
        $propertySearch = $request->input('search');
        $status = $request->input('status', []);
        $property_id = $request->input('property_id', []);

        // Common query filters based on input
        if ($propertySearch || $status || $property_id) {
            $query->where(function ($query) use ($propertySearch, $status, $property_id) {
                if ($propertySearch) {
                    $query->whereHas('user', function ($query) use ($propertySearch) {
                        $query->where('name', 'LIKE', "%{$propertySearch}%");
                    })
                        ->orWhereHas('properties', function ($query) use ($propertySearch) {
                            $query->where('name', 'LIKE', "%{$propertySearch}%");
                        })
                        ->orWhere('status', 'LIKE', "%{$propertySearch}%");
                }

                if (!empty($status)) {
                    $query->whereIn('status', $status);
                }

                if (!empty($property_id)) {
                    $query->whereIn('property_id', $property_id);
                }
            });
        }

        // Check user role and apply role-based filters
        if (Auth::user()->hasRole('super_admin')) {
            $leases = $query->orderByRaw("CASE
                    WHEN status = 'active' AND total_nominal < total_iuran THEN 1
                    WHEN status = 'active' AND total_nominal >= total_iuran THEN 2
                    WHEN status = 'expired' AND total_nominal < total_iuran THEN 3
                    ELSE 4
                  END")
                ->latest()

                ->paginate(10);
            $leases->appends([
                'search' => $propertySearch,
                'status' => $status,
                'property_id' => $property_id,
            ]);

            $properties = Property::all();
            $users = User::with(['lease'])->whereHas('roles', function ($query) {
                $query->where('name', '!=', 'super_admin');
            })->where('status', 'accepted')->whereDoesntHave('lease')->get();
        } else {
            if (Auth::user()->lease) {
                $query->whereHas('properties', function ($query) {
                    $query->where('id', Auth::user()->lease->property_id);
                });
            }

            $leases = $query->orderByRaw("CASE
                    WHEN status = 'active' AND total_nominal < total_iuran THEN 1
                    WHEN status = 'active' AND total_nominal >= total_iuran THEN 2
                    WHEN status = 'expired' AND total_nominal < total_iuran THEN 3
                    ELSE 4
                  END")
                ->latest()
                ->paginate(10);

            // Append query parameters to pagination links
            $leases->appends([
                'search' => $propertySearch,
                'status' => $status,
                'property_id' => $property_id,
            ]);

            $properties = Property::all();
            $users = User::with(['lease'])->whereHas('roles', function ($query) {
                $query->where('name', '!=', 'super_admin');
            })->where('status', 'accepted')->whereDoesntHave('lease')->get();
        }

        return view('pages.leases.index', compact('leases', 'properties', 'status', 'property_id', 'users'));
    }





    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLeaseRequest $request)
    {
        $user = User::findOrFail($request->user_id);
        $property = Property::findOrFail($request->property_id);
        if ($user->gender == $property->gender_target) {
            $existingLease = Lease::where('user_id', $request->user_id)
                ->where('end_date', '>', now())
                ->first();

            if ($existingLease) {
                return redirect()->route('leases.index')->with('error', 'Pengguna sudah memiliki sewa.');
            }

            $property = Property::find($request->property_id);

            if (!$property) {
                return redirect()->route('leases.index')->with('error', 'Kontrakan tidak di temukan.');
            }

            // Menggunakan Carbon untuk menangani tanggal
            $startDate = \Carbon\Carbon::parse($request->start_date);
            $totalMonth = (int) $request->end_date;
            $totalIuran = $totalMonth * $property->rental_price;
            $end_date = $startDate->copy()->addMonth($totalMonth);

            // Cek apakah end_date lebih awal dari start_date
            // if ($endDate->lt($startDate)) {
            //     return redirect()->back()->with('error', 'Tanggal akhir tidak boleh lebih awal dari tanggal mulai.');
            // }

            // Cek apakah start_date dan end_date berada di bulan yang sama
            // if ($startDate->isSameMonth($endDate)) {
            //     // Hitung total_iuran menggunakan rental_price dari property
            //     $totalIuran = $property->rental_price;
            // } else {
            //     $totalMonths = $startDate->diffInMonths($endDate->copy()->addMonth()) + 1;
            //     $totalIuran = $totalMonths * $property->rental_price;
            // }

            $capacity = $property->capacity;

            if (Lease::where('property_id', $request->property_id)->where('status', 'active')->count() < $capacity) {

                if (Lease::where('property_id', $request->property_id)->where('status', 'active')->count() == ($capacity - 1)) {
                    $property->update(['status' => 'full']);
                } else {
                    $property->update(['status' => 'available']);
                }

                $lease = Lease::create([
                    'user_id' => $request->user_id,
                    'property_id' => $request->property_id,
                    'start_date' => $request->start_date,
                    'end_date' => $end_date,
                    'description' => $request->description,
                    'total_iuran' => number_format($totalIuran, 2, '.', ''), // Format dengan dua desimal
                    'total_nominal' => 0,
                ]);

                $nominal = $request->first_paid_month;
                $totalNominal = $lease->total_nominal + $nominal;
                $startDate = \Carbon\Carbon::parse($lease->start_date);
                $totalMonthsPaid = floor($lease->total_nominal / $lease->properties->rental_price);
                $monthsToAdd = floor($nominal / $lease->properties->rental_price);
                $startPaymentMonth = $startDate->copy()->addMonths($totalMonthsPaid);
                $paymentMonth = $startDate->copy()->addMonths($totalMonthsPaid + $monthsToAdd);
                $startPaymentMonthFormatted = $startPaymentMonth->format('d F Y');
                $paymentMonthFormatted = $paymentMonth->format('d F Y');

                PaymentPerMonth::create([
                    'lease_id' => $lease->id,
                    'payment_month' =>  $startPaymentMonthFormatted,
                    'month' => $paymentMonthFormatted,
                    'due_date' => Carbon::parse($paymentMonthFormatted)->addDays(3),
                    'payment_date' => today(),
                    'nominal' => $request->first_paid_month,
                ]);

                $lease->update([
                    'total_nominal' => $totalNominal,
                ]);

                return redirect()->back()->with('success', 'Kontrak berhasil di tambahkan.');
            } else {
                return redirect()->back()->with('error', 'Kontrakan Sudah Penuh.');
            }
        } else {
            return redirect()->back()->withInput()->with('error', 'Jenis kelamin user ini dengan terget jenis kelamin kontrakan tidak sesuai.');
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Lease $lease)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lease $lease)
    {
        //
    }

    public function done(Lease $lease, Request $request)
    {
        if ($lease->total_nominal >= $lease->total_iuran) {
            $lease->update([
                'status' => 'expired',
                'description' => 'Kontrak Telah Usai'
            ]);
            return redirect()->route('leases.index')->with('success', 'Berhasil Menyelesaikan Kontrak.');
        } else {
            $lease->update([
                'status' => 'expired',
                'description' => $request->description
            ]);
            return redirect()->route('leases.index')->with('success', 'Berhasil Menyelesaikan Paksa Kontrak.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLeaseRequest $request, Lease $lease)
    {
        $property = Property::find($lease->properties->id);

        if (!$property) {
            return redirect()->route('leases.index')->with('error', 'Kontrakan tidak di temukan.');
        }

        // Menggunakan Carbon untuk menangani tanggal
        $startDate = \Carbon\Carbon::parse($request->start_date);
        $totalMonth = (int) $request->end_date;
        $totalIuran = $lease->total_iuran + ($totalMonth * $property->rental_price);
        $end_date = $startDate->copy()->addMonth($totalMonth);

        // Cek apakah end_date lebih awal dari start_date
        // if ($endDate->lt($startDate)) {
        //     return redirect()->back()->with('error', 'Tanggal akhir tidak boleh lebih awal dari tanggal mulai.');
        // }

        // if ($startDate->isSameMonth($endDate)) {
        //     $totalIuran = $property->rental_price;
        // } else {
        //     $totalMonths = $startDate->copy()->endOfMonth()->diffInMonths($endDate->startOfMonth()) + 1;
        //     $totalIuran = $totalMonths * $property->rental_price;
        // }

        $lease->update([
            'end_date' => $end_date,
            'status' => 'active',
            'description' => $request->description,
            'total_iuran' => number_format($totalIuran, 2, '.', ''),
        ]);

        // if ($request->end_date > $lease->end_date) {
        //     $lease->update([
        //         'end_date' => $request->end_date,
        //         'status' => 'active',
        //         'description' => $request->description,
        //         'total_iuran' => number_format($totalIuran, 2, '.', ''),
        //     ]);
        // } else {
        //     $lease->update([
        //         'end_date' => $request->end_date,
        //         'description' => $request->description,
        //         'total_iuran' => number_format($totalIuran, 2, '.', ''),
        //     ]);
        // }

        return redirect()->route('leases.index')->with('success', 'Data kontrak berhasil di ubah.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lease $lease)
    {
        try {
            $property = Property::find($lease->property_id);
            $property->update(['status' => 'available']);

            if ($lease->user->hasRole('admin')) {
                $lease->user->removeRole('admin');
                $lease->user->assignRole('tenant');
            }

            $lease->delete();
            return redirect()->route('leases.index')->with('success', 'Kontrak berhasil di hapus');
        } catch (\Exception $e) {
            if ($e->getCode() === '23000') {
                return redirect()->route('leases.index')->with('error', 'Tidak dapat menghapus kontrak ini karena data memiliki data terkait di tabel lain');
            }
        }
    }
}
