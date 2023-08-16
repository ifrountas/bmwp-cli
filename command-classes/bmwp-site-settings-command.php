<?php

    class BMWP_Site_Settings_Command {

        /**
         * Check and update WPForms settings.
         *
         * ## EXAMPLES
         *
         *     wp bmwp-fix-plugin-settings
         */
        public function __invoke() {

            // Flags to track if we made changes
            $settings_updated = false;
            
            // Check if WPForms is active
            if (is_plugin_active('wpforms/wpforms.php') || is_plugin_active('wpforms-lite/wpforms.php')) {
                $wpforms_settings = get_option('wpforms_settings');

                // Check if settings exist and are in array format
                if (!$wpforms_settings || !is_array($wpforms_settings)) {
                    WP_CLI::error("WPForms settings not found or in unexpected format.");
                    return;
                }
    
                if (!isset($wpforms_settings['hide-announcements']) || $wpforms_settings['hide-announcements'] === false) {
                    $wpforms_settings['hide-announcements'] = '1';
                    $settings_updated = true;
                }
                
                // Check 'email-summaries-disable' setting and set it if not set or if false
                if (!isset($wpforms_settings['email-summaries-disable']) || $wpforms_settings['email-summaries-disable'] === false) {
                    $wpforms_settings['email-summaries-disable'] = '1';
                    $settings_updated = true;
                }

                if ($settings_updated) {
                    update_option('wpforms_settings', $wpforms_settings);
                    WP_CLI::success("WPForms settings updated successfully.");
                }else {
                    WP_CLI::log("All WPForms settings are correct.");
                }
            } else {
                WP_CLI::warning("WPForms plugin is not activated.");
            }

            // Check if WP Mail SMTP is active
            if (is_plugin_active('wp-mail-smtp/wp_mail_smtp.php')) {
                $wp_mail_smtp_settings = get_option('wp_mail_smtp');

                if (!$wp_mail_smtp_settings || !is_array($wp_mail_smtp_settings)) {
                    WP_CLI::error("WP Mail SMTP settings not found or in unexpected format.");
                    return;
                }

                if ( !isset($wp_mail_smtp_settings['general']['dashboard_widget_hidden']) || $wp_mail_smtp_settings['general']['dashboard_widget_hidden'] === false ) {
                    $wp_mail_smtp_settings['general']['dashboard_widget_hidden'] = '1';
                    $settings_updated = true;
                }
        
                if (!isset($wp_mail_smtp_settings['general']['summary_report_email_disabled']) || $wp_mail_smtp_settings['general']['summary_report_email_disabled'] === false) {
                    $wp_mail_smtp_settings['general']['summary_report_email_disabled'] = '1';
                    $settings_updated = true;
                }
        
                if (!isset($wp_mail_smtp_settings['general']['am_notifications_hidden']) || $wp_mail_smtp_settings['general']['am_notifications_hidden'] === false) {
                    $wp_mail_smtp_settings['general']['am_notifications_hidden'] = '1';
                    $settings_updated = true;
                }

                if ($settings_updated) {
                    update_option('wp_mail_smtp', $wp_mail_smtp_settings);
                    WP_CLI::success("WP Mail SMTP settings updated successfully.");
                }
            } else {
                WP_CLI::warning("WP Mail SMTP plugin is not activated.");
            }
        

            // Check if Perfmatters is active
            if (is_plugin_active('perfmatters/perfmatters.php')) {

                $perfmatters_options = get_option('perfmatters_options');

                if (!isset($perfmatters_options['login_url']) || $perfmatters_options['login_url'] === "") {
                    $perfmatters_options['login_url'] = 'user-login';
                    $settings_updated = true;
                }
        
                if (!isset($perfmatters_options['login_url_behavior']) || $perfmatters_options['login_url_behavior'] === "" ) {
                    $perfmatters_options['login_url_behavior'] = '404';
                    $settings_updated = true;
                }
        
                if ($settings_updated) {
                    update_option('perfmatters_options', $perfmatters_options);
                    WP_CLI::success("Perfmatters options updated successfully.");
                }
            } else {
                WP_CLI::warning("Perfmatters plugin is not activated.");
            }

            if ($settings_updated) {
                WP_CLI::runcommand('cache flush');
            }
        
        }
    }