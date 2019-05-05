<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Notification</title>
</head>
<body>
	<p><span>Message:</span> {{ $msg }} </p>
	<p><span>Username:</span> {{ $username }} </p>
	<p><span>Password:</span> {{ $password }} </p>

	<p>Go back to your <a href="{{env('APP_URL', 'https://spicknspan.com')}}" target="_blank">Dashboard </a></p><br>

	<p>Regards,<br> Spick And Span Team </p>
</body>
</html>