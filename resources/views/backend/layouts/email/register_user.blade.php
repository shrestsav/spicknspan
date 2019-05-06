<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Notification</title>
</head>
<body>
	<p>These are your login credentials for Spick and Span Portal. Please Change your password immediately after you receive this email for User privacy</p>
	
	<p><span>Username:</span> {{$username}} </p>
	<p><span>Password:</span> {{$password}} </p>

	<p>Go to your <a href="{{env('APP_URL', 'https://spicknspan.com')}}" target="_blank">Dashboard </a></p><br>

	<p>Regards,<br> Spick And Span Team </p>
</body>
</html>