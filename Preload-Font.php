add_action('wp_head', function() {
    echo '<link rel="preload" href="'.get_stylesheet_directory_uri().'/fonts/MyFont.woff2" as="font" type="font/woff2" crossorigin>';
});