$(document).ready(function() {
	function checkPassword() {
		var p1 = $("#pass1"); 
		var p2 = $("#pass2");
		
		if (p1.val() == p2.val()) {
			p1.get(0).setCustomValidity("");  // All is well, clear error message
			return true;
		}	
		else	 {
			p1.get(0).setCustomValidity("Passwords do not match");
			return false;
		}
	}

	document.getElementById("pass2").oninput = checkPassword;
	
});