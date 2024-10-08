<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeedbackRequest;
use App\Http\Requests\UpdateFeedbackRequest;
use App\Models\Feedback;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(Request $request)
    {


        $message = [
            'message.required' => 'Pesan harus diisi',
            'message.string' => 'Pesan harus berupa teks',
            'message.max' => 'Pesan maksimal 1000 karakter',
            'lease_id.required' => 'Belum Mempunyai Kontrak',
        ];

        $request->validate([
            'message' => 'required|string|max:1000',
            'lease_id' => 'required|exists:leases,id',
            'rating' => 'required|integer|min:1|max:5',
        ], $message);

        $feedback = new Feedback();
        $feedback->message = $request->message;
        $feedback->rating = $request->rating;
        $feedback->lease_id = $request->lease_id;

        // if (Auth::check()) {
        //     $feedback->user_id = Auth::id();
        //     $feedback->user_name = Auth::user()->name;
        //     $feedback->user_image = Auth::user()->profile_image ?? 'image_not_available.png';
        // }

        $feedback->save();

        return redirect()->back()->with('success', 'Terimakasih atas masukannya!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Feedback $feedback)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Feedback $feedback)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFeedbackRequest $request, Feedback $feedback)
    {
        $feedback->update($request->all());
        return redirect()->back()->with('success', 'Feedback berhasil diubah!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Feedback $feedback)
    {
        try {
            $feedback->delete();
            return back()->with('success', 'Berhasil menghapus Feedback');
        } catch (Exception $e) {
            return back()->with('error', 'Gagal menghapus Feedback');
        }
    }

    public function submit(Request $request)
    {
        $feedback = new Feedback();
        $feedback->user_id = Auth::check() ? Auth::id() : null;
        $feedback->message = $request->input('feedback');
        $feedback->save();

        return redirect()->back()->with('success', 'Masukan berhasil di kirimg!');
    }
}
