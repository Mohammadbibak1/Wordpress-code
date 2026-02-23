<?php
/**
 * ۵. محاسبه زمان مطالعه (بهبود یافته برای فارسی).
 * شورت‌کد: [reading_time]
 */
function my_get_reading_time_shortcode( $atts ) {

    // گرفتن آیدی پست
    $post_id = get_the_ID();

    // گرفتن محتوای پست
    $content = get_post_field( 'post_content', $post_id );

    // حذف HTML
    $content = wp_strip_all_tags( $content );

    // شمارش کلمات: استفاده از متد کارآمد برای تشخیص کلمات فارسی (با استفاده از کاراکترهای فاصله‌گذار یونیکد)
    $word_count = count(preg_split('/\s+/u', trim($content)));

    // سرعت مطالعه (قابل تنظیم) - میانگین کلمات در دقیقه
    $words_per_minute = 200; 

    // تبدیل به دقیقه
    $minutes = ceil( $word_count / $words_per_minute );

    if ( $minutes < 1 ) {
        $minutes = 1; // حداقل ۱ دقیقه در نظر گرفته می‌شود
    }

    return $minutes . ' دقیقه زمان مطالعه';
}

// شورت‌کد: [reading_time]
add_shortcode( 'reading_time', 'my_get_reading_time_shortcode' );
