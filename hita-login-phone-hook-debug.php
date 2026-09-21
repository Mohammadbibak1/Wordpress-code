<?php

/**
 * Hita Shop - Login Phone Hook Debug
 *
 * Logs Form 99 hook execution and submitted form data.
 */

add_action(
    'jet-engine-booking/hita_login_phone_test_123',
    'hita_login_phone_test_123_handler',
    10,
    3
);

function hita_login_phone_test_123_handler( $data, $form, $notifications ) {

    $log = "===== HITA HOOK FIRED =====\n";
    $log .= "TIME: " . current_time( 'mysql' ) . "\n";
    $log .= "FORM: " . print_r( $form, true ) . "\n";
    $log .= "DATA: " . print_r( $data, true ) . "\n";
    $log .= "============================\n\n";

    file_put_contents(
        WP_CONTENT_DIR . '/hita-hook-debug.log',
        $log,
        FILE_APPEND
    );
}