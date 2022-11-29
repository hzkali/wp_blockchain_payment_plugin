<?php

$wooznd_nonce_action = basename(__FILE__);
wooznd_create_giftcard();
wooznd_update_giftcard();

$url_options = array();
if (isset($_GET['status'])) {
    $url_options['status'] = $_GET['status'];
}
if (isset($_GET['search'])) {
    $url_options['search'] = $_GET['search'];
}
if (isset($_GET['from'])) {
    $url_options['from'] = $_GET['from'];
}
if (isset($_GET['to'])) {
    $url_options['to'] = $_GET['to'];
}
if (isset($_GET['exp'])) {
    $url_options['exp'] = $_GET['exp'];
}
if (isset($_GET['orderby'])) {
    $url_options['orderby'] = $_GET['orderby'];
}
if (isset($_GET['order'])) {
    $url_options['order'] = $_GET['order'];
}

$url_format = admin_url('admin.php?page=wznd-manage-giftcard&pg={{page}}');

$default_url = admin_url('admin.php?page=wznd-manage-giftcard');

foreach ($url_options as $key => $value) {
    $url_format .= ('&' . $key . '={{' . $key . '}}');
    $default_url .= ('&' . $key . '={{' . $key . '}}');
}

$status = -1;
if (isset($_GET['status']) && $status != '') {
    $status = $_GET['status'];
}
$search = !empty($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
$orderby = !empty($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'event';
$order = !empty($_GET['order']) ? sanitize_text_field($_GET['order']) : 'desc';
$srch = !empty($_GET['search']) ? sanitize_text_field($_GET['search']) . '%' : '';
if (isset($_GET['exp'])) {
    $totals = WooZndGiftCardDB::GetExpiredGiftCardsCount($srch, $status);
} else {
    $totals = WooZndGiftCardDB::GetGiftCardsCount($srch, $status);
}


$pagesize = 25;
$pg = isset($_GET['pg']) ? $_GET['pg'] : 1;
$paging = new WooZndPaginator($totals, $pagesize, $pg);

if (isset($_GET['exp'])) {
    $rows = WooZndGiftCardDB::GetExpiredGiftCards($srch, $status, $paging->offset(), $paging->limit(), $orderby, $order);
} else {
    $rows = WooZndGiftCardDB::GetGiftCards($srch, $status, $paging->offset(), $paging->limit(), $orderby, $order);
}

function wooznd_create_giftcard() {
    global $wooznd_nonce_action;
    if (!isset($_POST['action_name'])) {
        return;
    }
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['action_name']) && $_POST['action_name'] == 'addgiftcard') {

        $is_valid_nonce = ( isset($_POST['wznd_giftcard_nonce']) && wp_verify_nonce($_POST['wznd_giftcard_nonce'], basename(__FILE__)) ) ? true : false;
        if (!$is_valid_nonce) {
            return;
        }
        WooZndUtil::UpdateOption('giftcard_buzy', 'yes');
        $attrs = WooZndGiftCardDB::GetAttributesFromSettings();


        $attrs['id'] = current_time('timestamp');
        $attrs['description'] = esc_html__('Gift Card Coupon', 'wooznd-smartpack');

        $attrs['product_categories'] = array();
        $attrs['excluded_product_categories'] = array();


        $attrs['discount_type'] = sanitize_text_field($_POST['discount_type']);
        $attrs['coupon_code'] = WooZndUtil::GenRandomPattern($attrs['coupon_pattern']);
        $attrs['amount'] = sanitize_text_field($_POST['amount']);
        $attrs['coupon_amount'] = sanitize_text_field($_POST['coupon_amount']);
        $attrs['apply_before_tax'] = (isset($_POST['apply_before_tax']) == 'yes') ? 'yes' : 'no';
        $attrs['free_shipping'] = (isset($_POST['free_shipping']) == 'yes') ? 'yes' : 'no';
        $attrs['send_date'] = sanitize_text_field($_POST['send_date']);
        $attrs['expiry_date'] = sanitize_text_field($_POST['expiry_date']);
        $attrs['minimum_amount'] = sanitize_text_field($_POST['minimum_amount']);
        $attrs['maximum_amount'] = sanitize_text_field($_POST['maximum_amount']);
        $attrs['exclude_sale_items'] = (isset($_POST['exclude_sale_items']) == 'yes') ? 'yes' : 'no';
        $attrs['individual_use'] = (isset($_POST['individual_use']) == 'yes') ? 'yes' : 'no';
        $attrs['usage_limit_per_user'] = sanitize_text_field($_POST['usage_limit_per_user']);
        $attrs['usage_limit'] = sanitize_text_field($_POST['usage_limit']);
        $attrs['pdf_template_id'] = sanitize_text_field($_POST['pdf_template_id']);
        $attrs['email_template_id'] = sanitize_text_field($_POST['email_template_id']);
        $attrs['message'] = sanitize_textarea_field($_POST['message']);
        $attrs['delivery_method'] = sanitize_text_field($_POST['delivery_method']);
        $attrs['sender_name'] = sanitize_text_field($_POST['sender_name']);
        $attrs['sender_email'] = sanitize_text_field($_POST['sender_email']);
        $attrs['receiver_name'] = sanitize_text_field($_POST['receiver_name']);
        $attrs['receiver_email'] = sanitize_text_field($_POST['receiver_email']);


        if (WooZndGiftCardDB::CreateGiftCard($attrs) == true) {
            
        }


        WooZndUtil::UpdateOption('giftcard_buzy', 'no');
    }
}

function wooznd_update_giftcard() {
    global $wooznd_nonce_action;
    if (!isset($_POST['action_name'])) {
        return;
    }
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['action_name']) && $_POST['action_name'] == 'editgiftcard') {

        $is_valid_nonce = ( isset($_POST['wznd_giftcard_nonce']) && wp_verify_nonce($_POST['wznd_giftcard_nonce'], basename(__FILE__)) ) ? true : false;
        if (!$is_valid_nonce) {
            return;
        }


        WooZndUtil::UpdateOption('giftcard_buzy', 'yes');
        if (isset($_POST['delete']) && $_POST['delete'] == 'Delete') {
            WooZndGiftCardDB::DeleteGiftCard(sanitize_text_field($_POST['giftcard_id']));
            WooZndUtil::UpdateOption('giftcard_buzy', 'no');
            return;
        }

        $attrs = array();


        $attrs['id'] = sanitize_text_field($_POST['giftcard_id']);
        $attrs['discount_type'] = sanitize_text_field($_POST['discount_type']);
        $attrs['amount'] = sanitize_text_field($_POST['amount']);
        $attrs['coupon_amount'] = sanitize_text_field($_POST['coupon_amount']);
        $attrs['coupon_code'] = sanitize_text_field($_POST['coupon_code']);
        $attrs['apply_before_tax'] = (isset($_POST['apply_before_tax']) == 'yes') ? 'yes' : 'no';
        $attrs['free_shipping'] = (isset($_POST['free_shipping']) == 'yes') ? 'yes' : 'no';
        $attrs['send_date'] = sanitize_text_field($_POST['send_date']);
        $attrs['expiry_date'] = sanitize_text_field($_POST['expiry_date']);
        $attrs['minimum_amount'] = sanitize_text_field($_POST['minimum_amount']);
        $attrs['maximum_amount'] = sanitize_text_field($_POST['maximum_amount']);
        $attrs['exclude_sale_items'] = (isset($_POST['exclude_sale_items']) == 'yes') ? 'yes' : 'no';
        $attrs['individual_use'] = (isset($_POST['individual_use']) == 'yes') ? 'yes' : 'no';
        $attrs['usage_limit_per_user'] = sanitize_text_field($_POST['usage_limit_per_user']);
        $attrs['usage_limit'] = sanitize_text_field($_POST['usage_limit']);
        $attrs['pdf_template_id'] = sanitize_text_field($_POST['pdf_template_id']);
        $attrs['email_template_id'] = sanitize_text_field($_POST['email_template_id']);
        $attrs['message'] = sanitize_textarea_field($_POST['message']);
        $attrs['delivery_method'] = sanitize_text_field($_POST['delivery_method']);
        $attrs['sender_name'] = sanitize_text_field($_POST['sender_name']);
        $attrs['sender_email'] = sanitize_text_field($_POST['sender_email']);
        $attrs['receiver_name'] = sanitize_text_field($_POST['receiver_name']);
        $attrs['receiver_email'] = sanitize_text_field($_POST['receiver_email']);
        $attrs['status'] = sanitize_text_field($_POST['status']);

        if (WooZndGiftCardDB::UpdateGiftCard($attrs) == true) {
            
        }

        WooZndUtil::UpdateOption('giftcard_buzy', 'no');
    }
}

function inc_util_giftcard_to_name($args) {
    if (!empty($args['to_name']) && !empty($args['to_email'])) {
        return $args['to_name'] . " (" . $args['to_email'] . ")";
    } else if (!empty($args['to_name'])) {
        return $args['to_name'];
    } else if (!empty($args['to_email'])) {
        return $args['to_email'];
    } else {
        return esc_html__('N/A', 'wooznd-smartpack');
    }
}
