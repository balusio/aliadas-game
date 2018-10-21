<?php
/*
Plugin Name: Aliadas Game
Plugin URI: https://github.com/balusio/aliadas-game
Description: Game Aliadas 1.3
Author: Jorge Manzano
Author URI: http://balu.ninja/
Copyright: MIT LICENSE
Text Domain: aliadas
*/

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('aliadasGame') ) :
	
	// vars
class aliadasGame {

  	public static $db;
    function __construct(){
        global $wpdb;
        aliadasGame::$db = $wpdb;
        
    }

	var $version = '1.3';


	function initialize(){
		
		$table_name= aliadasGame::$db->prefix.'gameScore';
		
		if(aliadasGame::$db->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		     //table not in database. Create new table
		     $charset_collate = aliadasGame::$db->get_charset_collate();
		 
		     $sql = "CREATE TABLE $table_name (
		          id mediumint(9) NOT NULL AUTO_INCREMENT,
		          user_id mediumint(9) NOT NULL,
		          user_score mediumint(9) NOT NULL,
		          user_rankposs mediumint(9),
		          user_start datetime,
		          user_end datetime DEFAULT NULL,
		          UNIQUE KEY id (id)
		     ) $charset_collate;";
		     require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		     dbDelta( $sql );
		}
	}


	public static function checkUserState($user_id){
		$table_name= aliadasGame::$db->prefix.'gameScore';
		$current_user = aliadasGame::$db->get_row( "SELECT * FROM $table_name WHERE user_id = $user_id" );
		$timeStart = date("Y-m-d H:i:s");

		if($current_user){
			
			$updateScore = aliadasGame::$db->query( "UPDATE $table_name set user_start = '". $timeStart ."', user_end = NULL WHERE user_id = " . $user_id );
		}
		else{
			//REGISTER GAME
			
			$added_user = aliadasGame::$db->insert($table_name, array(
			    'user_id' => $user_id,
			    'user_score' => 200,
			    'user_start' => $timeStart
			));

			echo $added_user . "AGREGADO EL USUARIO";


		}

	}

	public static function updateUserScore($user_id){

		$table_name= aliadasGame::$db->prefix.'gameScore';
		$timePassed = date("Y-m-d H:i:s");
		$score= 0;
		$updateScore = aliadasGame::$db->query( "UPDATE $table_name set user_end = '". $timePassed ."' WHERE user_id = " . $user_id );
		
		/*print_r($updateScore);*/
	}

	public static function checkExchange($user_id,$price,$title){
		
		$int_price = (int)$price;
		$table_name= aliadasGame::$db->prefix.'gameScore';
		$upadtePrice = aliadasGame::$db->query( "UPDATE $table_name set 
			user_score = IF(user_score >= ". $int_price . ", user_score - ". $int_price . ",user_score)
			WHERE user_id = " . $user_id );

		if($upadtePrice && !empty($user_id)){

			return true;
		}
		else{
			return false;
		}

	}
	
	public static function monthlyUpdate(){
		$values = [1000,800,600,500,400,300,200,100,100,100];
		$table_name= aliadasGame::$db->prefix.'gameScore';
		$lasts_users = aliadasGame::$db->get_results( "SELECT user_id, user_score, TIMESTAMPDIFF(SECOND,user_start,user_end) as TIMEDOWN FROM $table_name WHERE user_end IS NOT NULL ORDER BY TIMEDOWN ASC LIMIT 10");	
		
		$i = 0;
		foreach ($lasts_users as $current_user) {	

			$updateScore = aliadasGame::$db->query( "UPDATE $table_name set user_score = user_score + ". $values[$i] . " WHERE user_id = " . $current_user->user_id);

			$i++;
		}
		echo "string";

	}

}

function game() {

	global $aliadasGame;
	
	if( !isset($aliadasGame) ) {
	
		$aliadasGame = new aliadasGame();
		
		$aliadasGame->initialize();
		
	}
	
	return $aliadasGame;
	
}


// initialize
game();


endif; // class_exists check

?>