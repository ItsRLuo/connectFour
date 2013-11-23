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
	  
	function __construct() {
		
// // 		$match_arr = array(1, 3, 4, 2);
// // 		for ($i = 0; $i < 7; $i++) {
// // 			array_push($match_arr, array());
// // 			for ($j = 0; $j < 6; $j++) {
// // 				array_push($match_arr[$i], "N");
// // 			}
// // 		}
		$board_state = "ssss";
		
	}
	

	
	
	


	
}
?>