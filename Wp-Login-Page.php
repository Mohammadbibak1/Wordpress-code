/**
 * login-Page
 */

function custom_login_background() {
    ?>
    <style type="text/css">
        body.login {
            background-image: url('#');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-color: #f0f0f0; /* اگر تصویر لود نشود */
        }
    </style>
    <?php
}
add_action( 'login_enqueue_scripts', 'custom_login_background' );

function custom_login_logo() {
    ?>
    <style type="text/css">
        #login h1 a {
            background-image: url('#');
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