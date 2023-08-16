<?php

    class BMWP_Generate_Woo_Products_Command {

        /**
         * Adds sample products to a WooCommerce store.
         *
         * ## EXAMPLES
         *
         *     wp bmwp-import-products
         */
        public function __invoke() {
            if ( is_plugin_active('woocommerce/woocommerce.php')) {
                WP_CLI::line('WooCommerce is active. Proceeding with product import...');

                $this->import_simple_products();
                $this->import_variable_products();

                WP_CLI::success('Finished importing products.');
            } else {
                WP_CLI::warning('WooCommerce is not active. Please activate it first.');
            }
        }

        private function import_simple_products() {
            // Sample adjectives and nouns for generating product names
            $adjectives = array('Glossy', 'Matte', 'Stylish', 'Elegant', 'Modern', 'Vintage', 'Sleek', 'Rustic', 'Bold', 'Subtle');
            $nouns = array('Lamp', 'Chair', 'Table', 'Rug', 'Shelf', 'Clock', 'Mirror', 'Vase', 'Mug', 'Bowl');
        
            for ($i = 0; $i < 15; $i++) {
                $product_name = $adjectives[array_rand($adjectives)] . ' ' . $nouns[array_rand($nouns)];
        
                $product = new WC_Product();
                $product->set_name($product_name);
                $product->set_status("publish");
                $product->set_catalog_visibility('visible');
                $product->set_description('Sample description for ' . $product_name);
                $product->set_regular_price(rand(10, 200)); // Random price between 10 and 200
                
                $product->save();

                $attach_id = BMWP_WPCLI::upload_and_attach_image($product->get_id());
                set_post_thumbnail($product->get_id(), $attach_id);
        
            }
        }

        private function import_variable_products() {
            $sizes = array('Small', 'Medium', 'Large', 'XLarge', 'XXLarge');
            $adjectives = array('Glossy', 'Matte', 'Stylish', 'Elegant', 'Modern', 'Vintage', 'Sleek', 'Rustic', 'Bold', 'Subtle');
            $nouns = array('T-Shirt', 'Jacket', 'Jeans', 'Dress', 'Blouse', 'Pants', 'Sweater', 'Shorts', 'Coat', 'Scarf');
        
            for ($i = 0; $i < 5; $i++) {
                $product_name = $adjectives[array_rand($adjectives)] . ' ' . $nouns[array_rand($nouns)];
        
                // Creating the variable product
                $product = new WC_Product_Variable();
                $product->set_name($product_name);
                $product->set_status("publish");
                $product->set_catalog_visibility('visible');
                $product->set_description('Sample description for ' . $product_name);
                
                $product->save();

                $attach_id = BMWP_WPCLI::upload_and_attach_image($product->get_id());
                set_post_thumbnail($product->get_id(), $attach_id);
        
                // Adding sizes as product attribute
                $attributes = array();
                $attribute = new WC_Product_Attribute();
                $attribute->set_id(0);
                $attribute->set_name('Size');
                $attribute->set_options($sizes);
                $attribute->set_position(0);
                $attribute->set_visible(1);
                $attribute->set_variation(1);
                $attributes[] = $attribute;
                $product->set_attributes($attributes);
                $product->save();
        
                // Adding product variations (like size Small with a specific price)
                foreach ($sizes as $size) {
                    $variation = new WC_Product_Variation();
                    $variation->set_parent_id($product->get_id());
                    $variation->set_attributes(array('Size' => $size));
                    $variation->set_status("publish");
                    $variation->set_regular_price(rand(10, 100)); // Random price between 10 and 100 for each size
                    $variation->save();
                }
            }
        }
    }