<?php
/*****
 * Wordpress automated setup
 * 
 * scripts/na-setup-plugins.php
 * Activates plugins for this Wordpress install. Not all plugins like
 * getting activated this way (buddypress).
*****/

if ($_SERVER['SERVER_ADDR'] != $_SERVER['REMOTE_ADDR']) exit();

define('WP_ADMIN', TRUE);

/** Load WordPress Bootstrap */
require_once( dirname( __FILE__ ) . '/../wp-load.php' );

/** Load WordPress Administration Upgrade API */
require_once( dirname( __FILE__ ) . '/../wp-admin/includes/upgrade.php' );

/** Load wpdb */
require_once( dirname(__FILE__) . '/../wp-includes/wp-db.php');

global $blog_id;
print( "Activating Plugins...\n" );

//plugin activation doesn't fail, but doesn't work. Somethings missing here.

//lets turn on plugins, network-wide
$network_plugins = array(
	"akismet/akismet.php",
);

// turn on these plugins for just the root blog
$site_plugins = array( 
'' );

require_once("../wp-admin/includes/plugin.php");

foreach( $network_plugins as $plugin ) {
	
	echo "Activating network " . $plugin . "...   ";
	$result = activate_plugin( $plugin, '', true );
	
	if ( is_wp_error( $result ) ) {
		foreach ( $result->get_error_messages() as $err )
			print("FAILED: {$err}\n");
	} else {
		print("Activated\n");
	}
	
}

foreach( $site_plugins as $plugin ) {
	
	echo "Activating " . $plugin . "...   ";
	$result = activate_plugin( $plugin );
	
	if ( is_wp_error( $result ) ) {
		foreach ( $result->get_error_messages() as $err )
			print("FAILED: {$err}\n");
	} else {
		print("Activated\n");
	}
	
}

do_action( 'plugins_loaded' );

print("Success\n");


?>
