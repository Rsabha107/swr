<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>WDR</title>
</head>
<body>
Hello,

<p>We received a request to reset your password for your WDR account.</p>

<p>Click the link below to set a new password:</p>
<a href="{{ route('reset.password.get', $token) }}">Reset Password</a><br>

<p>If you didn’t request a password reset, you can safely ignore this email.</p>


<p>Thanks,  <br>
The WDR Team</p>
</body>
</html>
