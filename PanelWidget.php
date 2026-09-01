<?php
/**
 * Theme functions and definitions.
 *
 * For additional information on potential customization options,
 * read the developers' documentation:
 *
 * https://developers.elementor.com/docs/hello-elementor-theme/
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'HELLO_ELEMENTOR_CHILD_VERSION', '2.0.0' );

/**
 * Load child theme scripts & styles.
 *
 * @return void
 */
function hello_elementor_child_scripts_styles() {

	wp_enqueue_style(
		'hello-elementor-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		[
			'hello-elementor-theme-style',
		],
		HELLO_ELEMENTOR_CHILD_VERSION
	);

}
add_action( 'wp_enqueue_scripts', 'hello_elementor_child_scripts_styles', 20 );



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


function custom_login_logo() {
    ?>
    <style type="text/css">
        #login h1 a {
            background-image: url('https://fitclubcenter.ir/wp-content/uploads/2025/11/Fcc-Logo.webp');
            width: 350px;
            height: 90px;
            background-size: contain;
            background-repeat: no-repeat;
            margin: 0 auto;
        }
    </style>
    <?php
}
add_action( 'login_enqueue_scripts', 'custom_login_logo' );

function custom_login_glass_bg() {
    ?>
    <style>
        body.login {
            background: url('https://fitclubcenter.ir/wp-content/uploads/2025/12/pexels-pixabay-263201.webp') no-repeat center center fixed;
            background-size: cover;
        }

 #loginform {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    padding: 30px;
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

#loginform label {
    color: #fff;
}

#loginform input[type="text"],
#loginform input[type="password"] {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: #fff;
}

#loginform input[type="submit"] {
    background: rgba(255, 255, 255, 0.3);
    color: #fff;
    border: none;
    padding: 10px 20px;
}
        }
    </style>
    <?php
}
add_action('login_enqueue_scripts', 'custom_login_glass_bg');

add_action('wp_footer', function () {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.title-comments, .comment-reply-title').forEach(function(el){
            const p = document.createElement('p');
            p.className = el.className;
            p.innerHTML = el.innerHTML;
            el.replaceWith(p);
        });
    });
    </script>
    <?php
});



// نمایش وضعیت ایندکس + لینک مستقیم به تنظیمات خواندن (بالای پنل مدیریت)

add_action('admin_notices', function () {

    $blog_public = (int) get_option('blog_public', 1);
    $reading_url = admin_url('options-reading.php');

    if ($blog_public === 0) {
        $status_text  = 'ایندکس غیرفعال (برای تغییر کلیک کن)';
        $status_color = '#ff6b6b';
        $status_bg    = 'linear-gradient(135deg,#ff4d4d,#c0392b)';
    } else {
        $status_text  = 'ایندکس فعال (برای بررسی کلیک کن)';
        $status_color = '#2ecc71';
        $status_bg    = 'linear-gradient(135deg,#2ecc71,#27ae60)';
    }

    echo '
    <style>
        .bb-index-status-link{
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 16px;
            border-radius: 24px;
            background: '.$status_bg.';
            color: #fff !important;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none !important;
            box-shadow: 0 6px 18px rgba(0,0,0,0.25);
            transition: all .3s ease;
            cursor: pointer;
            margin: 10px 0 5px;
        }
        .bb-index-status-link:hover{
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 10px 28px rgba(0,0,0,0.35);
            opacity: .95;
        }
        .bb-index-dot{
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: '.$status_color.';
            box-shadow: 0 0 8px '.$status_color.';
        }
        .bb-index-arrow{
            opacity:.9;
            font-weight:700;
            margin-right:4px;
        }
    </style>

    <div>
        <a class="bb-index-status-link" href="'. esc_url($reading_url) .'" title="رفتن به تنظیمات خواندن (Reading)">
            <span class="bb-index-dot"></span>
            <span>وضعیت سایت: <strong>'.$status_text.'</strong></span>
            <span class="bb-index-arrow">↗</span>
        </a>
    </div>
    ';
});



add_action('admin_footer', function () {
    echo '
    <div style="
        position: fixed;
        bottom: 12px;
        left: 20px;
        z-index: 9999;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        border-radius: 20px;
        background: linear-gradient(135deg,#667eea,#764ba2);
        color: #fff;
        font-size: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.18);
        opacity: .95;
    ">
        <span style="
            width:8px;
            height:8px;
            background:#00ff9c;
            border-radius:50%;
            box-shadow:0 0 6px rgba(0,255,156,0.9);
        "></span>
        <span>
            طراحی شده توسط <strong style="font-weight:600;">محمد بی باک</strong>
        </span>
    </div>
    ';
});





/**
 * Activity Log (Lightweight)
 * - Login/Logout
 * - Post/Page publish
 * - Post/Page delete
 * - Options changes (settings)
 * Admin: Tools -> Activity Log
 */

defined('ABSPATH') || exit;

/** =========================
 *  1) DB Table Setup
 *  ========================= */
function bb_activity_log_table_name() {
    global $wpdb;
    return $wpdb->prefix . 'bb_activity_log';
}

function bb_activity_log_install() {
    global $wpdb;
    $table = bb_activity_log_table_name();

    // avoid repeated create
    if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table)) === $table) return;

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE {$table} (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        created_at DATETIME NOT NULL,
        user_id BIGINT UNSIGNED NULL,
        user_login VARCHAR(60) NULL,
        ip VARCHAR(45) NULL,
        action_key VARCHAR(50) NOT NULL,
        object_type VARCHAR(30) NULL,
        object_id BIGINT UNSIGNED NULL,
        message TEXT NOT NULL,
        meta LONGTEXT NULL,
        PRIMARY KEY  (id),
        KEY created_at (created_at),
        KEY user_id (user_id),
        KEY action_key (action_key),
        KEY object_id (object_id)
    ) {$charset_collate};";

    dbDelta($sql);
}
add_action('admin_init', 'bb_activity_log_install');

/** =========================
 *  2) Logger Core
 *  ========================= */
function bb_activity_log_ip() {
    // Best-effort; keep simple to avoid trusting spoofed headers
    return isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';
}

function bb_activity_log_add($action_key, $message, $object_type = null, $object_id = null, $meta = null) {
    global $wpdb;
    $table = bb_activity_log_table_name();

    $user = wp_get_current_user();
    $user_id = ($user && $user->exists()) ? (int) $user->ID : null;
    $user_login = ($user && $user->exists()) ? $user->user_login : null;

    $wpdb->insert($table, [
        'created_at'   => current_time('mysql'),
        'user_id'      => $user_id,
        'user_login'   => $user_login,
        'ip'           => bb_activity_log_ip(),
        'action_key'   => sanitize_key($action_key),
        'object_type'  => $object_type ? sanitize_key($object_type) : null,
        'object_id'    => $object_id ? (int) $object_id : null,
        'message'      => wp_kses_post($message),
        'meta'         => $meta ? wp_json_encode($meta, JSON_UNESCAPED_UNICODE) : null,
    ]);
}

/** =========================
 *  3) Events
 *  ========================= */

// Login
add_action('wp_login', function ($user_login, $user) {
    bb_activity_log_add(
        'login',
        'ورود به سیستم',
        'user',
        $user ? (int)$user->ID : null,
        ['user_login' => $user_login]
    );
}, 10, 2);

// Logout
add_action('wp_logout', function () {
    bb_activity_log_add('logout', 'خروج از سیستم', 'user', null, null);
});

// Publish (posts/pages/custom post types too)
add_action('transition_post_status', function ($new_status, $old_status, $post) {
    if (wp_is_post_revision($post->ID) || wp_is_post_autosave($post->ID)) return;
    if ($new_status !== 'publish' || $old_status === 'publish') return;

    $type = $post->post_type;
    $title = $post->post_title ? esc_html($post->post_title) : ('#' . $post->ID);

    bb_activity_log_add(
        'publish',
        'انتشار ' . esc_html($type) . ': <strong>' . $title . '</strong>',
        $type,
        (int)$post->ID,
        ['old_status' => $old_status, 'new_status' => $new_status]
    );
}, 10, 3);

// Delete (before delete)
add_action('before_delete_post', function ($post_id) {
    $post = get_post($post_id);
    if (!$post) return;

    if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) return;

    $type = $post->post_type;
    $title = $post->post_title ? esc_html($post->post_title) : ('#' . $post_id);

    bb_activity_log_add(
        'delete',
        'حذف ' . esc_html($type) . ': <strong>' . $title . '</strong>',
        $type,
        (int)$post_id,
        ['status' => $post->post_status]
    );
});

// Settings/options change (فقط وقتی مدیر تغییر می‌دهد)
add_action('updated_option', function ($option, $old_value, $value) {
    if (!is_admin()) return;
    if (!current_user_can('manage_options')) return;

    // برای جلوگیری از سنگین شدن، بعضی گزینه‌های پرحجم/حساس را لاگ نکن
    $skip = [
        'rewrite_rules', 'cron', 'widget_', 'sidebars_widgets',
        'theme_mods_', 'active_plugins', 'recently_edited',
    ];
    foreach ($skip as $prefix) {
        if (str_starts_with((string)$option, $prefix)) return;
    }

    // مقدارها را ذخیره نکنیم (ممکنه حساس باشد) فقط نام گزینه
    bb_activity_log_add(
        'option_change',
        'تغییر تنظیمات: <strong>' . esc_html($option) . '</strong>',
        'option',
        null,
        ['option' => (string)$option]
    );
}, 10, 3);

/** =========================
 *  4) Admin Page (Tools -> Activity Log)
 *  ========================= */
add_action('admin_menu', function () {
    add_management_page(
        'Activity Log',
        'لاگ فعالیت',
        'manage_options',
        'bb-activity-log',
        'bb_activity_log_page'
    );
});

function bb_activity_log_page() {
    if (!current_user_can('manage_options')) wp_die('دسترسی ندارید.');

    global $wpdb;
    $table = bb_activity_log_table_name();

    // Clear log
    if (isset($_POST['bb_clear_log']) && check_admin_referer('bb_clear_log_action')) {
        $wpdb->query("TRUNCATE TABLE {$table}");
        echo '<div class="notice notice-success"><p>لاگ پاک شد.</p></div>';
    }

    $per_page = 30;
    $paged = isset($_GET['paged']) ? max(1, (int) $_GET['paged']) : 1;
    $offset = ($paged - 1) * $per_page;

    $total = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table}");
    $rows = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM {$table} ORDER BY id DESC LIMIT %d OFFSET %d",
            $per_page,
            $offset
        ),
        ARRAY_A
    );

    $total_pages = max(1, (int) ceil($total / $per_page));

    echo '<div class="wrap"><h1>لاگ فعالیت کاربران</h1>';

    echo '<form method="post" style="margin:12px 0;">';
    wp_nonce_field('bb_clear_log_action');
    echo '<button type="submit" name="bb_clear_log" class="button button-secondary" onclick="return confirm(\'لاگ کامل پاک شود؟\')">پاک کردن لاگ</button>';
    echo '</form>';

    echo '<table class="widefat fixed striped">';
    echo '<thead><tr>
            <th style="width:160px;">زمان</th>
            <th style="width:160px;">کاربر</th>
            <th style="width:120px;">IP</th>
            <th style="width:140px;">نوع</th>
            <th>جزئیات</th>
          </tr></thead><tbody>';

    if (!$rows) {
        echo '<tr><td colspan="5">لاگی ثبت نشده است.</td></tr>';
    } else {
        foreach ($rows as $r) {
            $time = esc_html($r['created_at']);
            $user = $r['user_login'] ? esc_html($r['user_login']) : '—';
            $ip   = $r['ip'] ? esc_html($r['ip']) : '—';
            $act  = esc_html($r['action_key']);
            $msg  = wp_kses_post($r['message']);

            echo "<tr>
                    <td>{$time}</td>
                    <td>{$user}</td>
                    <td>{$ip}</td>
                    <td>{$act}</td>
                    <td>{$msg}</td>
                  </tr>";
        }
    }

    echo '</tbody></table>';

    // Pagination
    if ($total_pages > 1) {
        $base_url = admin_url('tools.php?page=bb-activity-log');
        echo '<div style="margin-top:12px;">';
        echo paginate_links([
            'base'      => add_query_arg('paged', '%#%', $base_url),
            'format'    => '',
            'prev_text' => '« قبلی',
            'next_text' => 'بعدی »',
            'total'     => $total_pages,
            'current'   => $paged,
        ]);
        echo '</div>';
    }

    echo '</div>';
}



/**
 * Dashboard Widgets Pack:
 * 1) Quick Actions (nice cards + dashicons)
 * 2) Activity Log Summary (last 10) -> expects wp_bb_activity_log table
 * 3) Project Guide (editable text)
 */

defined('ABSPATH') || exit;

add_action('admin_enqueue_scripts', function ($hook) {
    // فقط پیشخوان
    if ($hook !== 'index.php') return;

    // CSS کوچک برای هر 3 ویجت
    $css = '
    .bbw-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin-top:6px}
    .bbw-card{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:14px;
        background:linear-gradient(135deg,#1f2937,#111827);color:#fff;text-decoration:none!important;
        box-shadow:0 6px 18px rgba(0,0,0,.18);transition:transform .18s ease, box-shadow .18s ease, opacity .18s ease;
    }
    .bbw-card:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(0,0,0,.25);opacity:.95}
    .bbw-ico{width:34px;height:34px;border-radius:10px;display:grid;place-items:center;
        background:rgba(255,255,255,.12)
    }
    .bbw-ico .dashicons{font-size:18px;line-height:1}
    .bbw-title{font-weight:700}
    .bbw-sub{opacity:.85;font-size:12px}
    .bbw-row{display:flex;justify-content:space-between;gap:10px;padding:8px 0;border-bottom:1px solid rgba(0,0,0,.06)}
    .bbw-row:last-child{border-bottom:0}
    .bbw-pill{font-size:11px;padding:3px 8px;border-radius:999px;background:#f1f5f9;color:#0f172a;white-space:nowrap}
    .bbw-muted{color:#64748b;font-size:12px}
    .bbw-log-msg{font-size:13px}
    .bbw-box{padding:10px 12px;border-radius:14px;background:#f8fafc;border:1px solid #e2e8f0}
    .bbw-textarea{width:100%;min-height:140px}
    .bbw-actions{display:flex;gap:8px;align-items:center;margin-top:8px}
    ';
    wp_register_style('bbw-dashboard-style', false);
    wp_enqueue_style('bbw-dashboard-style');
    wp_add_inline_style('bbw-dashboard-style', $css);
});

add_action('wp_dashboard_setup', function () {

    wp_add_dashboard_widget(
        'bbw_quick_actions',
        'میانبرهای سریع',
        'bbw_render_quick_actions'
    );

    wp_add_dashboard_widget(
        'bbw_activity_log_summary',
        'خلاصه فعالیت‌ها (آخرین ۱۰)',
        'bbw_render_activity_log_summary'
    );

    wp_add_dashboard_widget(
        'bbw_project_guide',
        'راهنمای پروژه (قابل ویرایش)',
        'bbw_render_project_guide'
    );
});

/** =========================
 *  Widget 1: Quick Actions
 *  ========================= */
function bbw_render_quick_actions() {

    $items = [
        [
            'title' => 'افزودن نوشته',
            'sub'   => 'Post جدید بساز',
            'url'   => admin_url('post-new.php'),
            'icon'  => 'dashicons-edit',
        ],
        [
            'title' => 'افزودن برگه',
            'sub'   => 'Page جدید بساز',
            'url'   => admin_url('post-new.php?post_type=page'),
            'icon'  => 'dashicons-media-document',
        ],
        [
            'title' => 'رسانه‌ها',
            'sub'   => 'آپلود و مدیریت فایل‌ها',
            'url'   => admin_url('upload.php'),
            'icon'  => 'dashicons-format-image',
        ],
        [
            'title' => 'مشاهده سایت',
            'sub'   => 'بازدید از سایت',
            'url'   => home_url('/'),
            'icon'  => 'dashicons-admin-site',
        ],
        [
            'title' => 'منوها',
            'sub'   => 'مدیریت فهرست‌ها',
            'url'   => admin_url('nav-menus.php'),
            'icon'  => 'dashicons-menu',
        ],
        [
            'title' => 'کاربران',
            'sub'   => 'مدیریت کاربران',
            'url'   => admin_url('users.php'),
            'icon'  => 'dashicons-admin-users',
        ],
    ];

    // اگر ووکامرس بود، چند میانبر اضافه کن
    if (class_exists('WooCommerce')) {
        $items[] = [
            'title' => 'سفارش‌ها',
            'sub'   => 'مدیریت سفارش‌ها',
            'url'   => admin_url('edit.php?post_type=shop_order'),
            'icon'  => 'dashicons-cart',
        ];
        $items[] = [
            'title' => 'افزودن محصول',
            'sub'   => 'محصول جدید بساز',
            'url'   => admin_url('post-new.php?post_type=product'),
            'icon'  => 'dashicons-tag',
        ];
    }

    echo '<div class="bbw-grid">';
    foreach ($items as $it) {
        echo '
        <a class="bbw-card" href="'. esc_url($it['url']) .'">
            <span class="bbw-ico"><span class="dashicons '. esc_attr($it['icon']) .'"></span></span>
            <span>
                <div class="bbw-title">'. esc_html($it['title']) .'</div>
                <div class="bbw-sub">'. esc_html($it['sub']) .'</div>
            </span>
        </a>';
    }
    echo '</div>';
}

/** =========================
 *  Widget 2: Activity Log Summary
 *  expects table: {$wpdb->prefix}bb_activity_log
 *  ========================= */
function bbw_activity_log_table_name() {
    global $wpdb;
    return $wpdb->prefix . 'bb_activity_log';
}

function bbw_render_activity_log_summary() {
    global $wpdb;

    $table = bbw_activity_log_table_name();

    // چک وجود جدول
    $exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table));
    if ($exists !== $table) {
        echo '<div class="bbw-box">
                <div class="bbw-log-msg">جدول لاگ پیدا نشد. اگر می‌خوای این ویجت کار کنه، باید کد «لاگ فعالیت» قبلی رو هم نصب کرده باشی.</div>
                <div class="bbw-muted" style="margin-top:6px;">(table: '. esc_html($table) .')</div>
              </div>';
        return;
    }

    $rows = $wpdb->get_results(
        "SELECT created_at, user_login, ip, action_key, message
         FROM {$table}
         ORDER BY id DESC
         LIMIT 10",
        ARRAY_A
    );

    if (!$rows) {
        echo '<div class="bbw-box"><div class="bbw-muted">هنوز لاگی ثبت نشده است.</div></div>';
        return;
    }

    echo '<div class="bbw-box">';
    foreach ($rows as $r) {
        $time = esc_html($r['created_at']);
        $user = $r['user_login'] ? esc_html($r['user_login']) : '—';
        $ip   = $r['ip'] ? esc_html($r['ip']) : '—';
        $act  = $r['action_key'] ? esc_html($r['action_key']) : 'event';
        $msg  = wp_kses_post($r['message']);

        echo '
        <div class="bbw-row">
            <div>
                <div style="font-weight:700;font-size:12px;margin-bottom:2px;">'. $msg .'</div>
                <div class="bbw-muted">'. $user .' · '. $ip .' · '. $time .'</div>
            </div>
            <div class="bbw-pill">'. $act .'</div>
        </div>';
    }
    echo '</div>';

    echo '<div class="bbw-actions">
            <a class="button button-secondary" href="'. esc_url(admin_url('tools.php?page=bb-activity-log')) .'">نمایش همه</a>
          </div>';
}

/** =========================
 *  Widget 3: Project Guide (Editable)
 *  ========================= */
function bbw_project_guide_option_key() {
    return 'bbw_project_guide_text';
}

function bbw_render_project_guide() {
    if (!current_user_can('manage_options')) {
        echo '<div class="bbw-muted">دسترسی ندارید.</div>';
        return;
    }

    // Save
    if (isset($_POST['bbw_project_guide_save']) && check_admin_referer('bbw_project_guide_save_action')) {
        $txt = isset($_POST['bbw_project_guide_text']) ? wp_kses_post(wp_unslash($_POST['bbw_project_guide_text'])) : '';
        update_option(bbw_project_guide_option_key(), $txt);
        echo '<div class="notice notice-success inline"><p>راهنما ذخیره شد.</p></div>';
    }

    $default = "<strong>راهنمای استفاده:</strong>\n<ul>\n<li>برای افزودن محتوا از «افزودن نوشته/برگه» استفاده کنید.</li>\n<li>در صورت بروز مشکل ابتدا کش مرورگر را پاک کنید و دوباره تست کنید.</li>\n<li>برای تغییر تنظیمات سایت فقط با مدیر هماهنگ کنید.</li>\n</ul>";
    $value = get_option(bbw_project_guide_option_key(), $default);

    echo '
    <form method="post">
        '. wp_nonce_field('bbw_project_guide_save_action', '_wpnonce', true, false) .'
        <textarea class="bbw-textarea" name="bbw_project_guide_text">'. esc_textarea($value) .'</textarea>
        <div class="bbw-actions">
            <button type="submit" name="bbw_project_guide_save" class="button button-primary">ذخیره راهنما</button>
        </div>
        <div class="bbw-muted" style="margin-top:8px;">
            نکته: می‌تونی HTML ساده (مثل <code>&lt;strong&gt;</code> و <code>&lt;ul&gt;</code>) هم بذاری.
        </div>
    </form>';
}

