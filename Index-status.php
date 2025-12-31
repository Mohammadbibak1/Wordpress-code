<?php
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
