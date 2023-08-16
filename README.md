# Bake My WP CLI Commands
A curated list of WP-CLI commands crafted by Bake My WP to simplify your website management experience

Discover the power of four robust WP-CLI commands bundled in this compact plugin, each tailored to streamline your website management tasks.

❤️ We highly advise installing this plugin in the 'mu-plugins' folder for optimal performance. <b>Please remember to uninstall it when it's no longer required, as anyone with server access could potentially export your site</b>.

## > wp bmwp-import-posts
Use this command to create dummy content for your site. It crafts 20 unique posts, blending random titles with iconic James Bond movies, categorizes each post under a famed actor, and adorns them with a standard featured image.

## > wp bmwp-import-products
Use this command to create dummy content for your WooCommerce store. It crafts 20 unique products, 15 simple and 5 variable products.

## >  wp bmwp-delete-posts

#### OPTIONS
[--post_type=<post_type>]
: The post type to delete posts. Defaults to 'post'.

[--permanent]
: Whether to permanently delete the posts.

#### EXAMPLES
1. wp bmwp-delete-posts --post_type=product
2. wp bmwp-delete-posts --post_type=product --permanent

## > wp bmwp-fix-plugin-settings
Check if the plugins WPForms (both pro and lite), WP Mail SMTP, Perfmatters are installed and activated and update the options based on our settings.

## > wp bmwp-onboard-site

#### OPTIONS
[--dev=<true>]
: Check if is development mode and hide the site from Seach Engines.

A sequence of operations to optimize your setup:

1. Back up the database.
2. Rename the default category (automatically bypassed if it's not titled 'Uncategorized').
3. Convert HTTP links to HTTPS (coupled with a redis cache flush if alterations occur).
4. Adjust the time zone to Europe/Athens.
5. Update the site email with our default.
6. Check and delete Twenty Twenty-One theme if not active
7. Check and delete Twenty Twenty-Two theme if not active
8. Check for 'dev' flag and update 'blog_public' option

## > wp bmwp-offload-site
Enhance your site pre-offload preparations:

1. Perform a database backup.
2. Audit for certain options and obliterate if detected.
3. Deactivate specified plugins.
4. Install free versions of plugins.
5. Erase the formerly deactivated plugins.
6. Ready a database backup for seamless client handoff.

## > wp bmwp-export-site
Empower your clients with this command. It meticulously prepares a comprehensive backup, priming the site for a smooth transition to an alternative host."
