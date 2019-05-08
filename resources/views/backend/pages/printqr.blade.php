<!DOCTYPE html>
<html>
<head>
	<title>Print QR Codes Collection</title>
</head>
<body>
	<table>
		<tr>
			@php 
				$count = 0; 
				$arrCount = 0;
				foreach($pngImage as $p):
			@endphp
				<td style="text-align: center;">
					<img src="data:image/png;base64, {{ base64_encode($p)}}"><br>
					<span>{{$room_no[$arrCount]}}</span>
				</td>
			@php 
				$count ++; 
				$arrCount ++;
				if($count==3){
					echo '</tr><tr>';
					$count=0;
				}
				endforeach;
			@endphp
		</tr>
	</table>
	
</body>
<script type="text/javascript">
	window.print();
</script>
</html>
