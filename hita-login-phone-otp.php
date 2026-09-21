<?php

/**
 * Hita Shop - Login Phone OTP
 *
 * Generates a temporary OTP for phone authentication,
 * stores the hashed OTP in a transient,
 * and creates a temporary verification token.
 */

add_action(
    'jet-engine-booking/hita_login_phone_test_123',
    'hita_login_phone_otp_handler',
    10,
    3
);

function hita_login_phone_otp_handler( $data, $form, $notifications ) {

    if ( empty( $data['phone'] ) ) {
        return;
    }

    $phone = preg_replace( '/\D+/', '', $data['phone'] );

    if ( ! preg_match( '/^09\d{9}$/', $phone ) ) {
        return;
    }

    /*
     * Find user by WooCommerce billing_phone.
     */
    $users = get_users(
        array(
            'meta_key'   => 'billing_phone',
            'meta_value' => $phone,
            'number'     => 1,
            'fields'     => 'ID',
        )
    );

    $user_id = ! empty( $users ) ? (int) $users[0] : 0;

    /*
     * Generate a six-digit OTP.
     */
    $otp = (string) wp_rand( 100000, 999999 );

    /*
     * Store OTP as a hash.
     */
    $otp_hash = wp_hash_password( $otp );

    /*
     * Create a temporary token.
     */
    $verification_token = wp_generate_uuid4();

    /*
     * Store OTP data temporarily.
     */
    set_transient(
        'hita_otp_' . $verification_token,
        array(
            'phone'      => $phone,
            'user_id'    => $user_id,
            'otp_hash'   => $otp_hash,
            'attempts'   => 0,
            'created_at' => time(),
        ),
        5 * MINUTE_IN_SECONDS
    );

    /*
     * Store token for the verification step.
     */
    set_transient(
        'hita_phone_token_' . $phone,
        $verification_token,
        5 * MINUTE_IN_SECONDS
    );

    /*
     * Debug only.
     * Remove after connecting SMS panel.
     */
    $log = "===== HITA OTP GENERATED =====\n";
    $log .= "TIME: " . current_time( 'mysql' ) . "\n";
    $log .= "PHONE: " . $phone . "\n";
    $log .= "USER ID: " . $user_id . "\n";
    $log .= "OTP: " . $otp . "\n";
    $log .= "TOKEN: " . $verification_token . "\n";
    $log .= "==============================\n\n";

    file_put_contents(
        WP_CONTENT_DIR . '/hita-hook-debug.log',
        $log,
        FILE_APPEND
    );
}