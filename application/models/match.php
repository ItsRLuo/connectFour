<?php
class Match  {
	const ACTIVE = 1;
	const U1WON = 2;
	const U2WON = 3;
	
	public $id;
	
	public $user1_id;  
	public $user2_id;
	
	public $match_status_id = self::ACTIVE;
		
	public $board_state;
	
	
	public $match_arr = array();
	  
	function __construct() {
		
// 		for ($i = 0; $i < 7; $i++) {
// 			array_push($match_arr, array());
// 			for ($j = 0; $j < 6; $j++) {
// 				array_push($match_arr[$i], "");
// 			}
// 		}
// 		$board_state = serialize($match_arr);
		
	}
	

	
	
	


	
}