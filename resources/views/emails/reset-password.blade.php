<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Reset your password</title>
</head>
<body style="margin:0;padding:24px;background:#f8fafc;font-family:Arial,Helvetica,sans-serif;color:#0f172a;">
    <div style="max-width:640px;margin:0 auto;background:#ffffff;border:1px solid #dbe4f0;border-radius:16px;overflow:hidden;">
        <div style="padding:32px;">
            <h1 style="margin:0 0 16px;font-size:28px;line-height:1.2;">Reset your password</h1>
            <p style="margin:0 0 16px;font-size:16px;line-height:1.6;">
                Hello {{ $name ?: 'there' }},
            </p>
            <p style="margin:0 0 24px;font-size:16px;line-height:1.6;">
                We received a request to reset your CivicEase password. Use the button below to choose a new password.
            </p>
            <p style="margin:0 0 32px;">
                <a href="{{ $resetUrl }}" style="display:inline-block;padding:14px 24px;border-radius:999px;background:#1f4f8c;color:#ffffff;text-decoration:none;font-weight:700;">
                    Reset password
                </a>
            </p>
            <p style="margin:0 0 12px;font-size:14px;line-height:1.6;color:#475569;">
                This link will expire in {{ $expiresInMinutes }} minutes.
            </p>
            <p style="margin:0 0 12px;font-size:14px;line-height:1.6;color:#475569;">
                If the button does not work, copy and paste this link into your browser:
            </p>
            <p style="margin:0;font-size:14px;line-height:1.6;word-break:break-all;">
                <a href="{{ $resetUrl }}" style="color:#1f4f8c;">{{ $resetUrl }}</a>
            </p>
        </div>
    </div>
</body>
</html>
