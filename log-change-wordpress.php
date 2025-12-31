function custom_login_logo() {
    ?>
    <style type="text/css">
        #login h1 a {
            background-image: url('https://mohammadbibak1880.ir/wp-content/uploads/2025/01/cropped-Remove-bg.ai_1735897561880-min.webp');
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
