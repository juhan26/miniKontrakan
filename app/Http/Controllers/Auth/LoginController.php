<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Exceptions\Renderer\Exception;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    // protected function redirectTo()
    // {
    //     $user = Auth::user();

    //     if ($user->hasRole('admin') || $user->hasRole('super_admin')) {
    //         return redirect('/dashboard');
    //     }

    //     if ($user->hasRole('member') && $user->status === 'accepted') {
    //         return redirect('/');
    //     }

    //     return redirect('/');
    // }

    public function login(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'g-recaptcha-response' => 'recaptcha',
            recaptchaFieldName() => recaptchaRuleName(),
            'email' => 'required|email',
            'password' => 'required',
        ],[
            'g-recaptcha-response.recaptcha'=>'Captcha tidak valid',
            'email.required'=>'Email wajib diisi',
            'email.email'=>'Kolom email harus terdapat "@" dan "."',
            'password.required'=>'Password wajib diisi',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }else{
            $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials)) {
                $user = Auth::user();

                // Check if the user has the role 'member' and status 'pending'
                if ($user->roles->contains('name', 'tenant') && $user->status === 'pending') {
                    Auth::logout();
                    $request->session()->flash('error', 'Akun Anda masih belum dikonfirmasi admin, silakan hubungi admin.');
                    return redirect()->route('login');
                } elseif ($user->roles->contains('name', 'tenant') && $user->status === 'rejected') {
                    Auth::logout();
                    $request->session()->flash('error', 'Akun anda ditolak oleh admin, silakan hubungi admin.');
                    return redirect('/');
                } elseif ($user->roles->contains('name', 'tenant') && $user->status === 'accepted') {
                    return redirect('/');
                }

                return redirect()->intended('dashboard');
            }

            // If authentication fails, redirect back with error
            return back()->withErrors([
                'email' => 'email atau password yang anda berikan tidak cocok',
            ]);

        }
    }

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
}
