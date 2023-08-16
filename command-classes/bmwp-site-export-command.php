<?php

    class BMWP_Site_Export_Command {

        /**
         * Sets up a site with custom configurations.
         *
         * ## EXAMPLES
         *
         *     wp bmwp-export-site
         */
        public function __invoke( $args, $assoc_args ) {

            // 1. Run a full website export ready to be sent to the customer
            BMWP_WPCLI::run_full_site_backup();
        
        }
    }