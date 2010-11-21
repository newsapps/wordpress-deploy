<?php
/*****
 * Wordpress automated setup
 * 
 * scripts/na-install.php
 * Runs the inital wordpress setup. Assumes that there is a wp-config.php
 * file with correct db credentials for this install.
*****/

if ($_SERVER['SERVER_ADDR'] != $_SERVER['REMOTE_ADDR']) exit();

require('na-options.php');

define( 'WP_INSTALLING', true );
define('WP_ADMIN', TRUE);

define( 'WP_SITEURL', "http://${hostname}");


/** Load WordPress Bootstrap */
require_once( dirname( __FILE__ ) . '/../wp-load.php' );

/** Load WordPress Administration Upgrade API */
require_once( dirname( __FILE__ ) . '/../wp-admin/includes/upgrade.php' );

/** Load wpdb */
require_once( dirname(__FILE__) . '/../wp-includes/wp-db.php');

$wp_install_result = wp_install(
						$weblog_title,
						$user_name,
						$admin_email,
						$public,
						'',
						$admin_password
					);

if (is_wp_error( $wp_install_result )) {
	var_dump($wp_install_result);
	die("Wordpress install failed");
}

// Delete the first post
wp_delete_post( 1, true );

// Delete the default about page
wp_delete_post( 2, true );

print("Wordpress install finished\n");

// We need to create references to ms global tables to enable Network.
foreach ( $wpdb->tables( 'ms_global' ) as $table => $prefixed_table )
	$wpdb->$table = $prefixed_table;

install_network();

$ms_install_result = populate_network(
						$network_id,
						$hostname,
						$admin_email,
						$network_title,
						$base,
						$subdomain_install
					);

if (is_wp_error( $ms_install_result ) && $ms_install_result->get_error_code() != 'no_wildcard_dns') {
    print($ms_install_result->get_error_message() . "\n");
	die("Network setup failed");
}

print("Network setup finished\n");

// lets write some files

// Write the .htaccess file
$htaccess_file = 'RewriteEngine On
RewriteBase ' . $base . '
RewriteRule ^index\.php$ - [L]

# uploaded files
RewriteRule ^' . ( $subdomain_install ? '' : '([_0-9a-zA-Z-]+/)?' ) . 'files/(.+) wp-includes/ms-files.php?file=$' . ( $subdomain_install ? 1 : 2 ) . ' [L]' . "

RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]
RewriteRule  ^([_0-9a-zA-Z-]+/)?(wp-(content|admin|includes).*) $2 [L]
RewriteRule ^(.*/)?sitemap.xml wp-content/sitemap.php [L]
RewriteRule  ^([_0-9a-zA-Z-]+/)?(.*\.php)$ $2 [L]
RewriteRule . index.php [L]";

fwrite(fopen("../.htaccess", 'w'), $htaccess_file);

// Update the wp-config file
$configFile = file('../wp-config.php');
$newConfig = "";
foreach ($configFile as $line_num => $line) {
	if (substr($line,0,16) == "/* That's all, s") {
		$newConfig .= "define( 'MULTISITE', true );
define( 'SUBDOMAIN_INSTALL', ";
		$newConfig .= $subdomain_install ? 'true' : 'false';
		$newConfig .= ");\n";
		$newConfig .= "\$base = '$base';
define( 'DOMAIN_CURRENT_SITE', '$hostname' );
define( 'PATH_CURRENT_SITE', '$base' );
define( 'SITE_ID_CURRENT_SITE', 1 );
define( 'BLOG_ID_CURRENT_SITE', 1 );\n";
	}
	$newConfig .= $line;
}

fwrite(fopen("../wp-config.php", 'w'), $newConfig);

print("Wrote config files\n");

print("Success\n")

?>
