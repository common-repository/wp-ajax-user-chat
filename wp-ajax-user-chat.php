<?php
/**
 * @package Wp Ajax Chat
 * @version 1.2
 */
/*
Plugin Name: Wp Ajax User Chat
Plugin URI: http://www.ronakdave.in/
Description: Wp Ajax User Chat is a plugin for user to user chatting.
Author: Ronak Dave
Version: 1.2
Author URI: http://www.ronakdave.in/
License: GPLv2
*/
function wp_ajax_chat_admin_menu() 
{ 
	$page = add_menu_page(
		"Wordpress Chat Plugin",
		"Wp Ajax Chat",
		8,
		__FILE__,
		"wp_ajax_chat_admin_menu_list"
	); 
}
add_action('admin_menu','wp_ajax_chat_admin_menu');
function wp_ajax_chat_admin_menu_list()
{
	//Back End Page ?>
	<div class="wrap">
    	<h1>Wp user to user live chat plugin</h1>
        <p>First ever user to user chat plugin for wordpress.</p>
        <p>Please share this plugin if you liked it </p>
        <p><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A//wordpress.org/plugins/wp-ajax-user-chat/">Share on Facebook</a></p>
        <p><a target="_blank" href="https://twitter.com/home?status=https%3A//wordpress.org/plugins/wp-ajax-user-chat/">Share on Twitter</a></p>
        <p><a target="_blank" href="https://plus.google.com/share?url=https%3A//wordpress.org/plugins/wp-ajax-user-chat/">Share on Google+</a></p>
        <p><a target="_blank" href="https://www.linkedin.com/shareArticle?mini=true&url=https%3A//wordpress.org/plugins/wp-ajax-user-chat/&title=Wordpress%20User%20Chat%20Plugin&summary=This%20is%20a%20user%20to%20user%20chat%20plugin%20for%20WordPress.&source=">Share on LinkedIn</a></p>
        <p><a target="_blank" href="http://ronakdave.in/donate">Donate to this plugin!</a> :- If it helped you in any case and saved some of your bucks :).</p>
        <p><a target="_blank" href="https://wordpress.org/support/view/plugin-reviews/wp-ajax-user-chat?rate=5#postform">Click here and rate this plugin if you liked it..</a></p>
    </div>
    <?php
}
function wp_ajax_chat_scripts_basic(){	
	wp_register_style( 'jquicss', plugins_url('css/jquery-ui.min.css', __FILE__) );
	wp_register_style( 'chatcss', plugins_url('css/jquery.ui.chatbox.css', __FILE__) );
	wp_enqueue_style( 'jquicss' );   
	wp_enqueue_style( 'chatcss' );  
}
function wp_ajax_chat_footer(){
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-widget' );
	wp_enqueue_script( 'jquery-effects-core' );		
	wp_enqueue_script( 'jquery-effects-bounce' );
	wp_enqueue_script( 'jquery-effects-highlight' );
	wp_register_script( 'chatbox', plugins_url( 'js/jquery.ui.chatbox.js' , __FILE__ ) );
	wp_enqueue_script( 'chatbox' );
	wp_register_script( 'chatManager', plugins_url( 'js/chatboxManager.js' , __FILE__ ) ); 
	wp_enqueue_script( 'chatManager' );
	echo do_shortcode('[wp_ajax_chat]');
	wp_register_script( 'chat', plugins_url( 'js/chat.js' , __FILE__ ) );
	wp_enqueue_script( 'chat' );
}

	add_action('wp_footer','wp_ajax_chat_footer');
	add_action( 'wp_enqueue_scripts', 'wp_ajax_chat_scripts_basic' ); 

	// enqueue and localise scripts
	wp_enqueue_script( 'my-ajax-handle', plugin_dir_url( __FILE__ ) . 'js/ajax.js', array( 'jquery' ) );
	wp_localize_script( 'my-ajax-handle', 'the_ajax_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

	// THE AJAX ADD ACTIONS
	add_action( 'wp_ajax_the_ajax_hook', 'the_action_functions' );
	add_action( 'wp_ajax_nopriv_the_ajax_hook', 'the_action_functions' ); 

function the_action_functions(){
	$name = $_POST['user'];
	$chat = $_POST['chat'];
	$chat = stripslashes($chat);
	$uid = $_POST['uid'];
	$method = $_POST['method'];
	global $wpdb;
	$table = $wpdb->prefix . "user_chat";
	if($method == 'submitchat'){
	 $wpdb->insert( 
			$table, 
			array( 
				'from' => $name, 
				'to' => $uid,
				'text' => $chat,
				'flag' => 0
			)
		);
	}
	if($method == 'getusers'){
		$blogusers1 = get_users();
		$current_user = wp_get_current_user();
		//$cuser = $current_user->ID;
		$cusern1 = $current_user->user_login;
		$allusers = "";
			foreach($blogusers1 as $user) {
				if($cusern1 == $user->user_login){
					
				}else{
					$ouser = get_option($user->user_login, 'logout');
					if($ouser == 'login'){
						$allusers .= '<a rel="'.$user->user_login.'" class="chat" id="'.$user->ID.'" href="#">' . $user->user_login . ' (Online)</a>';		
					}else{
						//$allusers .= '<a rel="'.$user->user_login.'" class="chat offline" id="'.$user->ID.'" href="#">' . $user->user_login . ' (Offline)</a>';		
					}
				}
			}	
		echo $allusers;
		}
	if($method == 'getchat'){
	 	$sqlq = "SELECT *
			FROM $table
			WHERE `to` = '$uid'
			AND `flag` =0
			LIMIT 1";
	 	$record = $wpdb->get_results($sqlq);
		$outarray = array();
		$outarray['fromuser'] = $record[0]->from;
		$outarray['text'] = $record[0]->text;
		if($outarray['fromuser'] == "" || $outarray['fromuser'] == null){
			//do nothing;
			}else{
				$wpdb->delete( 
					$table, 
					array( 
					'ID' => $record[0]->ID 
					)
				); 
				echo json_encode($outarray);
			}
		
	}
 die();// wordpress may print out a spurious zero without this - can be particularly bad if using json
}
 // ADD EG A FORM TO THE PAGE
function wp_ajax_chat_frontend(){
	global $wpdb;
	echo "<div id='wpchat'>";
	if ( is_user_logged_in() ) {
	 	$blogusers = get_users();
		$current_user = wp_get_current_user();
		$cuser = $current_user->ID;
		$cusern = $current_user->user_login;
		$countu = count($blogusers);
		if($countu > 1){
			echo "<div id='wpchatusers'><h3 id='wpchatuserstitle'>Site Users<span class='mini'></span></h3>";
			echo "<div id='allusers'>";
			foreach($blogusers as $user) {
				if($cusern == $user->user_login){
					
				}else{
					$ouser = get_option($user->user_login, 'logout');
					if($ouser == 'login'){
						echo '<a rel="'.$user->user_login.'" class="chat" id="'.$user->ID.'" href="#">' . $user->user_login . ' (Online)</a>';		
					}else{
						echo '<a rel="'.$user->user_login.'" class="chat offline" id="'.$user->ID.'" href="#">' . $user->user_login . ' (Offline)</a>';		
					}
				}
		    }	
			echo "</div>";
			echo "</div>";
		}else{
			echo "<div id='wpchatnousers'>Oops! There are no users to chat with :( . get them registered.</div>";
		}
		echo '<div rel="'.$cusern.'" class="currentuser" id="'.$cuser.'"></div>';
	}else{
		echo "<div id='nologin'>Login to chat with other users!</div>";
	}
	echo "</div>";
}
add_shortcode("wp_ajax_chat", "wp_ajax_chat_frontend"); 
function login_function() {
	update_option( $_POST['log'], 'login' );
}
add_action('wp_login', 'login_function');
function logout_function() {
	$current_user = wp_get_current_user();
	update_option( $current_user->user_login, 'logout' );
}
add_action('wp_logout', 'logout_function');

function wp_ajax_chat_activate() {
    global $wpdb;
	$table_name = $wpdb->prefix . "user_chat";
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
	    $sql = "CREATE TABLE $table_name (
		  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
		  `from` VARCHAR(20) NOT NULL,
		  `to` VARCHAR(20) NOT NULL,
		  `text` text NOT NULL,
		  `flag` int(20) NOT NULL,
		  PRIMARY KEY (`ID`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
	    //reference to upgrade.php file
	    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	    dbDelta( $sql );
	}
	$current_userrr = wp_get_current_user();
	update_option( $current_userrr->user_login, 'login' );
}
register_activation_hook( __FILE__, 'wp_ajax_chat_activate' );
function wp_ajax_chat_remove_database() {
     global $wpdb;
     $table_name = $wpdb->prefix . "user_chat";
     $sql = "DROP TABLE IF EXISTS $table_name;";
     $wpdb->query($sql);
}
register_deactivation_hook( __FILE__, 'wp_ajax_chat_remove_database' );
?>