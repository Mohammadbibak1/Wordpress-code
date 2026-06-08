// حذف فیلد ایمیل، وبسایت و ذخیره اطلاعات
function custom_comment_fields($fields) {

    unset($fields['email']);
    unset($fields['url']);
    unset($fields['cookies']);

    return $fields;
}
add_filter('comment_form_default_fields', 'custom_comment_fields');
// حذف فیلدهای ایمیل، وبسایت و ذخیره اطلاعات
function custom_remove_comment_fields($fields) {

    unset($fields['email']);
    unset($fields['url']);
    unset($fields['cookies']);

    return $fields;
}
add_filter('comment_form_default_fields', 'custom_remove_comment_fields');


// غیرفعال کردن اجباری بودن ایمیل
add_filter('pre_option_require_name_email', '__return_false');


// جلوگیری از خطای اعتبارسنجی ایمیل
function custom_disable_comment_email_check($commentdata) {

    if (!isset($commentdata['comment_author_email'])) {
        $commentdata['comment_author_email'] = '';
    }

    return $commentdata;
}
add_filter('preprocess_comment', 'custom_disable_comment_email_check');