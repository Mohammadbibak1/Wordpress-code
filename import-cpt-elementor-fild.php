add_filter('elementor_pro/forms/render/item', function($item, $item_index, $form){

    // فقط فیلدهای checkbox
    if (empty($item['field_type']) || $item['field_type'] !== 'checkbox') {
        return $item;
    }

    // فقط فیلدی که Field ID = services دارد
    if (empty($item['custom_id']) || $item['custom_id'] !== 'services') {
        return $item;
    }

    // پست تایپ شما
    $post_type = 'services';

    $posts = get_posts([
        'post_type'      => $post_type,
        'post_status'    => 'publish',
        'posts_per_page' => 500,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'fields'         => 'ids',
    ]);

    $options = [];

    foreach ($posts as $pid) {
        $title = trim(get_the_title($pid));
        if (!$title) continue;

        // Label|value  => هر دو عنوان
        $safe_title = str_replace(['|', "\n", "\r"], ['-', ' ', ' '], $title);
        $options[] = $safe_title . '|' . $safe_title;
    }

    // پر کردن گزینه‌ها
    $item['field_options'] = implode("\n", $options);
    $item['options']       = $item['field_options'];

    return $item;
}, 10, 3);