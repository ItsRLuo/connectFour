
<!DOCTYPE html>

<html>
<head>
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script src="<?= base_url() ?>/js/jquery.timers.js"></script>
<link rel='stylesheet' type='text/css' href="<?php echo base_url(); ?>css/gameboard.css"></link>
<link rel='stylesheet' type='text/css' href="<?php echo base_url(); ?>css/style.css"></link>
<script>
	var otherUser = "<?= $otherUser->login ?>";
	var user = "<?= $user->login ?>";
	var status = "<?= $status ?>";
	
	$(function () {
	    $('body').everyTime(2000, function () {
	        if (status == 'waiting') {
	            $.getJSON('<?= base_url() ?>arcade/checkInvitation', function (data, text, jqZHR) {
	                if (data && data.status == 'rejected') {
	                    alert("Sorry, your invitation to play was declined!");
	                    window.location.href = '<?= base_url() ?>arcade/index';
	                }
	                if (data && data.status == 'accepted') {
	                    status = 'playing';
	                    $('#status').html('Playing ' + otherUser);
	                }
	
	            });
	        }
	        var url = "<?= base_url() ?>board/getMsg";
	        $.getJSON(url, function (data, text, jqXHR) {
	            if (data && data.status == 'success') {
	                var conversation = $('[name=conversation]').val();
	                var msg = data.message;
	                if (msg.length > 0)
	                    $('[name=conversation]').val(conversation + "\n" + otherUser + ": " + msg);
	            }
	        });
	    });
	
	    $('form').submit(function () {
	        var arguments = $(this).serialize();
	        var url = "<?= base_url() ?>board/postMsg";
	        $.post(url, arguments, function (data, textStatus, jqXHR) {
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
	<div class="infoMessage">
		<div class="greetingMessage">
			Hello
			<?= $user->fullName() ?>
			<?= anchor('account/logout','(Logout)') ?>
		</div>
		<div id='status'>
			<?php 
				if ($status == "playing")
					echo "Playing " . $otherUser->login;
				else
					echo "Waiting on " . $otherUser->login;
			?>
		</div>
	</div>
	<br />
	<table class="gameBoard">
		<?php 
			for ($row = 0; $row < 6; $row++) {
				echo "<tr class='row$row'>";
				for ($col = 0; $col < 7; $col++) {
					echo "<td><div id='row$row-col$col' class='circleBase boardSlot emptySlot'></div></td>";
				}
				echo "</tr>";
			}
		?>
	</table>
	<div class="chatSection">
		<?php 
			echo form_textarea(array('name' => 'conversation', 'disabled'));
			echo form_open();
			echo form_input('msg');
			echo form_submit('Send','Send');
			if (isset($userPlayerID)) { 
				echo $userPlayerID; 
			}
			
			echo form_close();
		?>
	</div>
<head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js">
</script>
<script>

Array.prototype.max = function() {
  return Math.max.apply(null, this);
};

$(document).ready(function() {


	
	var currTurnID = <?php echo $currentTurn; ?>;
	var userID = <?php echo $userPlayerID; ?>;
	
	if (userID == currTurnID) {

		$('body').delegate('.emptySlot','click',function() {
			
			var thisColNum = extractColNum($(this).attr('id'));
			var lowestSlot = getLowestRowInColumn(thisColNum);
			if (retVal == null) {
				alert("Column is full!");
			}
			else {
				lowestSlot.addClass('player' + userID).removeClass('emptySlot');
			}

		});

	}

	function getLowestRowInColumn(colNum) {
		
		slots = new Array();
		$(".boardSlot.emptySlot").each(function() {
			var thisColNum = extractColNum($(this).attr('id'));
			if (colNum == thisColNum) {
				slots.push(extractRowNum($(this).attr('id')));
			}
		});

		var lowestRow = slots.max();
		return getByRowColIndex(lowestRow, colNum);
		
	}

	function getByRowColIndex(row, col) {

		retVal = null;
		$(".boardSlot.emptySlot").each(function() {
			if ((extractRowNum($(this).attr('id')) == row) && (extractColNum($(this).attr('id')) == col)) {
				retVal = $(this);
				return false;
			} 
		});
		return retVal;

	}
	
	function extractRowNum(slot) {
		var regex = /row(\d)-col\d/;
		var returnSlot = slot.replace(regex, "$1");
		return parseInt(returnSlot);
	}

	function extractColNum(slot) {
		var regex = /row\d-col(\d)/;
		var returnSlot = slot.replace(regex, "$1");
		return parseInt(returnSlot);
	}


			
	
	
});


</script>
</head>
<body>

</body>

	
	

</body>

</html>


<!--  <canvas id="myCanvas" width="500" height="500" style="border:5px solid #c3c3c3;"> </canvas> -->
<!-- <div class='circleBase type1'></div> -->
<!-- <div class='circleBase type2'></div> -->
<!-- <div class='circleBase type2'></div> -->
<!-- <div class='circleBase type3'></div> -->
<!-- 
$(document).ready(function(){
	
  /*	$("p").click(function(){
	var a=  document.getElementById("0-0");
	var scrolltimes = 0;
	$(document).ready(function() {
	$('#clay').scroll(function() {
	$('#scrollamount p').html({'<p>Scrolled: '+ .scrolltimes++ + '</p>'});
	});*/
});


-->
