<?php

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

        /**
         * Upload an image from a URL and attach it to a WordPress post.
         *
         * @param int $post_id The ID of the post to which the image should be attached.
         * @return int $attach_id The attachment ID of the uploaded image.
         */
        public static function upload_and_attach_image($post_id) {
            $image_url = 'https://picsum.photos/1200/800';
            $upload_dir = wp_upload_dir();
            $image_data = file_get_contents($image_url);
            $filename = wp_unique_filename($upload_dir['path'], 'featured-' . $post_id . '.jpg');

            if(wp_mkdir_p($upload_dir['path'])) {
                $file = $upload_dir['path'] . '/' . $filename;
            } else {
                $file = $upload_dir['basedir'] . '/' . $filename;
            }

            file_put_contents($file, $image_data);

            $wp_filetype = wp_check_filetype($filename, null);
            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_title' => sanitize_file_name($filename),
                'post_content' => '',
                'post_status' => 'inherit'
            );

            $attach_id = wp_insert_attachment($attachment, $file, $post_id);
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attach_data = wp_generate_attachment_metadata($attach_id, $file);
            wp_update_attachment_metadata($attach_id, $attach_data);

            return $attach_id;
        }
    }