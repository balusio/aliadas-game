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

    function __construct(){
        global $wpdb;
        $this->db = $wpdb;
        
    }

	var $version = '1.0';


	function initialize(){
		
		$table_name= $this->db->prefix.'gameScore';
		
		if($this->db->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		     //table not in database. Create new table
		     $charset_collate = $this->db->get_charset_collate();
		 
		     $sql = "CREATE TABLE $table_name (
		          id mediumint(9) NOT NULL AUTO_INCREMENT,
		          user_id mediumint(9) NOT NULL,
		          user_score mediumint(9) NOT NULL,
		          UNIQUE KEY id (id)
		     ) $charset_collate;";
		     require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		     dbDelta( $sql );
		}
	}

	public static function checkUserState($user_id){
		
		$table_name= $this->db->prefix.'gameScore';

		$current_user = $this->db->get_row( "SELECT * FROM $table_name WHERE user_id = $user_id" );
		return $current_user;
		/*echo 'current_user is equal to =' .$user_id;*/

	}
}

function game() {

	global $aliadasGame;
	
	if( !isset($aliadasGame) ) {
	
		$aliadasGame = new aliadasGame();
		
		$aliadasGame->initialize();

/*		//CHECK USER TO REGISTER AND SET POINTS
		if ( ! function_exists( 'chekUserSetted' ) ) {

		    function chekUserSetted($user_id) {
		       return $aliadasGame::checkUserState($user_id);

		    }
		}*/

		
	}
	
	return $aliadasGame;
	
}


// initialize
game();


endif; // class_exists check

?>