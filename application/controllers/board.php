<?php

class Board extends CI_Controller {
     
    function __construct() {
    		// Call the Controller constructor
    	parent::__construct();
    	session_start();
    } 
          
    public function _remap($method, $params = array()) {
	    	// enforce access control to protected functions	
    		
    	if (!isset($_SESSION['user'])) {
   			redirect('account/loginForm', 'refresh'); //Then we redirect to the index page again
    	}
	    return call_user_func_array(array($this, $method), $params);
    }
  
    function flattenArr($arr) {
    	
    	$arrs = array();
    	
    	try
    	{
    		foreach ($arr as $arrElem) {
    			//place holder
    			if (isint($arrElem)){
    				foreach ($arrElem as $arrElemElem) {
    					//echo gettype($arrElemElem);
    					foreach ($arrElemElem as $arrElemElemElem){
    						//echo $arrElemElemElem;
    						array_push($arrs, $arrElemElemElem);
    					}
    				}
    			}
    		};
    		
    		return $arrs;
    	}
    	catch (Exception $e)
    	{
    		throw new Exception( 'Something really gone wrong', 0, $e);
    		return null;
    	}
    	
    }
    
    function checkHorizontal($player, $board, $col_num, $row_num) {
    	
    	$currentLongest = 0;
    	
    	for ($i = -3; $i < 4; $i++) {
    		if ($i == 0) {
    			$currentLongest++;
    		}
    		else if ($col_num + $i < 0) {
    			$currentLongest = 0;
    			continue;
    		}
    		else if ($col_num + $i > 6) {
    			return false;
    		}
    		else if ($board[$row_num][$col_num + $i] == $player) {
    			$currentLongest++;
    		} 
    		else {
    			$currentLongest = 0;
    			continue;
    		}
    		
    		if ($currentLongest == 4) {
    			return true;
    		}
    	}
    	
    	return false;

    }
    
    function checkVertical($player, $board, $col_num, $row_num) {
    	
    	$currentLongest = 0;
    	 
    	for ($i = -3; $i < 4; $i++) {
    		if ($i == 0) {
    			$currentLongest++;
    		}
    		else if ($row_num + $i < 0) {
    			$currentLongest = 0;
    			continue;
    		}
    		else if ($row_num + $i > 5) {
    			return false;
    		}
    		else if ($board[$row_num + $i][$col_num] == $player) {
    			$currentLongest++;
    		}
    		else {
    			$currentLongest = 0;
    			continue;
    		}
    	
    		if ($currentLongest == 4) {
    			return true;
    		}
    	}
    	 
    	return false;

    }
    
    function checkDiagonal($player, $board, $col_num, $row_num) {
    	
	    $currentLongest = 0;
	    
	    for ($i = -3; $i <= 3; $i++) {
	    	if ($i == 0) {
	    		$currentLongest++;
	    	}
	    	else if ($row_num + $i < 0 || $col_num + $i < 0) {
	    		$currentLongest = 0;
	    		continue;
	    	}
	    	else if ($row_num + $i > 5 || $col_num + $i > 6) {
	    		return false;
	    	}
	    	else if ($board[$row_num + $i][$col_num + $i] == $player) {
	    		$currentLongest++;
	    	}
	    	else {
	    		$currentLongest = 0;
	    		continue;
	    	}
	    	 
	    	if ($currentLongest == 4) {
	    		return true;
	    	}
	    }
	    
	    for ($i = -3; $i <= 3; $i++) {
	    	if ($i == 0) {
	    		$currentLongest++;
	    	}
	    	else if ($row_num + $i < 0 || $col_num + $i > 6) {
	    		$currentLongest = 0;
	    		continue;
	    	}
	    	else if ($row_num + $i > 5 || $col_num + $i < 0) {
	    		return false;
	    	}
	    	else if ($board[$row_num + $i][$col_num + $i] == $player) {
	    		$currentLongest++;
	    	}
	    	else {
	    		$currentLongest = 0;
	    		continue;
	    	}
	    
	    	if ($currentLongest == 4) {
	    		return true;
	    	}
	    }
	    
	    return false;

    }
    
    function checkDraw($board){
    	//If all board is filled and there is no one has won, checkDraw returns true

     	for ($j = 0; $j < 6; $j++) {
    	    if ($board[0][$j] == 0) {
    	        return False;
    	    }
    	}
    	
    	return True;
    }
    
	function checkVictory() {
		//Run through all above checking condition to get a outcome and json_encode it
		$this->load->model('user_model');
		$this->load->model('match_model');
		$user = $_SESSION['user'];
		$user = $this->user_model->get($user->login);
		$match = $this->match_model->get($user->match_id);
		$arr = json_decode($match->board_state);
		$board = $arr->match_arr;
		 
		//get variable from inputs
		$player = $this->input->get("playerID");
		$userID = $this->input->get("userID");
		$col_num = $this->input->get("col_num");
		$row_num = $this->input->get("row_num");
		 
		//init different conditon to array
		$winArray = array('status'=>'success','message'=>"You win!", 'outcome' => 'win');
		$loseArray = array('status'=>'success','message'=>"You lose!", 'outcome' => 'lose');
		$drawArray = array('status'=>'success','message'=>"Draw!", 'outcome' => 'draw');
		$noWinArray = array('status' => 'success', 'message'=> 'No victory conditions', 'outcome' => 'none');
		 
		//run check condition functions 
		if ($this->checkVertical($player, $board, $col_num, $row_num) ||
			$this->checkHorizontal($player, $board, $col_num, $row_num) ||
			$this->checkDiagonal($player, $board, $col_num, $row_num)) {
			 
			if ($userID == $player) {
				echo json_encode($winArray);
			} else {
				echo json_encode($loseArray);
			}
			 
		}
		 
		else if ($this->checkDraw($board)) {
			echo json_encode($drawArray);
		}
		else {
			echo json_encode($noWinArray);
		}
	}    
    
	function finishGame() {
		//Reset everything to make sure there is no carry over for the next play through
		$winner;
		$this->load->model('user_model');
		$this->load->model('match_model');
		$this->load->model('invite_model');
		$user = $_SESSION['user'];
		$user = $this->user_model->getExclusive($user->login);
		$userID = $user->id;
		if ($user->user_status_id != User::PLAYING) {
			$errormsg="Not in PLAYING state";
			goto error;
		}
		
		// start transactional mode
		$this->db->trans_begin();
		
		// Set the match status to finished.
		$match = $this->match_model->get($user->match_id);
		if ($this->input->post("player") == 1) {
			$winner = 2;
		} else {
			$winner = 3;
		}
		$this->match_model->updateStatus($user->match_id, $winner);
		
		// Set the MATCH ID and INVITE ID of both players to NULL, and userSTATUSID to AVAILABLE.
		if ($match->user1_id == $userID) {
			$otherUser = $this->user_model->getFromId($match->user2_id);
		} else {
			$otherUser = $this->user_model->getFromId($match->user1_id);
		}
		//update everything
		$otherUserID = $otherUser->id;
 		$this->user_model->updateMatch($otherUserID, NULL);
 		$this->user_model->updateMatch($userID, NULL);
 		$this->user_model->updateStatus($otherUserID, 2);
 		$this->user_model->updateStatus($userID, 2);
 		$this->user_model->updateInvitation($otherUserID, NULL);
 		$this->user_model->updateInvitation($userID, NULL);
		
		// DELETE THE ASSOCIATED INVITE.
		$this->invite_model->deleteByID($user->match_id);
		
		// CHECK IF EVERYTHING WENT WELL, AND RETURN THE PROPER ENCODED ARRAY:
		if ($this->db->trans_status() === FALSE) {
 			$errormsg = "Transaction error";
 			goto transactionerror;
 		}
 		
 		$this->db->trans_commit();
 		
 		echo json_encode(array('status'=>'success','message' => $msg));
		return;
		
		transactionerror:
		$this->db->trans_rollback();
		
		error:
		echo json_encode(array('status'=>'failure','message' => $errormsg));
	}
	
    function index() {
		$user = $_SESSION['user'];
    	$this->load->model('user_model');
    	$this->load->model('invite_model');
    	$this->load->model('match_model');
    	//load model
    	$user = $this->user_model->get($user->login);
    	$match = $this->match_model->get($user->match_id);
    	$invite = $this->invite_model->get($user->invite_id);
    	
    	// The inviter
    	if ($user->user_status_id == User::WAITING) {
    		$invite = $this->invite_model->get($user->invite_id);
    		$otherUser = $this->user_model->getFromId($invite->user2_id);
    		$data['otherUser'] = $otherUser;
    		$data["currentTurn"] = 1;
    	}
    	
    	// The invited
    	else if ($user->user_status_id == User::PLAYING) {
    		$arr = json_decode($match->board_state);
    		if ($match->user1_id == $user->id) {
    			$otherUser = $this->user_model->getFromId($match->user2_id);
    			$data["userPlayerID"] = 2;
    			$data["otherPlayerID"] = 1;
    		} else {
    			$otherUser = $this->user_model->getFromId($match->user1_id);
    			$data["userPlayerID"] = 1;
    			$data["otherPlayerID"] = 2;
    		}
    		
    		// The inviter 
    		$data["currentTurn"] = 1;
    		$data['otherUser'] = $otherUser;
    		// $date['arrs'] = $arrs;
    	}
    	
    	$data['user'] = $user;

    	switch($user->user_status_id) {
    		case User::PLAYING:	
    			$data['status'] = 'playing';
    			break;
    		case User::WAITING:
    			$data['status'] = 'waiting';
    			break;
    	}
	    		
		$this->load->view('match/board', $data);
		
    }

    function makeMove() {
    	
    	$this->load->model('user_model');
    	$this->load->model('match_model');
    	
    	// Get the current user info.
     	$user = $_SESSION['user'];
 		$user = $this->user_model->getExclusive($user->login);
 		if ($user->user_status_id != User::PLAYING) {	
			$errormsg="Not in PLAYING state";
			goto error;
 		}
 		
 		if (intval($this->input->post('playerID')) != intval($this->input->post('currentPlayerTurn'))) {
 			$msg = "NOT YOUR TURN!";
 			goto waiting;
 		}
 		
 		// Get the current match info.
    	$match = $this->match_model->get($user->match_id);
    	$board_state = json_decode($match->board_state);
    	$position = $this->input->post('pieceAdded');
    	$colNum = $position[0];
    	$rowNum = $position[1];
    	
    	// Update the board with a player's move and change the current player.
    	$board_state->match_arr[$rowNum][$colNum] = $board_state->curr_player;
    	$board_state->curr_player = 3 - $board_state->curr_player;
    	$board_state->col_num = $colNum;
    	$board_state->row_num = $rowNum;

    	// start transactional mode
    	$this->db->trans_begin();
    	
    	$encoded_board_state = json_encode($board_state);
    	$this->match_model->updateBoard($user->match_id, $encoded_board_state);

    	if ($this->db->trans_status() === FALSE) {
    		$errormsg = "Transaction error";
    		goto transactionerror;
    	}
    		
    	// if all went well commit changes
    	$this->db->trans_commit();
    		
    	echo json_encode(array('status' => 'success', 'board' => $board_state->match_arr, 'curr_player' => $board_state->curr_player));
    	return;
    	
    	transactionerror:
    	$this->db->trans_rollback();
    	
    	error:
    	echo json_encode(array('status'=>'failure','message'=>$errormsg));

    	waiting:
    	echo json_encode(array('status' => 'waiting'));
    	
    	//////////////////////////////////
    	
    }
    
    function opponentMadeMove() {
    	
    	// Get the user and match info.
    	// Get teh match from the SQL database.
    	
    	// If the baord has changed:
    		// get the board
    		
    	$this->load->model('match_model');
    	$this->load->model('user_model');
    	 
    	$user = $_SESSION['user'];
    	 
    	$user = $this->user_model->get($user->login);
    	if ($user->user_status_id != User::PLAYING) {
    		$errormsg="Not in PLAYING state";
    		goto error;
    	}
    	
    	// start transactional mode
    	$this->db->trans_begin();
    	 

    	$match = $this->match_model->get($user->match_id);
    	
    	$arr = json_decode($match->board_state);
    	
    	if ($this->db->trans_status() === FALSE) {
    		$errormsg = "Transaction error";
    		goto transactionerror;
    	}
    	
    	$board_state = json_decode($match->board_state);
    	if (intval($this->input->get('userTurn')) != intval($board_state->curr_player)) {
    		$errormsg = "Not your turn";
    		goto waiting;
    	}

    	// if all went well commit changes
    	$this->db->trans_commit();
    	
    	//save data of current board and player
    	echo json_encode(array('status'=>'success', 'board' => $board_state->match_arr, 'col_num' => $board_state->col_num, 'row_num' => $board_state->row_num, 'curr_player' => $board_state->curr_player));
    	return;
    	 
    	transactionerror:
    	$this->db->trans_rollback();
    	 
    	error:
    	echo json_encode(array('status'=>'failure','message'=>$errormsg));
    	
    	waiting:
    	$this->db->trans_rollback();
    	echo json_encode(array('status' => 'waiting'));
    	
    }
      
 	function postMsg() {
 		$this->load->library('form_validation');
 		$this->form_validation->set_rules('msg', 'Message', 'required');
 		
 		if ($this->form_validation->run() == TRUE) {
 			$this->load->model('user_model');
 			$this->load->model('match_model');

 			$user = $_SESSION['user'];
 			 
 			$user = $this->user_model->getExclusive($user->login);
 			if ($user->user_status_id != User::PLAYING) {	
				$errormsg="Not in PLAYING state";
 				goto error;
 			}
 			
 			$match = $this->match_model->get($user->match_id);			
 			
 			$msg = $this->input->post('msg');
 			
 			if ($match->user1_id == $user->id)  {
 				$msg = $match->u1_msg == ''? $msg :  $match->u1_msg . "\n" . $msg;
 				$this->match_model->updateMsgU1($match->id, $msg);
 			}
 			else {
 				$msg = $match->u2_msg == ''? $msg :  $match->u2_msg . "\n" . $msg;
 				$this->match_model->updateMsgU2($match->id, $msg);
 			}
 				
 			echo json_encode(array('status'=>'success'));
 			 
 			return;
 		}
		
 		$errormsg="Missing argument";
 		
		error:
			echo json_encode(array('status'=>'failure','message'=>$errormsg));
 	}
 
	function getMsg() {
 		$this->load->model('user_model');
 		$this->load->model('match_model');
 			
 		$user = $_SESSION['user'];
 		 
 		$user = $this->user_model->get($user->login);
 		if ($user->user_status_id != User::PLAYING) {	
 			$errormsg="Not in PLAYING state";
 			goto error;
 		}
 		// start transactional mode  
 		$this->db->trans_begin();
 			
 		$match = $this->match_model->getExclusive($user->match_id);			
 			
 		if ($match->user1_id == $user->id) {
			$msg = $match->u2_msg;
 			$this->match_model->updateMsgU2($match->id,"");
 		}
 		
 		else {
 			$msg = $match->u1_msg;
 			$this->match_model->updateMsgU1($match->id,"");
 		}

 		if ($this->db->trans_status() === FALSE) {
 			$errormsg = "Transaction error";
 			goto transactionerror;
 		}
 		
 		// if all went well commit changes
 		$this->db->trans_commit();
 		
 		echo json_encode(array('status'=>'success','message'=>$msg));
		return;
		
		transactionerror:
		$this->db->trans_rollback();
		
		error:
		echo json_encode(array('status'=>'failure','message'=>$errormsg));
 	}
 }
?>
