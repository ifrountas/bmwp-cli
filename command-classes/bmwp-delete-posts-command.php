<?php

    class BMWP_Delete_Posts_Command {

        /**
         * Delete all posts of a specific post type and their associated images. 
         * If --permanent flag is set, delete them permanently.
         *
         * ## OPTIONS
         * 
         * [--post_type=<post_type>]
         * : The post type to delete posts. Defaults to 'post'.
         *
         * [--permanent]
         * : Whether to permanently delete the posts.
         *
         * ## EXAMPLES
         *
         *     wp bmwp-delete-posts --post_type=product
         *     wp bmwp-delete-posts --post_type=product --permanent
         *
         * @param array $args
         * @param array $assoc_args
         */
        public function __invoke($args, $assoc_args) {

            $post_type = isset($assoc_args['post_type']) ? $assoc_args['post_type'] : 'post';
            $permanent = isset($assoc_args['permanent']);

            if (!post_type_exists($post_type)) {
                WP_CLI::error("The post type '{$post_type}' does not exist.");
                return;
            }

            $query_args = [
                'post_type'      => $post_type,
                'posts_per_page' => -1,
                'fields'         => 'ids',
                'post_status'    => $permanent ? 'trash' : 'any'
            ];

            $posts = get_posts($query_args);

            foreach ($posts as $post_id) {
                // Log the item before it's deleted
                WP_CLI::log( WP_CLI::colorize( "%yDeleting%n " ) . WP_CLI::colorize( "%w{$post_type} with ID: {$post_id}%n" ) );

                // Delete associated images
                $attachment_id = get_post_thumbnail_id($post_id);
                if ($attachment_id) {
                    wp_delete_attachment($attachment_id, true);
                }

                // Delete the post
                wp_delete_post($post_id, true);
            }

            WP_CLI::success("All {$post_type}s and their associated images have been deleted.");
        }
    }