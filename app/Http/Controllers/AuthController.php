<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Services\PasswordResetService;

class AuthController extends Controller
{
    /**
     * Hiển thị trang đăng nhập.
     */
    public function login()
    {
        return view('auth.login');
    }

    /**
     * Xử lý đăng nhập.
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->validate(
            [
                'email' => ['required', 'email'],
                'password' => ['required'],
            ],
            [
                'email.required' => 'Vui lòng nhập email.',
                'email.email' => 'Email không đúng định dạng.',
                'password.required' => 'Vui lòng nhập mật khẩu.',
            ]
        );

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->route('home');
        }

        return back()
            ->withErrors([
                'email' => 'Email hoặc mật khẩu không đúng.',
            ])
            ->onlyInput('email');
    }


    /**
     * Hiển thị trang đăng ký.
     */
    public function register()
    {
        return view('auth.register');
    }


    /**
     * Xử lý đăng ký.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],

                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    'unique:users,email',
                ],

                'phone' => [
                    'required',
                    'string',
                    'max:20',
                ],

                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'confirmed',
                ],
            ],
            [
                'name.required' => 'Vui lòng nhập họ và tên.',

                'email.required' => 'Vui lòng nhập email.',
                'email.email' => 'Email không đúng định dạng.',
                'email.unique' => 'Email này đã được sử dụng.',

                'phone.required' => 'Vui lòng nhập số điện thoại.',

                'password.required' => 'Vui lòng nhập mật khẩu.',
                'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
                'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            ]
        );

        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => 'customer',
        ]);

        // Registration ends at the login page so the user can explicitly authenticate.

        return redirect()->route('login')->with('success', 'Đăng ký thành công. Vui lòng đăng nhập.');
    }

    /**
     * Xử lý đăng xuất.
     */
    public function forgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendOtp(Request $request, PasswordResetService $passwordResetService)
    {
        $validated = $request->validate(
            ['email' => ['required', 'email']],
            [
                'email.required' => 'Vui lòng nhập email.',
                'email.email' => 'Email không đúng định dạng.',
            ],
        );

        $email = strtolower($validated['email']);
        $isResend = $request->boolean('resend');
        $rateLimitKey = 'password-reset-resend|'.$request->ip().'|'.sha1($email);

        if ($isResend && RateLimiter::tooManyAttempts($rateLimitKey, 1)) {
            return back()
                ->withErrors(['email' => 'Vui lòng đợi trước khi yêu cầu mã OTP mới.'])
                ->withInput();
        }

        $user = User::where('email', $email)->first();

        // if (! $user) {
        //     return back()
        //         ->withErrors(['email' => 'Email không tồn tại trong hệ thống.'])
        //         ->withInput();
        // }

        if ($isResend) {
            RateLimiter::hit($rateLimitKey, 60);
        }

        try {
            $passwordResetService->sendOtp($user);
        } catch (\Throwable $exception) {
            return back()
                ->withErrors(['email' => 'Không thể gửi email OTP. Vui lòng thử lại sau.'])
                ->withInput();
        }

        $request->session()->put('password_reset_email', $email);

        return redirect()->route('password.verify')
            ->with('success', 'Mã OTP đã được gửi đến email của bạn.');
    }

    public function verifyOtp(Request $request)
    {
        if (! $request->session()->has('password_reset_email')) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Vui lòng yêu cầu mã OTP trước.']);
        }

        return view('auth.verify-otp', [
            'email' => $request->session()->get('password_reset_email'),
        ]);
    }

    public function verifyOtpCode(Request $request, PasswordResetService $passwordResetService)
    {
        $validated = $request->validate(
            ['otp' => ['required', 'digits:6']],
            [
                'otp.required' => 'Vui lòng nhập mã OTP.',
                'otp.digits' => 'Mã OTP phải gồm 6 chữ số.',
            ],
        );

        $email = $request->session()->get('password_reset_email');

        if (! $email) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Phiên đặt lại mật khẩu đã hết hạn.']);
        }

        $result = $passwordResetService->verify($email, $validated['otp']);

        if ($result === 'valid') {
            return redirect()->route('password.reset');
        }

        if ($result === 'locked') {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Bạn đã nhập sai OTP quá nhiều lần. Vui lòng yêu cầu mã mới.']);
        }

        return back()->withErrors([
            'otp' => 'Mã OTP không đúng hoặc đã hết hạn.',
        ]);
    }

    public function resetPassword(Request $request, PasswordResetService $passwordResetService)
    {
        $email = $request->session()->get('password_reset_email');

        if (! $email || ! $passwordResetService->isVerified($email)) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Vui lòng xác thực OTP trước khi đặt lại mật khẩu.']);
        }

        return view('auth.reset-password');
    }

    public function updatePassword(Request $request, PasswordResetService $passwordResetService)
    {
        $validated = $request->validate(
            ['password' => ['required', 'string', 'min:8', 'confirmed']],
            [
                'password.required' => 'Vui lòng nhập mật khẩu mới.',
                'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
                'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            ],
        );

        $email = $request->session()->get('password_reset_email');

        if (! $email || ! $passwordResetService->isVerified($email)) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Phiên đặt lại mật khẩu đã hết hạn.']);
        }

        try {
            $passwordResetService->resetPassword($email, $validated['password']);
        } catch (\Throwable $exception) {
            return back()->withErrors([
                'password' => 'Không thể cập nhật mật khẩu. Vui lòng thử lại.',
            ]);
        }

        $request->session()->forget('password_reset_email');

        return redirect()->route('login')
            ->with('success', 'Đặt lại mật khẩu thành công. Vui lòng đăng nhập.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
