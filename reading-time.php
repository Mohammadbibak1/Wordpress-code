// محاسبه زمان مطالعه
function pr_reading_time( $post_id = null ) {

    // اگر آیدی پست مشخص نشده، از لوپ فعلی استفاده کن
    if ( ! $post_id ) {
        global $post;
        if ( ! $post ) return '۱ دقیقه زمان مطالعه';
        $post_id = $post->ID;
    }

    // گرفتن محتوای پست با فیلترهای وردپرس (المنتور و بلاک‌ها هم درست حساب می‌کنه)
    $content = get_post_field( 'post_content', $post_id );
    $content = apply_filters( 'the_content', $content );

    // حذف تگ‌های HTML
    $content = wp_strip_all_tags( $content );

    // شمارش کلمات (مناسب فارسی)
    $word_count = count( preg_split( '/\s+/u', trim( $content ) ) );

    // سرعت مطالعه
    $words_per_minute = 200;

    // محاسبه دقیقه
    $minutes = ceil( $word_count / $words_per_minute );

    // حداقل ۱ دقیقه
    if ( $minutes < 1 ) $minutes = 1;

    return $minutes . ' دقیقه زمان مطالعه';
}

// ==============================
// شورت‌کد [reading_time] برای استفاده در محتوا
// ==============================
function pr_reading_time_shortcode( $atts ) {
    return pr_reading_time();
}
add_shortcode( 'reading_time', 'pr_reading_time_shortcode' );