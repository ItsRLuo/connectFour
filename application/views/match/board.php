<!DOCTYPE html>
<html>
<head>

	<script src="http://code.jquery.com/jquery-latest.js"></script>
	<script src="<?= base_url() ?>/js/jquery.timers.js"></script>
	<script type="text/javascript" src="<?= base_url() ?>/js/scripts/board.php"></script>
	<link rel='stylesheet' type='text/css' href="<?= base_url(); ?>css/gameboard.css"></link>
	<link rel='stylesheet' type='text/css' href="<?= base_url(); ?>css/style.css"></link>
	<script>
	
	var otherUser = "<?= $otherUser->login ?>";
	var user = "<?= $user->login ?>";
	var status = "<?= $status ?>";
	$(document).ready(function () {
		
		// Run this code when ever a chat message is submitted.
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

	    $('body').everyTime(2000, function () {

		    // Run this if we are waiting for an opponent to accept OR reject a game request.
	        if (status == 'waiting') {
	            $.getJSON('<?= base_url() ?>arcade/checkInvitation', function (data, text, jqZHR) {
	                if (data && data.status == 'rejected') {
	                    alert("Sorry, your invitation to play was declined!");
	                    window.location.href = '<?= base_url() ?>arcade/index';
	                }
	                if (data && data.status == 'accepted') {
	                    status = 'playing';
	                    $('#status').html('Playing ' + otherUser);
	                    window.location.href = '<?= base_url() ?>board/index';
	                }
	
	            });
	        }

	        // Get any messages the opponent sends through chat.
	        var url = "<?= base_url() ?>board/getMsg";
	        $.getJSON(url, function (data, text, jqXHR) {
	            if (data && data.status == 'success') {
	                var conversation = $('[name=conversation]').val();
	                var msg = data.message;
	                if (msg != null && msg.length > 0 ) {
	                    $('[name=conversation]').val(conversation + "\n" + otherUser + ": " + msg);
	                }
	            }
	        });

		});
	});

</script>
</head>

<!-- Display the gameboard, game info and the chat interface. -->

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
					echo "Waiting on " . $otherUser->login . ", the game has NOT started yet...";
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
			echo form_close();
		?>
	</div>
	<script>
	var userID;
	var currTurnID;
	var opponentID;
	var opponentStrID;
	var opponentMadeMoveURL;
	var makeMoveURL;

	Array.prototype.max = function() {
		 return Math.max.apply(null, this);
	};

	Array.prototype.contains = function(item) {
		return this.indexOf(item) >= 0;
	}
	
	$(document).ready(function() {
		// Initialized variables:
		currTurnID = <?php echo $currentTurn; ?>;
		userID = <?php echo $userPlayerID; ?>;
		opponentID = 3 - userID;
		opponentStrID = opponentID.toString();

		// URLs
		opponentMadeMoveURL = "<?= base_url() ?>board/opponentMadeMove";
		makeMoveURL = "<?= base_url() ?>board/makeMove";
		checkVictoryURL = "<?= base_url() ?>board/checkVictory";
		finishGameURL = '<?= base_url() ?>board/finishGame';

		// Insert a token into a slot, if it's the user's turn.
		$('body').delegate('.emptySlot','click', makeMove);

		// If it's the opponent's turn, wait for the opponent to make a move.
		$('body').everyTime(200, waitForOpponent);

		
	});

	// Wait for the opponent to make a move.
	function waitForOpponent() {
		
		if (userID != currTurnID) {
			var argArray = {"userTurn": userID};
			var arguments = $.param(argArray);

	        $.ajax({
	            type: "GET",
	            url: opponentMadeMoveURL,
	            data: arguments,
	            success: function(data) {
		            var data_decode = JSON.parse(data);
		            if (data_decode.status == "success" && data_decode.board && data_decode.curr_player == userID) {
		            	//Refresh the page to match the other player
	            		updateBoard(data_decode.board);
	            		currTurnID = userID;

	            		var arguments = {'playerID': opponentID, 'col_num': data_decode.col_num, 
	    	            				 'row_num': data_decode.row_num, 'userID': userID};
						//Check to see if anybody has won
	            		checkVictory(arguments);
	            	}
	            }
	      	});

	        
		}
	}
	

	// Make a move on the game board.
	function makeMove() {

		var board;
		if (userID == currTurnID) {
	 		var thisColNum = extractColNum($(this));
			var lowestSlot = getLowestRowInColumn(thisColNum);
			lowestSlot.addClass('player' + userID).removeClass('emptySlot');
			var thisRowNum = extractRowNum(lowestSlot);

			var argArray = {"playerID": userID, "currentPlayerTurn": currTurnID, "pieceAdded": new Array(thisColNum, thisRowNum)};
			var arguments = $.param(argArray);

	        $.ajax({
	            type: "POST",
	            url: makeMoveURL,
	            data: arguments,
	            success: function(data){
		            var data_decode = JSON.parse(data);
		            if (data_decode.status == "success" && data_decode.board) {
		            	//Refresh the page to match the other player
	            		updateBoard(data_decode.board);
	            		currTurnID = 3 - currTurnID;

	            		var arguments = {'playerID': userID, 'col_num': thisColNum, 
	    	            		'row_num': thisRowNum, 'userID': userID};
						//Check to see if anybody has won
	            		checkVictory(arguments);
	            	}
	            }
	      	});
	        
	        return false;
		}
	}

	// Update the game board, with the contents in boardArr. 
	function updateBoard(boardArr) {
		//Go through the game board 
		for (var i = 0; i < 6; i++) {
			for (var j = 0; j < 7; j++) {
				var item = getByRowColIndexFull(i, j);
				if (boardArr[i][j] == 0) {
					item.addClass('emptySlot').removeClass('player1').removeClass('player2');
				} else {
					item.addClass('player' + boardArr[i][j]).removeClass('emptySlot');
				}
			} 
		}
	}

	// Use controller/board.php to see if a win, loss or draw has occurred.
	function checkVictory(arguments) {
		$.ajax({
			type: "GET",
			url: checkVictoryURL,
			data: arguments,
			success: function(data) {
				data_decode = JSON.parse(data);
				var outcomes = ["win", "lose", "draw"];
				if (outcomes.contains(data_decode.outcome)) {
					alert(data_decode.message);
					gameFinished();
				}
			}
		});
	}
	
	// Return to arcade/index once the game is complete.
	function gameFinished() {
		
		var arguments = {"player": userID};
		$.ajax({
			type: "POST",
			url: finishGameURL,
			data: arguments,
			success: function() {
			}, 
			complete: function() {
				//go back to index
				window.location.href = '<?= base_url() ?>arcade/index';
			}
		});
		
	}
	
	// Return the lowest unfilled slot on the board in the given column number.
	function getLowestRowInColumn(colNum) {
	
		slots = new Array();
		$(".boardSlot.emptySlot").each(function() { 

			var thisColNum = extractColNum($(this));
			if (colNum == thisColNum) {
				slots.push(extractRowNum($(this)));
			}
			
		});
	
		var lowestRow = slots.max();
		return getByRowColIndex(lowestRow, colNum);
		
	}

	// Given a row and column index, return the appropriate slot on the game board,
	// as a JQuery object, if it is empty.
	function getByRowColIndex(row, col) {
	
		retVal = null;
		$(".boardSlot.emptySlot").each(function() {
			if ((extractRowNum($(this)) == row) && (extractColNum($(this)) == col)) {
				retVal = $(this);
				return false;
			} 
		});
		return retVal;
	
	}

	// Given a row and column index, return the appropriate slot on the game board,
	// as a JQuery object.
	function getByRowColIndexFull(row, col) {
		retVal = null;
		$(".boardSlot").each(function() {
			if ((extractRowNum($(this)) == row) && (extractColNum($(this)) == col)) {
				retVal = $(this);
				return false;
			} 
		});
		return retVal;
	}

	// Given a slot on the game board, get its row number.
	function extractRowNum(slot) {
		
		var regex = /row(\d)-col\d/;
		var returnSlot = slot.attr('id').replace(regex, "$1");
		return parseInt(returnSlot);
	}
	
	// Given a slot on the game board, get its column number.
	function extractColNum(slot) {
		
		var regex = /row\d-col(\d)/;
		var returnSlot = slot.attr('id').replace(regex, "$1");
		return parseInt(returnSlot);
	}
</script>
 
 
</body>

</html>
