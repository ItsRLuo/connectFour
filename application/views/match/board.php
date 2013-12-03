
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
	//Create Alert as long as the invited user hasn't decided to join the game or not
	while(status=="waiting"){
			alert("Waiting On Other Player");
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
	$(document).ready(function () {
		
			
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
//Drawing the Info,gameBoard and chat
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

			if (isset($userPlayerID)) {
				echo $userPlayerID;
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
		//init variables
		currTurnID = <?php echo $currentTurn; ?>;
		userID = <?php echo $userPlayerID; ?>;
		opponentID = 3 - userID;
		opponentStrID = opponentID.toString();
		opponentMadeMoveURL = "<?= base_url() ?>board/opponentMadeMove";
		makeMoveURL = "<?= base_url() ?>board/makeMove";
		checkVictoryURL = "<?= base_url() ?>board/checkVictory";
		finishGameURL = '<?= base_url() ?>board/finishGame';

		// Inserts a token into a slot, if it's the user's turn.
		$('body').delegate('.emptySlot','click', makeMove);

		
		// If it's the opponent's turn, this waits for the opponent to make a move.
		
		$('body').everyTime(200, waitForOpponent);

		
	});

	function waitForOpponent() {
		//Run after a move is made and updating for the other player
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
	            },
	            error: function(x,y,z){
	            },
	            complete: function(x,y){
	            }
	      	});

	        
		}
	}
	
	
	function makeMove() {
		//function runs if player made a move on the board
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
	            },
	            error: function(x, y, z) {
	                 //  alert('error\n'+x+'\n'+y+'\n'+z);
	            },
	            complete: function(x, y){
	            }
	      	});
	        
	        return false;
		}
	}

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

	function checkVictory(arguments) {
		//with the controller, check to see the outcome(win,lose or draw) and act accordingly
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
			},
            error: function(x, y, z) {
            },
            complete: function(x, y){
            }
		});
	}
	

	function gameFinished() {
		//Jump back to arcade/index page when there is a outcome for checkvictory
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
	
	function getLowestRowInColumn(colNum) {
		//find the lowest unfilled circle for the board
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
	
	function extractRowNum(slot) {
		//Take out the row numbest of the slot
		var regex = /row(\d)-col\d/;
		var returnSlot = slot.attr('id').replace(regex, "$1");
		return parseInt(returnSlot);
	}
	
	function extractColNum(slot) {
		//take out the colnum number of the slot
		var regex = /row\d-col(\d)/;
		var returnSlot = slot.attr('id').replace(regex, "$1");
		return parseInt(returnSlot);
	}
</script>
 
 
</body>

</html>
