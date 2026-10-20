<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\SendOtpRequest;
use App\Http\Requests\Auth\VerifyOtpCodeRequest;
use App\Models\User;
use App\Services\PasswordResetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

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
    public function authenticate(LoginRequest $request)
    {
        $credentials = $request->validated();
        $remember = $request->boolean('remember');

        if (Auth::validate($credentials)) {
            $user = User::where('email', $credentials['email'])->first();
            if (!$user->is_active) {
                return back()
                    ->withErrors(['email' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.'])
                    ->onlyInput('email');
            }
        }

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
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
    public function store(RegisterRequest $request)
    {
        $validated = $request->validated();

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => 'customer',
        ]);

        return redirect()->route('login')->with('success', 'Đăng ký thành công. Vui lòng đăng nhập.');
    }

    /**
     * Hiển thị trang yêu cầu đặt lại mật khẩu.
     */
    public function forgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Gửi mã OTP đặt lại mật khẩu tới email người dùng.
     */
    public function sendOtp(SendOtpRequest $request, PasswordResetService $passwordResetService)
    {
        $validated = $request->validated();
        $email = strtolower($validated['email']);
        $isResend = $request->boolean('resend');
        $rateLimitKey = 'password-reset-resend|' . $request->ip() . '|' . sha1($email);

        if ($isResend && RateLimiter::tooManyAttempts($rateLimitKey, 1)) {
            return back()
                ->withErrors(['email' => 'Vui lòng đợi trước khi yêu cầu mã OTP mới.'])
                ->withInput();
        }

        $user = User::where('email', $email)->first();

        // Kiểm tra tài khoản tồn tại trong hệ thống
        if (!$user) {
            return back()
                ->withErrors(['email' => 'Email không tồn tại trong hệ thống.'])
                ->withInput();
        }

        // Kiểm tra tài khoản có bị khóa hay không
        if (!$user->is_active) {
            return back()
                ->withErrors(['email' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.'])
                ->withInput();
        }

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

    /**
     * Hiển thị trang nhập mã xác nhận OTP.
     */
    public function verifyOtp(Request $request)
    {
        if (!$request->session()->has('password_reset_email')) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Vui lòng yêu cầu mã OTP trước.']);
        }

        return view('auth.verify-otp', [
            'email' => $request->session()->get('password_reset_email'),
        ]);
    }

    /**
     * Kiểm tra tính hợp lệ của mã OTP.
     */
    public function verifyOtpCode(VerifyOtpCodeRequest $request, PasswordResetService $passwordResetService)
    {
        $validated = $request->validated();
        $email = $request->session()->get('password_reset_email');

        if (!$email) {
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

    /**
     * Hiển thị form nhập mật khẩu mới sau khi xác thực OTP thành công.
     */
    public function resetPassword(Request $request, PasswordResetService $passwordResetService)
    {
        $email = $request->session()->get('password_reset_email');

        if (!$email || !$passwordResetService->isVerified($email)) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Vui lòng xác thực OTP trước khi đặt lại mật khẩu.']);
        }

        return view('auth.reset-password');
    }

    /**
     * Cập nhật mật khẩu mới vào cơ sở dữ liệu.
     */
    public function updatePassword(ResetPasswordRequest $request, PasswordResetService $passwordResetService)
    {
        $validated = $request->validated();
        $email = $request->session()->get('password_reset_email');

        if (!$email || !$passwordResetService->isVerified($email)) {
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
