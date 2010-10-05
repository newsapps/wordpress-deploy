<?php
/*****
 * Wordpress automated setup
 * 
 * scripts/na-createblog.php
 * Creates a network blog on this Wordpress install. The new blog is configured
 * with settings from na-options. This script only creates one blog at a time.
 * Request this script with a 'new_blog_index' get param to create a blog with
 * the associated settings in the $sites array.
*****/

if ($_SERVER['SERVER_ADDR'] != $_SERVER['REMOTE_ADDR']) exit();

/** Load WordPress Bootstrap */
require_once( dirname( __FILE__ ) . '/../wp-load.php' );

/** Load wpdb */
require_once( dirname(__FILE__) . '/../wp-includes/wp-db.php');

// set default blog options
require_once("na-options.php");
$options["public"] = 1;

// Get Wordpress Network stuff
require_once("../wp-includes/ms-load.php");
require_once("../wp-includes/ms-functions.php");
require_once("../wp-includes/ms-blogs.php");

if (count($sites) <= (int) $_GET['new_blog_index'])
	die("No more sites defined");

$site = $sites[(int) $_GET['new_blog_index']];

$wpdb->hide_errors();
if ($subdomain_install) {
	$id = wpmu_create_blog($site['slug'].".".$hostname, "", $site['name'], 1, $options, 1);
} else {
	$id = wpmu_create_blog($hostname, "/".$site['slug'], $site['name'], 1, $options, 1);
}
$wpdb->show_errors();



if (!is_wp_error( $id )) {
	//doing a normal flush rules will not work, just delete the rewrites
	switch_to_blog( $id );

	// we delete the rewrites because flushing them does not work if the originally
	// loaded blog is the main one, deleteing them will force a propper flush on that site's first page.
	delete_option( 'rewrite_rules' );

	// Delete the first post
	wp_delete_post( 1, true );

	// Delete the about page
	wp_delete_post( 2, true );

	//Configure categories
	wp_delete_category(1);
	$inserted_cats = array();
	foreach ($categories as $cat) {
		$cat_id = wp_insert_category( $cat );
		$inserted_cats[$cat_id] = $cat;
	}

	// Create the Section Menu
	$menu_id = wp_create_nav_menu( 'Main', array( 'slug' => 'main' ) );

	// Assign it to the sections theme location
	$theme = get_current_theme();
	$mods = get_option( 'mods_' . $theme );
	$mods['nav_menu_locations']['sections'] = $menu_id;
	update_option( 'mods_' . $theme, $mods );

	// Create the menu items
	foreach ( $inserted_cats as $cat_id => $cat ) :

		if ( in_array( $cat['cat_name'], $section_nav ) ) :

			$menu_item_id = wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title' => $cat['cat_name'],
					'menu-item-type' => 'taxonomy',
					'menu-item-object' => 'category',
					'menu-item-object-id' => $cat_id,
					'menu-item-position' => 1,
					'menu-item-status' => 'publish'
				)
			);

			wp_set_object_terms( $menu_item_id, (int) $menu_id, 'nav_menu' );

		endif;

	endforeach;

    // flush rewrite rules
	delete_option( 'rewrite_rules' );

	print("Success - ".$site['name']." setup");
} else {
	die($id->get_error_message());
}

?>
