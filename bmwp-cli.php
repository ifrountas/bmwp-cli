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

class BMWP_WPCLI {
    public static function run_database_backup($state, $step_number) {
        $timestamp = date('YmdHis');
        $backup_file = ABSPATH . $timestamp . '-'.$state.'.sql';
        WP_CLI::runcommand( 'db export ' . $backup_file );
        WP_CLI::log( WP_CLI::colorize( "%gStep → ". $step_number.": Database exported at: " . $backup_file . ".%n" ) );
        return $backup_file;
    }

    public static function run_full_site_backup() {
        // Define the path to the htdocs folder and the destination archive file
        $timestamp = date('YmdHis');
        $htdocs_path = ABSPATH; // ABSPATH is the absolute path to the WordPress directory
        $archive_file = $htdocs_path . '/'.$timestamp.'-archive.tar.gz';

        // Create the tar.gz archive
        $command = "tar -czvf $archive_file --exclude='*pre-offload.sql' -C " . dirname($htdocs_path) . " htdocs";
        $output = shell_exec($command);

        if ($output) {
            WP_CLI::success( WP_CLI::colorize("%g✔️%n" ) . WP_CLI::colorize("%y Website archived successfully to: %n") . WP_CLI::colorize("%w $archive_file%n") );
        } else {
            WP_CLI::error( "✖️ Failed to archive the website." );
        }
    }
}

class BMWP_Generate_Posts {

    /**
     * Generate Dummy content for the WordPress site
     *
     * ## EXAMPLES
     *
     *     wp bmwp-posts
     */

    /**
     * Default behavior when this command is called.
     */
    public function __invoke() {
        $this->generate_custom_posts();
    }

    public static function generate_lorem_ipsum() {
        // API endpoint to generate random Lorem Ipsum
        $endpoint = 'https://loripsum.net/api/1/medium/plaintext';

        // Fetch content from API
        $response = wp_remote_get($endpoint);
        
        if (is_wp_error($response)) {
            // If there's an error or the API is not accessible, return a default Lorem Ipsum text.
            return "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce lacinia nec neque non tempus. Sed pharetra purus a risus ultrices, et condimentum risus interdum. Cras vel sapien non dolor facilisis sagittis. Curabitur fringilla dolor quis ex pharetra, quis ornare nisl dictum. Donec eu diam a justo elementum efficitur.";
        }
        
        return wp_remote_retrieve_body($response);
    }

    /**
     * Generate 20 custom posts with James Bond movie titles, random images, and actor-based categories.
     */
    public static function generate_custom_posts() {

        $start_time = microtime(true);

        $bond_movies = array(
            'Dr. No and the Enchanted Realm of Mysteries' => 'Sean Connery',
            'From Russia: A Tale of Global Affection and Intrigue' => 'Sean Connery',
            'Goldfinger\'s Enchanting Muse: A Dance of Destiny' => 'Sean Connery',
            'Thunderball meets Dragonball: An Unexpected Collision of Worlds' => 'Sean Connery',
            'You Only Live Twice: The Chronicles of Fate and Fortune' => 'Sean Connery',
            'In Her Majesty\'s Exclusive Service: Shadows and Secrets' => 'George Lazenby',
            'Diamonds: The Eternal Allure and the Quest for Timelessness' => 'Sean Connery',
            'Live, Thrive, and Conquer: Leaving the Shadows of the Past Behind' => 'Roger Moore',
            'The Man with the Golden Ambition: A Journey of Power and Pride' => 'Roger Moore',
            'The Spy Who Became My Heart\'s Compass' => 'Roger Moore',
            'Moonraker\'s Quest: Beyond the Stars and Dreams' => 'Roger Moore',
            'For Your Gaze Alone, Cecilia: A Whispered Secret' => 'Roger Moore',
            'Octopussy: Life Lessons Beyond the Screen of Netflix' => 'Roger Moore',
            'A Panoramic View to Cherish and Protect the World' => 'Roger Moore',
            'Basking in the Radiant Living Daylights' => 'Timothy Dalton',
            'License to Thrill: An Iconic Tale of Danger and Desire' => 'Timothy Dalton',
            'GoldenEye\'s Admiration: The Silent Stare of Fate' => 'Pierce Brosnan',
            'Tomorrow\'s Mysteries: A Dance with the Unknown' => 'Pierce Brosnan',
            'The World in Its Magnificent Expanse: A Tale of Unity' => 'Pierce Brosnan',
            'Cherish Today\'s Game, for Tomorrow Promises a New Challenge' => 'Pierce Brosnan'
        );

        // Loop through movies and create categories based on the actor if not already exists.
        foreach ($bond_movies as $movie => $actor) {
            if (!term_exists($actor, 'category')) {
                $term = wp_create_category($actor);
                if ( is_wp_error( $term ) ) {
                    WP_CLI::log("Error creating category for actor {$actor}: " . $term->get_error_message());
                }
            }
        }
     
        foreach ($bond_movies as $movie => $actor) {
            if (!$actor) {
                WP_CLI::log("Unable to find actor for movie '{$movie}'. Skipping.");
                continue;
            }
            // Generate post content
            $content = "<!-- wp:paragraph -->\n<p>" . self::generate_lorem_ipsum() . "</p>\n<!-- /wp:paragraph -->\n<!-- wp:paragraph -->\n<p>" . self::generate_lorem_ipsum() . "</p>\n<!-- /wp:paragraph -->\n<!-- wp:paragraph -->\n<p>" . self::generate_lorem_ipsum() . "</p>\n<!-- /wp:paragraph -->";

            // Get actor category ID
            $actor_term = get_term_by('name', $actor, 'category');
            if (!$actor_term) {
                WP_CLI::log("Unable to find category for actor {$actor}");
                continue;  // Skip to next iteration
            }
            $actor_cat_id = $actor_term->term_id;

            // Create post
            $post_id = wp_insert_post( array(
                'post_title'   => $movie,
                'post_content' => $content,
                'post_status'  => 'publish',
                'post_type'    => 'post',
                'post_category'=> array( $actor_cat_id )
            ) );

            // Download and set a random featured image
            $image_url = 'https://picsum.photos/1200/800';
            $upload_dir = wp_upload_dir();
            $image_data = file_get_contents($image_url);
            $filename = wp_unique_filename( $upload_dir['path'], 'featured-' . $post_id . '.jpg' ); // changed filename generation
            if(wp_mkdir_p($upload_dir['path'])) 
                $file = $upload_dir['path'] . '/' . $filename;
            else
                $file = $upload_dir['basedir'] . '/' . $filename;
            file_put_contents($file, $image_data);

            $wp_filetype = wp_check_filetype($filename, null );
            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_title' => sanitize_file_name($filename),
                'post_content' => '',
                'post_status' => 'inherit'
            );
            $attach_id = wp_insert_attachment( $attachment, $file, $post_id );
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
            wp_update_attachment_metadata( $attach_id, $attach_data );
            set_post_thumbnail( $post_id, $attach_id );

            WP_CLI::log(WP_CLI::colorize("%cPost titled '{$movie}' created successfully!%n"));
        }

        $end_time = microtime(true);
        $execution_time = ($end_time - $start_time);

        WP_CLI::success("All posts created successfully! Total time taken: {$execution_time} seconds.");
    }

}

class BMWP_Site_Onboard_Command {

    /**
     * Sets up a site with custom configurations.
     *
     * ## EXAMPLES
     *
     *     wp bmwp-onboard
     */
    public function __invoke( $args, $assoc_args ) {

        // 1. Backup the database
        BMWP_WPCLI::run_database_backup('pre-onboard', '1');

        // 2. Check and rename default category
        $default_category = get_option( 'default_category' );
        $cat = get_category( $default_category );

        if ( strtolower($cat->name) == 'uncategorized' ) {
            wp_update_term($default_category, 'category', array(
              'name' => 'Blog',
              'slug' => 'blog'
            ));
            WP_CLI::log( WP_CLI::colorize( "%gStep → 2: Default category renamed to Blog.%n" ) );
        } else {
            WP_CLI::log( WP_CLI::colorize( "%yStep → 2: Default category is already named: " . $cat->name . ".%n" ) );
        }

        // 3. Search and replace links
        $changes = WP_CLI::runcommand('search-replace "http://" "https://" --skip-columns=guid --dry-run', array('return' => true));
        $replacements = 0;

        // Match lines like "table_name: # replacements"
        if (preg_match_all('/Success:\s(\d+)\sreplacement/', $changes, $matches)) {
            // Sum all replacements found
            $replacements = intval($matches[1]);
        }
        // If there are replacements to be made (i.e., count is greater than 0), then run the actual search-replace command
        if ($replacements > 0) {
            WP_CLI::runcommand( 'search-replace "http://" "https://" --skip-columns=guid' );
            WP_CLI::log( WP_CLI::colorize( "%gStep 3: All links replaced from http to https.%n" ) );
            // Flush the cache after updating the links
            WP_CLI::runcommand( 'cache flush' );
            WP_CLI::log( WP_CLI::colorize( "%gStep → 3½: Cache flushed successfully.%n" ) );
        } else {
            WP_CLI::log( WP_CLI::colorize( "%yStep → 3: No http links found to replace with https.%n" ) );
        }

        // 4. Set time to Europe/Athens
        $current_timezone = get_option('timezone_string');
        if ($current_timezone !== 'Europe/Athens') {
            update_option('timezone_string', 'Europe/Athens');
            WP_CLI::log(WP_CLI::colorize("%gStep → 4: Timezone set to Europe/Athens.%n"));
        } else {
            WP_CLI::log(WP_CLI::colorize("%yStep → 4: Timezone is already set to Europe/Athens.%n"));
        }

       // 5. Change site email
        $current_email = get_option('admin_email');
        if ($current_email !== 'alerts@bakemywp.com') {
            update_option('admin_email', 'alerts@bakemywp.com');
            WP_CLI::log(WP_CLI::colorize("%gStep → 5: Site email changed to alerts@bakemywp.com.%n"));
        } else {
            WP_CLI::log(WP_CLI::colorize("%yStep → 5: Site email is already set to alerts@bakemywp.com.%n"));
        }

    }
}

class BMWP_Site_Offload_Command {

    /**
     * Offloads the site configurations and plugins.
     *
     * ## EXAMPLES
     *
     *     wp bmwp-offload
     */
    public function __invoke( $args, $assoc_args ) {

        // 1. Backup the database
        BMWP_WPCLI::run_database_backup('pre-offload', '1');

        // 2. Check for specific options and delete them if found
        $options_to_check = array('acf_pro_license', 'wpforms_license', 'perfmatters_edd_license_key', 'wpae_license_key');
        foreach ($options_to_check as $option) {
            if (shell_exec("wp option get $option")) {
                WP_CLI::runcommand( "option delete $option" );
                WP_CLI::success( "Removed option: $option" );
            } else {
                WP_CLI::warning( "Option not found: $option" );
            }
        }

       // 3. Deactivate specified plugins
       $plugins_to_deactivate = array('advanced-custom-fields-pro', 'wp-armour-extended', 'perfmatters', 'wpforms');
       foreach ($plugins_to_deactivate as $plugin) {
           if (shell_exec("wp plugin is-active $plugin")) {
               WP_CLI::runcommand( "plugin deactivate $plugin" );
               WP_CLI::success( "Plugin deactivated: $plugin" );
           } else {
               WP_CLI::warning( "Plugin already inactive or not found: $plugin" );
           }
       }

        // 4. Install 'wpforms-lite' and 'advanced-custom-fields' plugins
        $free_plugins_to_install = array('wpforms-lite', 'advanced-custom-fields');
        foreach ($free_plugins_to_install as $plugin) {
            WP_CLI::runcommand( "plugin install $plugin --activate" );
            WP_CLI::success( "Plugin installed and activated: $plugin" );
        }

        // 5. Delete the previously deactivated plugins
        foreach ($plugins_to_deactivate as $plugin) {
            if (!shell_exec("wp plugin is-active $plugin")) { 
                WP_CLI::runcommand( "plugin delete $plugin" );
                WP_CLI::success( "Plugin deleted: $plugin" );
            }
        }

        // 6. Backup the database
        BMWP_WPCLI::run_database_backup('offloaded', '6');

    }
}

class BMWP_Site_Export_Command {

    /**
     * Sets up a site with custom configurations.
     *
     * ## EXAMPLES
     *
     *     wp bmwp-export
     */
    public function __invoke( $args, $assoc_args ) {

        // 1. Run a full website export ready to be sent to the customer
        BMWP_WPCLI::run_full_site_backup();
    
    }
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
    WP_CLI::add_command( 'bmwp-posts',   'BMWP_Generate_Posts' );
    WP_CLI::add_command( 'bmwp-onboard', 'BMWP_Site_Onboard_Command' );
    WP_CLI::add_command( 'bmwp-offload', 'BMWP_Site_Offload_Command' );
    WP_CLI::add_command( 'bmwp-export',  'BMWP_Site_Export_Command' );
}
