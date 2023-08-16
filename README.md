# Bake My WP CLI Commands
A curated list of WP-CLI commands crafted by Bake My WP to simplify your website management experience

Discover the power of four robust WP-CLI commands bundled in this compact plugin, each tailored to streamline your website management tasks.

❤️ We highly advise installing this plugin in the 'mu-plugins' folder for optimal performance. Please remember to uninstall it when it's no longer required, as anyone with server access could potentially export your site.

## > wp bmwp-posts
Use this command to create dummy content for your site. It crafts 20 unique posts, blending random titles with iconic James Bond movies, categorizes each post under a famed actor, and adorns them with a standard featured image.

## > wp bmwp-onboard
A sequence of operations to optimize your setup:

1. Back up the database.
2. Rename the default category (automatically bypassed if it's not titled 'Uncategorized').
3. Convert HTTP links to HTTPS (coupled with a redis cache flush if alterations occur).
4. Adjust the time zone to Europe/Athens.
5. Update the site email with our default.

## > wp bmwp-offload
Enhance your site pre-offload preparations:

1. Perform a database backup.
2. Audit for certain options and obliterate if detected.
3. Deactivate specified plugins.
4. Install free versions of plugins.
5. Erase the formerly deactivated plugins.
6. Ready a database backup for seamless client handoff.

## > wp bmwp-export
Empower your clients with this command. It meticulously prepares a comprehensive backup, priming the site for a smooth transition to an alternative host."
