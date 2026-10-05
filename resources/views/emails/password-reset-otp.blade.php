<div style="font-family: Arial, sans-serif; color: #111827; line-height: 1.6;">
    <h2>{{ config('app.name') }}</h2>

    <p>Xin chào,</p>

    <p>Bạn vừa yêu cầu đặt lại mật khẩu cho tài khoản của mình.</p>

    <p style="font-size: 28px; font-weight: 700; letter-spacing: 8px; color: #16a34a;">
        {{ $otp }}
    </p>

    <p>Mã OTP có hiệu lực trong {{ $expiresInMinutes }} phút.</p>
    <p><strong>Không chia sẻ mã này với bất kỳ ai.</strong></p>

    <p>Nếu bạn không yêu cầu đặt lại mật khẩu, có thể bỏ qua email này.</p>
</div>
