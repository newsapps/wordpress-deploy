<?php 
/*****
 * Wordpress automated setup
 * 
 * scripts/na-postinstall.php
 * Post install runs after Wordpress is all setup and ready 
 * to go. It configures the root blog and network.
*****/

if ($_SERVER['SERVER_ADDR'] != $_SERVER['REMOTE_ADDR']) exit();

/** Load WordPress Bootstrap */
require_once( dirname( __FILE__ ) . '/../wp-load.php' );

/** Load wpdb */
require_once( dirname(__FILE__) . '/../wp-includes/wp-db.php');

// Load automated install settings
require_once("na-options.php");

// Set default blog options
foreach ($options as $key=>$val) {
	update_option($key, $val);
}
print("Default blog options set\n");

// Set network options
foreach ($site_options as $key => $val)
	update_site_option($key, $val);

print("Default network options set\n");

// Build a menu for the root blog pages
$menu_id = wp_create_nav_menu( 'Pages', array( 'slug' => 'pages' ) );

// Assign it to the 'pages' menu theme location
$theme = get_current_theme();
$mods = get_option( 'mods_' . $theme );
$mods['nav_menu_locations']['pages'] = $menu_id;
update_option( 'mods_' . $theme, $mods );

// Add each page to the root blog then add the page to the 'pages' menu
if ($posts) {
    foreach ($posts as $post) {
        $post_id = wp_insert_post($post);
        if ($post_id == 0)
            print("Error adding ".$post['post_title']."\n");
        else {
            print("Added ".$post['post_title']."\n");
            $mitem_id = wp_update_nav_menu_item(
              $menu_id,
              0,
              array(
                 'menu-item-title' => $post['post_title'],
                 'menu-item-type' => 'post_type',
                 'menu-item-object' => 'post',
                 'menu-item-object-id' => $post_id,
                 'menu-item-position' => 1,
                 'menu-item-status' => 'publish')
              );
            wp_set_object_terms( $mitem_id, (int) $menu_id, 'nav_menu' );
            print("Added to menu\n");
        } 
    }

    print("Added some posts\n");
}

?>
