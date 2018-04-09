<?php
/*
Plugin Name: Aliadas Game
Plugin URI: https://www.advancedcustomfields.com/
Description: game aliadas 1.0
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

	var $version = '1.0';


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
		          user_time varchar(255),
		          UNIQUE KEY id (id)
		     ) $charset_collate;";
		     require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		     dbDelta( $sql );
		}
	}


	public static function checkUserState($user_id){
		$table_name= aliadasGame::$db->prefix.'gameScore';
		$current_user = aliadasGame::$db->get_row( "SELECT * FROM $table_name WHERE user_id = $user_id" );
		
		if($current_user){
			print_r($current_user);
		}
		else{
			//REGISTER GAME
			$added_user = aliadasGame::$db->insert($table_name, array(
			    'user_id' => $user_id,
			    'user_score' => 200
			));

			echo $added_user . "AGREGADO EL USUARIO";


		}

	}

	public static function updateUserScore($user_id,$timePassed){

		$table_name= aliadasGame::$db->prefix.'gameScore';
		$score= 350;
		$upadteScore = aliadasGame::$db->query( "UPDATE $table_name set user_score = user_score + ". $score . ", user_time = '". $timePassed ."' WHERE user_id = " . $user_id );
		print_r($upadteScore);
	}

	public static function checkExchange($user_id,$price){
		
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