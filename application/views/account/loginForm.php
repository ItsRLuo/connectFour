
<!DOCTYPE html>

<html>
	<head>
		<link rel="stylesheet" type="text/css" href="<?= base_url() ?>/css/login.css">
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600,700,300' rel='stylesheet' type='text/css'>		
		<style>
			input {
				display: block;
			}
		</style>

	</head> 
<body>  
<?php 
	if (isset($errorMsg)) {
		echo "<p>" . $errorMsg . "</p>";
	}

	echo "<h1>CONNECT<span>4</span></h1>";
	echo "<h2>LOGIN</h2>";
	echo form_open('account/login');
	echo form_label('Username'); 
	echo form_error('username');
	echo form_input('username',set_value('username'),"required");
	echo "<br />";
	echo form_label('Password'); 
	echo form_error('password');
	echo form_password('password','',"required");
	
	echo form_submit('submit', 'Login');
	
	echo "<p>" . anchor('account/newForm','Create Account') . "</p>";

	echo "<p>" . anchor('account/recoverPasswordForm','Recover Password') . "</p>";
	
	
	echo form_close();
?>	
</body>

</html>

