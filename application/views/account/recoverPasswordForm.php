
<!DOCTYPE html>

<html>
	<head>
	    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>/css/style.css">
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600,700,300' rel='stylesheet' type='text/css'>	
		<style>
			input {
				display: block;
			}
		</style>

	</head> 
<body>  
	<h1>Recover Password</h1>
<?php 
	if (isset($errorMsg)) {
		echo "<p>" . $errorMsg . "</p>";
	}

	echo form_open('account/recoverPassword');
	echo form_label('Email'); 
	echo form_error('email');
	echo form_input('email',set_value('email'),"required");
	echo form_submit('submit', 'Recover Password');
	echo form_close();
?>	
</body>

</html>

