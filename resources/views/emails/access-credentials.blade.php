<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>TotalCashPro access</title>
</head>
<body style="font-family: Inter, Arial, sans-serif; color: #111827; line-height: 1.6;">
    <p>Hi {{ $user->name }},</p>
    <p>Your TotalCashPro account for <strong>{{ $organization->name }}</strong> is ready.</p>
    <p>
        <strong>Login:</strong> <a href="{{ $loginUrl }}">{{ $loginUrl }}</a><br>
        <strong>Email:</strong> {{ $user->email }}<br>
        <strong>Temporary password:</strong> {{ $password }}
    </p>
    <p>Please sign in and change your password after your first login.</p>
    <p>— TotalCashPro</p>
</body>
</html>
