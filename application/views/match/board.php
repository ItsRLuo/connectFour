
<!DOCTYPE html>

<html>
	<head>
	<script src="http://code.jquery.com/jquery-latest.js"></script>
	<script src="<?= base_url() ?>/js/jquery.timers.js"></script>
	<script>

		var otherUser = "<?= $otherUser->login ?>";
		var user = "<?= $user->login ?>";
		var status = "<?= $status ?>";
		
		$(function(){
			$('body').everyTime(2000,function(){
					if (status == 'waiting') {
						$.getJSON('<?= base_url() ?>arcade/checkInvitation',function(data, text, jqZHR){
								if (data && data.status=='rejected') {
									alert("Sorry, your invitation to play was declined!");
									window.location.href = '<?= base_url() ?>arcade/index';
								}
								if (data && data.status=='accepted') {
									status = 'playing';
									$('#status').html('Playing ' + otherUser);
								}
								
						});
					}
					var url = "<?= base_url() ?>board/getMsg";
					$.getJSON(url, function (data,text,jqXHR){
						if (data && data.status=='success') {
							var conversation = $('[name=conversation]').val();
							var msg = data.message;
							if (msg.length > 0)
								$('[name=conversation]').val(conversation + "\n" + otherUser + ": " + msg);
						}
					});
			});

			$('form').submit(function(){
				var arguments = $(this).serialize();
				var url = "<?= base_url() ?>board/postMsg";
				$.post(url,arguments, function (data,textStatus,jqXHR){
						var conversation = $('[name=conversation]').val();
						var msg = $('[name=msg]').val();
						$('[name=conversation]').val(conversation + "\n" + user + ": " + msg);
						});
				return false;
				});	
		});
	
	</script>
	</head> 
<body>  
	<h1>Game Area</h1>

	<div>
	Hello <?= $user->fullName() ?>  <?= anchor('account/logout','(Logout)') ?>  
	</div>
	
	<div id='status'> 
	<?php 
		if ($status == "playing")
			echo "Playing " . $otherUser->login;
		else
			echo "Wating on " . $otherUser->login;
	?>
	</div>

<body>

<!--  <canvas id="myCanvas" width="500" height="500" style="border:5px solid #c3c3c3;">
</canvas>

<script>

var c=document.getElementById("myCanvas");
var ctx=c.getContext("2d");

ctx.fillStyle="#FFCC00";
ctx.fillRect(0,0,500,500);

ctx.fillStyle= "#FFFFFF";
$a = 50;
$b = 100;
for ($y=0; $y<=5; $y++){
	for ($x=0; $x<=6; $x++)
	{
		ctx.beginPath();
		ctx.arc($a, $b, 27, 0, Math.PI*2, true); 
		ctx.closePath();
		ctx.fill();
		$a = $a + 65;
	}
	$b = $b + 65;
	$a = 50;
}

</script>-->

<div style="BACKGROUND-COLOR: #FFCC00; BORDER-BOTTOM: black thin solid; BORDER-LEFT: black thin solid; BORDER-RIGHT: black thin solid; BORDER-TOP: black thin solid; HEIGHT: 500px; LEFT: 400px; POSITION: absolute; TOP: 65px; WIDTH: 500px"></div>



</body>


	
<?php 
	echo "<link rel='stylesheet' type='text/css' href='" . base_url() . "css/gameboard.css'>";
	echo "<div class='circleBase type1'></div>";
	
	echo "<div class='circleBase type2'></div><div class='circleBase type2'></div>";
	
	echo "<div class='circleBase type3'></div>";
	
	
	echo form_textarea('conversation');
	echo form_open();
	echo form_input('msg');
	echo form_submit('Send','Send');
	echo form_close();
	
?>
	
	
	
	
</body>

</html>

