<?php
if (!defined('ABSPATH')) exit;

class QL_Admin {
    public static function menu() {
        add_menu_page('Quiz Lab', 'Quiz Lab', 'manage_options', 'quiz-lab', [self::class, 'page_home'], 'dashicons-welcome-learn-more', 26);
        add_submenu_page('quiz-lab', 'آزمون‌ها', 'آزمون‌ها', 'manage_options', 'edit.php?post_type=ql_quiz');
        add_submenu_page('quiz-lab', 'دسته‌بندی‌ها', 'دسته‌بندی‌ها', 'manage_options', 'edit-tags.php?taxonomy=ql_quiz_category&post_type=ql_quiz');
        add_submenu_page('quiz-lab', 'نتایج', 'نتایج', 'manage_options', 'quiz-lab-results', [self::class, 'page_results']);
    }

    public static function page_home() {
        echo '<div class="wrap ql-admin-page">';
        echo '<h1>Quiz Lab</h1>';
        echo '<div class="ql-admin-hero">';
        echo '<div class="ql-admin-hero__text">';
        echo '<p style="margin:0 0 8px;">آزمون بساز، شورتکد بگیر، توی برگه بذار و نتایج رو ببین.</p>';
        echo '<p style="margin:0;color:#667085;">پشتیبانی: آزمون عادی، نظرسنجی (Poll)، شخصیت‌شناسی/MBTI</p>';
        echo '</div>';
        echo '<div class="ql-admin-hero__actions">';
        echo '<a class="button button-primary" href="' . admin_url('post-new.php?post_type=ql_quiz') . '">افزودن آزمون جدید</a> ';
        echo '<a class="button" href="' . admin_url('edit.php?post_type=ql_quiz') . '">مدیریت آزمون‌ها</a> ';
        echo '<a class="button" href="' . admin_url('admin.php?page=quiz-lab-results') . '">دیدن نتایج</a>';
        echo '</div></div>';
        echo '<div style="margin-top:14px" class="ql-admin-card">';
        echo '<b>شورتکد:</b> <code>[quiz_lab id="123"]</code> (عدد 123 را با ID آزمون جایگزین کن)';
        echo '</div>';
        echo '</div>';
    }

    public static function page_results() {
        global $wpdb;
        $attempts = $wpdb->prefix . 'ql_attempts';
        $rows = $wpdb->get_results("SELECT * FROM $attempts ORDER BY id DESC LIMIT 150", ARRAY_A);

        echo '<div class="wrap ql-admin-page"><h1>نتایج (۱۵۰ مورد آخر)</h1>';
        echo '<table class="widefat striped ql-results-table">';
        echo '<thead><tr>
                <th>ID</th><th>آزمون</th><th>کاربر</th><th>نوع</th><th>وضعیت</th><th>نتیجه</th><th>زمان</th><th>تاریخ</th>
              </tr></thead><tbody>';

        if (!$rows) {
            echo '<tr><td colspan="8">هنوز نتیجه‌ای ثبت نشده.</td></tr>';
        } else {
            foreach ($rows as $r) {
                $quiz_id = (int)$r['quiz_id'];
                $quiz_title = get_the_title($quiz_id);
                $meta = ql_get_quiz_meta($quiz_id);
                $type = $meta['type'] ?? 'normal';

                $user = 'مهمان';
                if ((int)$r['user_id']) {
                    $u = get_userdata((int)$r['user_id']);
                    $user = $u ? $u->user_login : 'کاربر';
                }

                $type_map = ['normal' => 'عادی', 'poll' => 'نظرسنجی', 'personality' => 'شخصیت‌شناسی'];
                $type_fa = $type_map[$type] ?? $type;

                $status_map = ['started' => 'در حال انجام', 'finished' => 'تکمیل شده', 'abandoned' => 'لغو/بسته شده'];
                $status = $status_map[$r['status']] ?? $r['status'];

                if ($type === 'normal') $result = (int)$r['score'] . ' / ' . (int)$r['max_score'] . ' (' . (int)$r['percent'] . '%)';
                elseif ($type === 'poll') $result = 'ثبت شد';
                else $result = 'Type: ' . ($r['personality_type'] ? esc_html($r['personality_type']) : '-');

                echo '<tr>';
                echo '<td>' . (int)$r['id'] . '</td>';
                echo '<td>' . esc_html($quiz_title ?: ('#'.$quiz_id)) . '</td>';
                echo '<td>' . esc_html($user) . '</td>';
                echo '<td>' . esc_html($type_fa) . '</td>';
                echo '<td><span class="ql-badge ql-badge--' . esc_attr($r['status']) . '">' . esc_html($status) . '</span></td>';
                echo '<td>' . $result . '</td>';
                echo '<td>' . (int)$r['time_spent'] . ' ثانیه</td>';
                echo '<td>' . esc_html($r['created_at']) . '</td>';
                echo '</tr>';
            }
        }

        echo '</tbody></table></div>';
    }

    public static function metaboxes() {
        add_meta_box('ql_quiz_settings', 'تنظیمات آزمون', [self::class, 'metabox_settings'], 'ql_quiz', 'normal', 'high');
        add_meta_box('ql_quiz_questions', 'سوالات آزمون', [self::class, 'metabox_questions'], 'ql_quiz', 'normal', 'high');
        add_meta_box('ql_quiz_shortcode', 'شورتکد', [self::class, 'metabox_shortcode'], 'ql_quiz', 'side', 'high');
    }

    public static function metabox_shortcode($post) {
        echo '<p>این شورتکد را در برگه/نوشته قرار بده:</p>';
        echo '<p><code style="font-size:12px;">[quiz_lab id="' . (int)$post->ID . '"]</code></p>';
    }

    public static function metabox_settings($post) {
        $type = get_post_meta($post->ID, '_ql_type', true) ?: 'normal';
        $time_limit = (int) get_post_meta($post->ID, '_ql_time_limit', true);
        $login_required = (int) get_post_meta($post->ID, '_ql_login_required', true);

        $types_raw = (string)get_post_meta($post->ID, '_ql_personality_types', true);
        $redirects = get_post_meta($post->ID, '_ql_personality_redirects', true);
        $redirects = is_array($redirects) ? $redirects : [];

        wp_nonce_field('ql_save_quiz', 'ql_nonce');

        echo '<div class="ql-admin-card"><div class="ql-grid">';
        echo '<div class="ql-field"><label><b>نوع آزمون</b></label>
              <select class="ql-control" name="ql_type" id="ql_type">
                <option value="normal" ' . selected($type, 'normal', false) . '>آزمون عادی (درست/غلط)</option>
                <option value="poll" ' . selected($type, 'poll', false) . '>نظرسنجی (Poll)</option>
                <option value="personality" ' . selected($type, 'personality', false) . '>شخصیت‌شناسی/MBTI</option>
              </select>
              <small>نوع آزمون روی سازنده سوالات اثر دارد.</small></div>';

        echo '<div class="ql-field"><label><b>تایمر (ثانیه)</b></label>
              <input class="ql-control" type="number" name="ql_time_limit" value="' . esc_attr($time_limit) . '" min="0" />
              <small>۰ یعنی بدون تایمر.</small></div>';

        echo '<div class="ql-field"><label><b>دسترسی</b></label>
              <label class="ql-check"><input type="checkbox" name="ql_login_required" value="1" ' . checked($login_required, 1, false) . ' /> فقط کاربران لاگین‌شده</label>
              <small>اگر فعال باشد، مهمان‌ها نمی‌توانند آزمون را شروع کنند.</small></div>';
        echo '</div></div>';

        echo '<div class="ql-admin-card" style="margin-top:12px;" id="ql-personality-settings">';
        echo '<h3 style="margin:0 0 10px;">تنظیمات شخصیت‌شناسی/MBTI</h3>';
        echo '<div class="ql-field"><label><b>لیست Type ها (با کاما جدا کن)</b></label>
              <input class="ql-control" type="text" name="ql_personality_types" value="' . esc_attr($types_raw) . '" placeholder="مثلاً: INTJ, INTP, ENFP, ..." />
              <small>بعد از ذخیره، در سازنده سوالات برای هر گزینه امتیاز هر Type نمایش داده می‌شود.</small></div>';

        $types = ql_parse_types($types_raw);
        if (!empty($types)) {
            echo '<div class="ql-field" style="margin-top:10px;"><label><b>ریدایرکت نتیجه</b></label>
                  <small>برای هر Type یک برگه انتخاب کن (یا خالی بذار).</small></div>';
            echo '<div class="ql-grid" style="grid-template-columns: repeat(3, minmax(0, 1fr));">';
            foreach ($types as $t) {
                $selected_page = $redirects[$t] ?? '';
                echo '<div class="ql-field"><label><b>' . esc_html($t) . '</b></label>';
                wp_dropdown_pages([
                    'name' => 'ql_redirect_page[' . esc_attr($t) . ']',
                    'show_option_none' => '— بدون ریدایرکت —',
                    'option_none_value' => '',
                    'selected' => is_numeric($selected_page) ? (int)$selected_page : 0,
                ]);
                echo '</div>';
            }
            echo '</div>';
        } else {
            echo '<div class="ql-empty" style="margin-top:10px;">برای ریدایرکت، اول Type ها را وارد کن و ذخیره کن.</div>';
        }

        echo '</div>';
    }

    public static function metabox_questions($post) {
        $questions = get_post_meta($post->ID, '_ql_questions', true);
        $questions = is_array($questions) ? $questions : [];
        $meta = ql_get_quiz_meta($post->ID);

        echo '<div id="ql-questions-builder" class="ql-builder"
              data-type="' . esc_attr($meta['type'] ?? 'normal') . '"
              data-types="' . esc_attr(wp_json_encode($meta['personality_types'] ?? [])) . '"
              data-questions="' . esc_attr(wp_json_encode($questions)) . '"></div>';

        echo '<input type="hidden" id="ql_questions_input" name="ql_questions_json" value="' . esc_attr(wp_json_encode($questions)) . '" />';
    }

    public static function save_quiz_meta($post_id, $post) {
        if (!isset($_POST['ql_nonce']) || !wp_verify_nonce($_POST['ql_nonce'], 'ql_save_quiz')) return;
        if (!current_user_can('edit_post', $post_id)) return;

        $type = isset($_POST['ql_type']) ? sanitize_text_field($_POST['ql_type']) : 'normal';
        update_post_meta($post_id, '_ql_type', $type);

        $time_limit = isset($_POST['ql_time_limit']) ? (int)$_POST['ql_time_limit'] : 0;
        update_post_meta($post_id, '_ql_time_limit', max(0, $time_limit));

        $login_required = ql_sanitize_bool($_POST['ql_login_required'] ?? 0);
        update_post_meta($post_id, '_ql_login_required', $login_required);

        $types_raw = isset($_POST['ql_personality_types']) ? sanitize_text_field($_POST['ql_personality_types']) : '';
        update_post_meta($post_id, '_ql_personality_types', $types_raw);

        $redirect_pages = [];
        if (isset($_POST['ql_redirect_page']) && is_array($_POST['ql_redirect_page'])) {
            foreach ($_POST['ql_redirect_page'] as $t => $pid) {
                $t = sanitize_text_field($t);
                $pid = (int)$pid;
                if ($pid > 0) $redirect_pages[$t] = $pid;
            }
        }
        update_post_meta($post_id, '_ql_personality_redirects', $redirect_pages);

        $raw = wp_unslash($_POST['ql_questions_json'] ?? '');
        $arr = json_decode($raw, true);
        if (!is_array($arr)) $arr = [];

        $types = ql_parse_types($types_raw);

        $clean = [];
        foreach ($arr as $q) {
            $title = isset($q['title']) ? sanitize_text_field($q['title']) : '';
            if (!$title) continue;

            $options = isset($q['options']) && is_array($q['options']) ? $q['options'] : [];
            $correct = isset($q['correct']) ? (int)$q['correct'] : -1;

            if ($type === 'personality') {
                $objs = [];
                foreach ($options as $opt) {
                    $label = '';
                    $scores2 = [];
                    if (is_array($opt)) {
                        $label = sanitize_text_field($opt['label'] ?? '');
                        $scores = isset($opt['scores']) && is_array($opt['scores']) ? $opt['scores'] : [];
                        foreach ($scores as $k => $v) {
                            $k = sanitize_text_field((string)$k);
                            if (!in_array($k, $types, true)) continue;
                            $scores2[$k] = (int)$v;
                        }
                    } else {
                        $label = sanitize_text_field((string)$opt);
                    }
                    $label = trim($label);
                    if (!$label) continue;
                    $objs[] = ['label' => $label, 'scores' => $scores2];
                }
                if (count($objs) < 2) continue;
                $clean[] = ['title' => $title, 'options' => $objs, 'correct' => -1];
            } else {
                $opts = [];
                foreach ($options as $opt) {
                    $label = is_array($opt) ? (string)($opt['label'] ?? '') : (string)$opt;
                    $label = trim(sanitize_text_field($label));
                    if ($label !== '') $opts[] = $label;
                }
                if (count($opts) < 2) continue;
                $clean[] = ['title' => $title, 'options' => $opts, 'correct' => ($type === 'normal') ? $correct : -1];
            }
        }

        update_post_meta($post_id, '_ql_questions', $clean);
    }

    public static function shortcode($atts) {
        $atts = shortcode_atts(['id' => 0], $atts);
        $id = (int)$atts['id'];
        if (!$id) return '<div class="ql-alert ql-alert--error">شناسه آزمون مشخص نیست.</div>';

        return '<div class="ql-wrap" data-quiz-id="' . esc_attr($id) . '">
            <div class="ql-card">
              <div class="ql-top">
                <div class="ql-title"></div>
                <div class="ql-meta">
                  <div class="ql-pill ql-pill--timer" style="display:none;"><span class="ql-timer"></span></div>
                  <div class="ql-pill ql-pill--step"><span class="ql-step"></span></div>
                </div>
              </div>
              <div class="ql-progress"><div class="ql-progress-bar"></div></div>
              <div class="ql-body"></div>
              <div class="ql-actions">
                <button class="ql-btn ql-btn--ghost ql-prev" type="button">قبلی</button>
                <button class="ql-btn ql-next" type="button">بعدی</button>
                <button class="ql-btn ql-finish" type="button" style="display:none;">ثبت و پایان</button>
              </div>
              <div class="ql-result" style="display:none;"></div>
            </div>
          </div>';
    }

    public static function enqueue_front_assets() {
        if (!is_singular()) return;
        $post = get_post();
        if (!$post) return;
        if (stripos($post->post_content ?? '', '[quiz_lab') === false) return;

        wp_register_style('quiz-lab-front', QL_URL . 'assets/front.css', [], QL_VERSION);
        wp_register_script('quiz-lab-front', QL_URL . 'assets/front.js', [], QL_VERSION, true);

        wp_enqueue_style('quiz-lab-front');
        wp_enqueue_script('quiz-lab-front');

        wp_localize_script('quiz-lab-front', 'QL', [
            'rest' => esc_url_raw(rest_url('quiz-lab/v1')),
            'nonce' => wp_create_nonce('wp_rest'),
            'is_logged_in' => is_user_logged_in() ? 1 : 0,
            'strings' => [
                'login_required' => 'برای شرکت در این آزمون باید وارد حساب شوید.',
                'start_error' => 'شروع آزمون ناموفق بود.',
                'unknown_error' => 'خطای نامشخص',
                'confirm_leave' => 'اگر صفحه را ببندید یا رفرش کنید، آزمون لغو می‌شود.',
                'time_over' => 'زمان آزمون تمام شد و پاسخ ثبت شد.',
                'select_option' => 'لطفاً یک گزینه انتخاب کن.',
                'redirecting' => 'در حال انتقال به صفحه نتیجه...',
            ],
        ]);
    }

    public static function enqueue_admin_assets($hook) {
        if ($hook !== 'post.php' && $hook !== 'post-new.php' && $hook !== 'toplevel_page_quiz-lab' && $hook !== 'quiz-lab_page_quiz-lab-results') return;

        wp_enqueue_style('quiz-lab-admin', QL_URL . 'assets/admin.css', [], QL_VERSION);

        $screen = function_exists('get_current_screen') ? get_current_screen() : null;
        if ($screen && $screen->post_type === 'ql_quiz') {
            wp_enqueue_script('quiz-lab-admin', QL_URL . 'assets/admin.js', [], QL_VERSION, true);
        }
    }
}
