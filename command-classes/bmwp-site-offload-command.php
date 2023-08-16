<?php

    class BMWP_Site_Offload_Command {

        /**
         * Offloads the site configurations and plugins.
         *
         * ## EXAMPLES
         *
         *     wp bmwp-offload-site
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