<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Instance;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Lease;
use App\Models\PaymentPerMonth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status', []);

        // Query umum
        $query = User::query();
        $query2 = User::query();

        if ($search || !empty($status)) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%$search%")
                    ->orWhere('email', 'LIKE', "%$search%")
                    ->orWhereHas('instance', function ($query) use ($search) {
                        $query->where('name', 'LIKE', "%$search%");
                    });
            })->when(!empty($status), function ($query) use ($status) {
                $query->whereIn('status', $status);
            });
        }
        if ($search) {
            $query2->where(function ($query2) use ($search) {
                $query2->where('name', 'LIKE', "%$search%")
                    ->orWhere('email', 'LIKE', "%$search%")
                    ->orWhereHas('instance', function ($query2) use ($search) {
                        $query2->where('name', 'LIKE', "%$search%");
                    });
            });
        }

        // Tenant users
        $users = $query->whereHas('roles', function ($query) {
            $query->where('name', 'tenant');
        })
            ->orderByRaw("
        CASE
            WHEN status = 'pending' THEN 1
            WHEN status = 'accepted' THEN 2
            WHEN status = 'rejected' THEN 3
            ELSE 4
        END
    ")
            ->paginate(10);

        $users2 = $query2->whereHas('roles', function ($query2) {
            $query2->where('name', 'admin');
        })
            ->orderByRaw("
        CASE
            WHEN status = 'pending' THEN 1
            WHEN status = 'accepted' THEN 2
            WHEN status = 'rejected' THEN 3
            ELSE 4
        END
    ")
            ->paginate(10);

        // Debugging to check the resulting SQL and bindings

        // Attach query parameters for pagination links
        $users->appends([
            'search' => $search,
            'status' => $status,
        ]);
        $users2->appends([
            'search' => $search,
        ]);

        $instances = Instance::orderBy('name', 'ASC')->get();

        return view('pages.users.index', compact('users', 'users2', 'instances', 'status'));
    }





    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
    public function accept(User $user)
    {
        $user->update(['status' => 'accepted']);
        $user->save();
        return redirect()->route('user.index')->with('success', 'Penyewa telah di konfirmasi');
    }
    public function reject(User $user)
    {
        $user->status = 'rejected';
        $user->save();
        return redirect()->route('user.index')->with('success', 'Penyewa telah berhasil ditolak');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        // dd($request->instance_id);
        if ($request->photo) {

            $imagePath = $request->photo->store('photos', 'public');
            User::create([
                'photo' => $imagePath,
                'gender' => $request->gender,
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'instance_id' => $request->instance_id,
                'status' => 'accepted',
                'password' =>  bcrypt('Tenant2024'), // Hash the password before storing it
            ])->assignRole('tenant');
        } else
            User::create([
                'gender' => $request->gender,
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'instance_id' => $request->instance_id,
                'status' => 'accepted',
                'password' =>  bcrypt('Tenant2024'), // Hash the password before storing it
            ])->assignRole('tenant');
        return redirect()->route('user.index')->with('success', 'Pengguna berhasil di tambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        if (Auth::user()->id !== $user->id) {
            return redirect()->route('user.index')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }

        // Mengambil data instance untuk dropdown atau pilihan lainnya
        $instances = Instance::orderBy('name', 'ASC')->get();

        // Mengirim data pengguna dan instance ke view
        return view('pages.users.show', compact('user', 'instances'));
    }
    public function profile(User $user)
    {
        if (Auth::user()->id !== $user->id) {
            return redirect()->route('user.index')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }

        // Mengambil data instance untuk dropdown atau pilihan lainnya
        $instances = Instance::orderBy('name', 'ASC')->get();

        // Mengirim data pengguna dan instance ke view
        return view('landing.users.show', compact('user', 'instances'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            if ($user->photo) {
                Storage::delete('public/' . $user->photo);
            }

            // Simpan foto baru
            $photoPath = $request->file('photo')->store('photos', 'public');
            $user->photo = $photoPath;
        }

        $user->name = $request->input('name');
        $user->phone_number = $request->phone_number;

      if (!$request->memberMenu){
        $leases = Lease::where('user_id', $user->id)
            ->with('properties')
            ->get();
        // dd($leases);

        foreach ($leases as $lease) {
            if ($request->gender !== $lease->properties->gender_target) {
                return redirect()->back()->with('error', 'Perubahan Gagal Karena tidak sesuai dengan kontrakan yang anda pilih');
            }
        }
        $user->gender = $request->gender;
        }
        // Lanjutkan proses penyimpanan data pengguna



        $user->save();

        return redirect()->back()->with('success', 'Pengguna berhasil diubah');
    }


    public function deletePropertyLeader(User $user)
    {

        $lastLeader = $user->lease()->whereHas('user.roles', function ($query) {
            $query->where('name', 'admin');
        })->first();

        if ($lastLeader) {
            $lastLeader->user->removeRole('admin');
            $lastLeader->user->assignRole('tenant');
        }
        return redirect()->route('user.index')->with('success', 'Berhasil memberhentikan Ketua Kontrakan');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8',
            'confirmPassword' => 'required|same:password',
        ], [
            'password.required' => 'Password Tidak Boleh Kosong',
            'password.min' => 'Password Minimal Harus Terdiri Dari 8 Karakter',
            'confirmPassword.required' => 'Konfirmasi Password Tidak Boleh Kosong',
            'confirmPassword.same' => 'Password Harus Sama Dengan Konfirmasi Password',
        ]);

        if ($request->password === Auth::user()->password) {
            return back()->with('error', 'Password Tidak Boleh Sama');
        }

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->back()->with('success', 'Password Berhasil Diubah');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $user->delete();
            return redirect()->route('user.index')->with('success', 'Pengguna berhasil di hapus');
        } catch (\Exception $e) {
            $user->assignRole('tenant');
            return redirect()->route('user.index')->with('error', 'Tidak dapat menghapus pengguna ini karena pengguna memiliki kontrak');
        }
    }

    public function history($user)
    {
        // Mendapatkan ID pengguna yang sedang login
        $user = Auth::user()->id;

        // Mendapatkan lease yang terkait dengan pengguna yang sedang login beserta data pembayaran
        $leases = Lease::where('user_id', $user)
            ->with('payments')
            ->latest()
            ->paginate(10);

        // Mengirim data ke view
        return view('landing.users.history', compact('leases'));
    }
}
