<?php

    class BMWP_Site_Onboard_Command {

        /**
         * Sets up a site with custom configurations.
         *
         * ## EXAMPLES
         *
         *     wp bmwp-onboard-site
         */
        public function __invoke( $args, $assoc_args ) {

            // 1. Backup the database
            BMWP_WPCLI::run_database_backup('pre-onboard', '1');

            // 2. Check and rename default category
            $default_category = get_option( 'default_category' );
            $cat = get_category( $default_category );

            if ( strtolower($cat->name) == 'uncategorized' ) {
                wp_update_term($default_category, 'category', array(
                'name' => 'Our News',
                'slug' => 'our-news'
                ));
                WP_CLI::log( WP_CLI::colorize( "%gStep → 2: Default category renamed to Our News.%n" ) );
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

            // 6. Check and delete Twenty Twenty-One theme if not active
            $this->check_and_delete_theme('twentytwentyone', '6');

            // 7. Check and delete Twenty Twenty-Two theme if not active
            $this->check_and_delete_theme('twentytwentytwo', '7');

            // 8. Check for 'dev' flag and update 'blog_public' option
            if ( isset($assoc_args['dev']) && $assoc_args['dev'] === 'true' ) {
                $current_blog_public_value = get_option('blog_public');
                
                if ($current_blog_public_value !== '0') {
                    update_option('blog_public', '0');
                    WP_CLI::log(WP_CLI::colorize("%gStep → 8: Search engine visibility blocked.%n"));
                } else {
                    WP_CLI::log(WP_CLI::colorize("%yStep → 8: The Search engine visibility is already blocked.%n"));
                }
            }

        }

        /**
         * Check if a theme is installed and if it's not the active theme, then delete it.
         *
         * @param string $theme_slug The slug of the theme to check and possibly delete.
         */
        private function check_and_delete_theme($theme_slug, $step) {
            if (wp_get_theme($theme_slug)->exists()) {
                // Check if it's the active theme
                if (get_stylesheet() !== $theme_slug) {
                    // It's not the active theme, so we can safely delete it
                    delete_theme($theme_slug);
                    WP_CLI::log( WP_CLI::colorize("%gStep → {$step}: %n") . WP_CLI::colorize("%wDeleted the {$theme_slug} theme.%n") );
                } else {
                    WP_CLI::warning("The {$theme_slug} theme is currently active. Skipped deleting.");
                }
            } else {
                WP_CLI::log(WP_CLI::colorize("%yStep → {$step}: The {$theme_slug} theme is not installed.%n"));
            }
        }
    }
