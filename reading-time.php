// محاسبه زمان مطالعه
function pr_reading_time( $post_id = null ) {

    if ( ! $post_id ) {
        global $post;
        if ( ! $post ) return '۱ دقیقه زمان مطالعه';
        $post_id = $post->ID;
    }

    $content = get_post_field( 'post_content', $post_id );
    $content = apply_filters( 'the_content', $content );

    $content = wp_strip_all_tags( $content );

    $word_count = count( preg_split( '/\s+/u', trim( $content ) ) );

    $words_per_minute = 200;

    $minutes = ceil( $word_count / $words_per_minute );

    if ( $minutes < 1 ) $minutes = 1;

    return $minutes . ' دقیقه زمان مطالعه';
}

// ==============================
// شورت‌کد [reading_time] 
// ==============================
function pr_reading_time_shortcode( $atts ) {
    return pr_reading_time();
}
add_shortcode( 'reading_time', 'pr_reading_time_shortcode' );