
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
	<h1>Password Recovery</h1>
	
	<p>Please check your email for your new password.
	</p>
	
	
	
<?php 
	if (isset($errorMsg)) {
		echo "<p>" . $errorMsg . "</p>";
	}

	echo "<p>" . anchor('account/index','Login') . "</p>";
?>	
</body>

</html>

