<?php

    class BMWP_Generate_Posts_Command {

        /**
         * Generate Dummy content for the WordPress site
         *
         * ## EXAMPLES
         *
         *     wp bmwp-import-posts
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

                $attach_id = BMWP_WPCLI::upload_and_attach_image($post_id);
                set_post_thumbnail( $post_id, $attach_id );

                WP_CLI::log(WP_CLI::colorize("%cPost titled '{$movie}' created successfully!%n"));
            }

            $end_time = microtime(true);
            $execution_time = ($end_time - $start_time);

            WP_CLI::success("All posts created successfully! Total time taken: {$execution_time} seconds.");
        }

    }