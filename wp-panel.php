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
