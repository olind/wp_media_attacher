# wp_media_attacher
Searches *WordPress media library* for *unattached media*, tries to locate the post where it's used and attach the media.

Place the folder in wp-content
Use WP-CLI to run it with: `wp eval-file wp_media_attacher.php --debug` and to save all output to log file `wp eval-file wp_media_attacher.php --debug > doit.log 2>&1`

Not nearly production quality. Used to clean my own really old (20+ years) websites.