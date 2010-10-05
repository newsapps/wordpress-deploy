<?php
/*****
 * Wordpress automated setup
 * 
 * scripts/na-options.php
 * This is where all the settings for the automated setup are defined.
*****/

// Wordpress install
$weblog_title = "My automatic blog";
$user_name = "admin";
$admin_email = "me@example.com";
$public = 1;
$admin_password = "password";

// Wordpress network install
$network_id = 1;
$hostname = $_SERVER['SERVER_NAME'];
$network_title = "My automatic blog network";
$base = '/';
$subdomain_install = false;

// In this array, setup any options you want set on every 
// blog, including the root blog.
$options = array(
	// Akismet
	"wordpress_api_key"		=> "",
	// Active theme
	'template'				=> 'twentyten',
	'stylesheet'			=> 'twentyten',
	'current_theme'			=> 'Twenty Ten',
	'timezone_string'		=> 'America/Chicago',
	'use_trackback'			=> '1',
	'comment_registration'	=> '0',
	'comment_whitelist'		=> '',
	'category_base'			=> '/category',
	'tag_base'				=> '/tag',
	'default_ping_status'	=> 1,
	'comments_notify'		=> 1
);

// Prepare Network settings

// Some settings are serialized and saved in the database. Arrays seem
// to be serialized differently depending on how they are defined. So 
// do it this way or else stuff will break.

// set the allowed theme for all sites
$allowed_themes = array();
$allowed_themes["twentyten"]=true;

// enable the add image button for network sites
$mu_media_buttons = array();
$mu_media_buttons['image'] = '1';

// Network settings
$site_options = array(
	// Default network theme
	'allowedthemes' => $allowed_themes,
	
	// Network settings
	'dashboard_blog' => 1,
	// 'admin_notice_feed' => 'http://'.$hostname.'/feed/',
	'registrationnotification' => 'no',
	'add_new_users' => 1,
	'registration' => 'user',
	'welcome_user_email' => "Dear User,\n\nYour new account is set up.\n\nYou can log in with the following information:\n\tUsername: USERNAME\n\tPassword: PASSWORD\nLOGINLINK\n\nThanks!\n\n--The Team @ SITE_NAME",
	'mu_media_buttons' => $mu_media_buttons,
	'upload_space_check_disabled' => 1,
	
	// Set max upload file size
	'fileupload_maxk' => '10000',
);

// Define network blogs to create. The keys 'slug' and 'name' are required for each.
// You can also define blog specific options by adding more 'key'=>'value' pairs.
$sites = array(
	array(
        'slug' => 'rabbits', // for the url for this blog. e.g. slug.example.com or example.com/slug
        'name' => 'My blog about rabbits' // this is the name for this blog to be used everywhere
    ),
    array(
        'slug' => 'frogs',
        'name' => 'My blog about frogs'
    )
);

// Posts to add automatically.
// For the root blog only.
$posts = array(
	array( // FAQ
		'post_type' => 'page',
		'post_author' => 1,
		'post_status' => 'publish',
		'post_title' => 'F.A.Q.',
		'post_name' => 'faq',
		'post_content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'
	),
	array( // Contact us
		'post_type' => 'page',
		'post_author' => 1,
		'post_status' => 'publish',
		'post_title' => 'Contact us',
		'post_name' => 'contact-us',
		'post_content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'
	),
	array( // Help
		'post_type' => 'page',
		'post_author' => 1,
		'post_status' => 'publish',
		'post_title' => 'Help',
		'post_name' => 'help',
		'post_content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'
	)
);

// Categories to add to each blog.
// For child blogs only.
$categories = array(
	array(
        'cat_name' => 'Sports',
        'category_nicename' => 'sports',
        'category_description' => '',
    ),
	array(
        'cat_name' => 'News', 
        'category_nicename' => 'news', 
        'category_description' => ''
    ),
	array( // replaces Uncategorized 
        'cat_ID' => 1, 
        'cat_name' => 'Other', 
        'category_nicename' => 'other', 
        'category_description' => ''
    ) 
);

// Create a menu on each blog and add these categories as items.
// For child blogs only.
$menu_categories = array(
	'News',
	'Sports'
);

// Create these users in all blogs.
$users = array(
	array('alissaswango','Alissa Swango','aswango@tribune.com','',NULL,'administrator'),

);

?>
