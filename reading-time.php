// محاسبه زمان مطالعه
function my_get_reading_time_shortcode( $atts ) {

    // گرفتن آیدی پست
    $post_id = get_the_ID();

    // گرفتن محتوای پست
    $content = get_post_field( 'post_content', $post_id );

    // حذف HTML
    $content = wp_strip_all_tags( $content );

    // شمارش کلمات (برای فارسی هم خوبه)
    $word_count = str_word_count( $content );

    // سرعت مطالعه (قابل تنظیم)
    $words_per_minute = 200;

    // تبدیل به دقیقه
    $minutes = ceil( $word_count / $words_per_minute );

    if ( $minutes < 1 ) {
        $minutes = 1;
    }

    return $minutes . ' دقیقه زمان مطالعه';
}

// شورت‌کد: [reading_time]
add_shortcode( 'reading_time', 'my_get_reading_time_shortcode' );