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
    function win_state($win){
    	//save to database here
    }
    
    function index() {
		$user = $_SESSION['user'];
    	$this->load->model('user_model');
    	$this->load->model('invite_model');
    	$this->load->model('match_model');
    	
    	$user = $this->user_model->get($user->login);

    	$invite = $this->invite_model->get($user->invite_id);
    	
    	// The inviter
    	if ($user->user_status_id == User::WAITING) {
    		$invite = $this->invite_model->get($user->invite_id);
    		$otherUser = $this->user_model->getFromId($invite->user2_id);
    		$data['otherUser'] = $otherUser;
    		
    		// The inviter
    		$data["currentTurn"] = 1;
    		$data['otherUser'] = $otherUser;
    	}
    	
    	// The invited
    	else if ($user->user_status_id == User::PLAYING) {
    		$match = $this->match_model->get($user->match_id);
    		
<<<<<<< HEAD
    		$arr = json_decode($match->board_state);
   			$arrs = array();
   			$match = $this->match_model->get($user->match_id);
   			 
   			$arr = json_decode($match->board_state);
=======
//     		$arr = json_decode($match->board_state);
//    			$arrs = array();
//     		try
//     		{
//     			foreach ($arr as $arrElem) {
//     				//place holder
//     				if (gettype($arrElem) != gettype(2)){
//     					foreach ($arrElem as $arrElemElem) {
//     						echo gettype($arrElemElem);
//     						foreach ($arrElemElem as $arrElemElemElem){
//     							echo $arrElemElemElem;
// 								array_push($arrs, $arrElemElemElem);
//     						}
//     					}
//     					//echo "<br/>";
//     				}
//     			};
//     			// Get the current match info.
//     		}
//     		catch (Exception $e)
//     		{
//     			throw new Exception( 'Something really gone wrong', 0, $e);
//     		}
>>>>>>> 5573fab726b1b4e34efb480d8a424a85c4fd409d

   			try
   			{
   				foreach ($arr as $arrElem) {
   					//place holder
   					if (gettype($arrElem) != gettype(2)){
   						foreach ($arrElem as $arrElemElem) {
   							//echo gettype($arrElemElem);
   							foreach ($arrElemElem as $arrElemElemElem){
   								//echo $arrElemElemElem;
   								array_push($arrs, $arrElemElemElem);
   							}
   						}
   						//echo "<br/>";
   					}
   				};
   				// Get the current match info.
   			}
   			catch (Exception $e)
   			{
   				throw new Exception( 'Something really gone wrong', 0, $e);
   			}
   			$win = 0;
   			$count = 0;
   			$count2 = 0;
   			for ($x=0;$x<sizeof($arrs);$x++){
   				for ($y = 0;$y<4;$y++){
   					$count = $count +1;
   					if ($arrs[$x+$y] == $arr[$x+1+$y]){
   						$win = $arrs[$x+$y];
   					}
   					else{
   						$win = 0;
   						$count = $y;
   						break;
   					}
   					if ($count == 6){
   						$count = 0;
   						$win = 0;
   						break;
   					}
   					if ($count == 4){
   						if ($win != 0){
   							win_state($win);
   						}
   						$win = 0;
   						break;
   					}
   				}
   				//up/down
   				try
   				{	
   					for ($y = 0;$y<4;$y++){
   						$count = $count +1;
   						if ($arrs[$x+($y*7)+$y] == $arr[$x-1-$y+((1+$y)*7)]){
   							$win = $arrs[$x+($y*7)];
   						}
   						else{
   							$win = 0;
   							$count = $y;
   							break;
   						}
   						if ($count == 6){
   							$count = 0;
   							$win = 0;
   							break;
   						}
   						if ($count == 4){
   							if ($win != 0){
   								win_state($win);
   							}
   							$win = 0;
   							break;
   						}
   					}
   				}
   				catch (Exception $e)
   				{
   					$win = 0;
   				}
   				//diaL
   				for ($y = 0;$y<4;$y++){
   					$count = $count +1;
   					if ($arrs[$x+($y*7)-$y] == $arr[1+$y+$x+((1+$y)*7)]){
   						$win = $arrs[$x+($y*7)];
   					}
   					else{
   						$win = 0;
   						$count = $y;
   						break;
   					}
   					if ($count == 6){
   						$count = 0;
   						$win = 0;
   						break;
   					}
   					if ($count == 4){
   						if ($win != 0){
   							win_state($win);
   						}
   						$win = 0;
   						break;
   					}
   				}
   				//diaR
   				for ($y = 0;$y<4;$y++){
   					$count = $count +1;
   					if ($arrs[$x+($y*7)] == $arr[$x+((1+$y)*7)-1]){
   						$win = $arrs[$x+($y*7)];
   					}
   					else{
   						$win = 0;
   						$count = $y;
   						break;
   					}
   					if ($count == 6){
   						$count = 0;
   						$win = 0;
   						break;
   					}
   					if ($count == 4){
   						if ($win != 0){
   							win_state($win);
   						}
   						$win = 0;
   						break;
   					}
   				}
   					
   			} 
   			
   			
			
    		//$loowestSlots = getLowestRowInColumn(2);
    		// Insert a token into the selected column, if there is room.
    		//lowestSlots.addClass('player' + 1).removeClass('emptySlot');
    		// Determine who the other user is.
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
<<<<<<< HEAD
=======
    		// $date['arrs'] = $arrs;
>>>>>>> 5573fab726b1b4e34efb480d8a424a85c4fd409d
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
 		
 		// Get the current match info.
    	$match = $this->match_model->get($user->match_id);
    	$board_state = json_decode($match->board_state);
    	$position = $this->input->post('pieceAdded');
    	
    	$colNum = $position[0];
    	$rowNum = $position[1];
    	//echo print_r($board_state);
    	
    	// Update the board with a player's move and change the current player.
    	$board_state->match_arr[$rowNum][$colNum] = $board_state->curr_player;
    	$board_state->curr_player = 3 - $board_state->curr_player;
    	$board_state->col_num = $colNum;
    	$board_state->row_num = $rowNum;
    	
    	
    	$board_state = json_encode($board_state);
    	
    	// start transactional mode
    	$this->db->trans_begin();
    	
    	$this->match_model->updateBoard($user->match_id, $board_state);

    	if ($this->db->trans_status() === FALSE) {
    		$errormsg = "Transaction error";
    		goto transactionerror;
    	}
    		
    	// if all went well commit changes
    	$this->db->trans_commit();
    		
    	echo json_encode(array('status'=>'success'));
    	return;
    	
    	transactionerror:
    	$this->db->trans_rollback();
    	
    	error:
    	echo json_encode(array('status'=>'failure','message'=>$errormsg));

    	
    	//////////////////////////////////
    	
    	
    }
    
    function opponentMadeMove() {
    	
    	// Get the user and match info.
    	// Get teh match from the SQL database.
    	
    	// If the baord has changed:
    		// get the board
    	
    	// Otherwise
    		// .....
    		
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
<<<<<<< HEAD
    	
    	$arr = json_decode($match->board_state);
    	
    	$arrs = array();
    	try
    	{
    		foreach ($arr as $arrElem) {
    			//place holder
    			if (gettype($arrElem) != gettype(2)){
    				foreach ($arrElem as $arrElemElem) {
    					echo gettype($arrElemElem);
    					foreach ($arrElemElem as $arrElemElemElem){
    						echo $arrElemElemElem;
    						array_push($arrs, $arrElemElemElem);
    					}
    				}
    				//echo "<br/>";
    			}
    		};
    		// Get the current match info.
    	}
    	catch (Exception $e)
    	{
    		throw new Exception( 'Something really gone wrong', 0, $e);
    	}
		$data['arrs'] = $arrs;
=======
>>>>>>> 5573fab726b1b4e34efb480d8a424a85c4fd409d
    	
    	if ($this->db->trans_status() === FALSE) {
    		$errormsg = "Transaction error";
    		goto transactionerror;
    	}
    	
    	$board_state = json_decode($match->board_state);

    	// if all went well commit changes
    	$this->db->trans_commit();
    	
    	
    	echo json_encode(array('status'=>'success', 'col_num' => $board_state->col_num, 'row_num' => $board_state->row_num));
    	return;
    	 
    	transactionerror:
    	$this->db->trans_rollback();
    	 
    	error:
    	echo json_encode(array('status'=>'failure','message'=>$errormsg));
    	
    	///////////////
    	//echo json_encode(array('status'=>'success'));
    	//return;
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
