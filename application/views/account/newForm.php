<?php 

	
?>
<!DOCTYPE html>

<html>
	<head>
		
		<link rel="stylesheet" type="text/css" href="<?= base_url() ?>/css/style.css">
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600,700,300' rel='stylesheet' type='text/css'>	
		<script src="http://code.jquery.com/jquery-latest.js"></script>
		<script src="<?php echo base_url() ?>js/scripts/jQueryScript.js"></script>
		<script>
		
		$(document).ready(function() {

			$(".changeCaptchaText").click(function() {
				$("#captcha").attr('src', '<?php echo base_url() ?>securimage/securimage_show.php?' + Math.random()); 
			});
			
		});
		
		</script>
	</head> 
<body>  
	<h1>New Account</h1>
<?php 
	echo form_open('account/createNew');
	echo form_label('Username'); 
	echo form_error('username');
	echo form_input('username',set_value('username'),"required");
	echo form_label('Password'); 
	echo form_error('password');
	echo form_password('password','',"id='pass1' required");
	echo form_label('Password Confirmation'); 
	echo form_error('passconf');
	echo form_password('passconf','',"id='pass2' required'");
	echo form_label('First');
	echo form_error('first');
	echo form_input('first',set_value('first'),"required");
	echo form_label('Last');
	echo form_error('last');
	echo form_input('last',set_value('last'),"required");
	echo form_label('Email');
	echo form_error('email');
	echo form_input('email',set_value('email'),"required");
	
	if (isset($captchaErrorMessage)) {
		echo $captchaErrorMessage;
	}
	
	echo "<br/>";
	echo '<img id="captcha" src="' . base_url() . 'securimage/securimage_show.php" alt="CAPTCHA Image" /><br/><br/>';
	echo '<input type="text" name="captcha_code" size="10" maxlength="6" /><br/>';
	echo '<a class="changeCaptchaText" href="#">[ Different Image ]</a><br/><br/>';
	echo form_submit('submit', 'Register');
	echo form_close();
?>	
</body>

</html>

