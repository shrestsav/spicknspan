<!DOCTYPE html>
<html>
<head>
	<title>Print QR</title>
</head>
<body>
	<img src="data:image/png;base64, {{ base64_encode($pngImage)}}">
</body>
<script type="text/javascript">
	window.print();
</script>
</html>
