<?php
/**
 * Plugin Name:       Bake My WP CLI Commands
 * Description:       A curated list of WP-CLI commands crafted by Bake My WP to simplify your website management experience
 * Version:           1.0.0
 * Author:            Bake My WP
 * Author URI:        https://bakemywp.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bmwp-cli
 * Requires PHP:      8.0
 */




    require_once plugin_dir_path( __FILE__ ) . 'command-classes/bmwp-cli-commands.php';
    require_once plugin_dir_path( __FILE__ ) . 'command-classes/bmwp-generate-posts-command.php';
    require_once plugin_dir_path( __FILE__ ) . 'command-classes/bmwp-generate-woo-products-command.php';
    require_once plugin_dir_path( __FILE__ ) . 'command-classes/bmwp-site-settings-command.php';
    require_once plugin_dir_path( __FILE__ ) . 'command-classes/bmwp-site-onboard-command.php';
    require_once plugin_dir_path( __FILE__ ) . 'command-classes/bmwp-site-offload-command.php';
    require_once plugin_dir_path( __FILE__ ) . 'command-classes/bmwp-site-export-command.php';
    require_once plugin_dir_path( __FILE__ ) . 'command-classes/bmwp-delete-posts-command.php';


    if ( defined( 'WP_CLI' ) && WP_CLI ) {
        WP_CLI::add_command( 'bmwp-import-posts',        'BMWP_Generate_Posts_Command' );
        WP_CLI::add_command( 'bmwp-import-products',     'BMWP_Generate_Woo_Products_Command' );
        WP_CLI::add_command( 'bmwp-delete',              'BMWP_Delete_Posts_Command' );
        WP_CLI::add_command( 'bmwp-fix-plugin-settings', 'BMWP_Site_Settings_Command' );
        WP_CLI::add_command( 'bmwp-onboard-site',        'BMWP_Site_Onboard_Command' );
        WP_CLI::add_command( 'bmwp-offload-site',        'BMWP_Site_Offload_Command' );
        WP_CLI::add_command( 'bmwp-export-site',         'BMWP_Site_Export_Command' );
    }