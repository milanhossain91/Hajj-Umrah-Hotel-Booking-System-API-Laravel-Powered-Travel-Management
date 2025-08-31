<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body>
    <p>Dear user,</p>

    <p>You requested to reset your password. Click the link below to reset it:</p>

    <p><a href="{{ $resetUrl }}">{{ $resetUrl }}</a></p>

    <p>If you didn’t request a password reset, you can safely ignore this email.</p>

    <p>Regards,<br>SCF Naogaon</p>
</body>
</html>
