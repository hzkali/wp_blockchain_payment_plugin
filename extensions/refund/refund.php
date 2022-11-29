<?php

include_once ('inc/class-refund-settings.php');
if (WooZndUtil::GetOption('enable_refund', 'yes') == 'yes') {
    include_once ('inc/refund-activate.php');
    include_once ('inc/class-refund-db.php');
    include_once ('inc/class-refund.php');


    add_action('woocommerce_init', 'WooZndRefund::Init');

    //Admin Menu
    add_action("admin_menu", 'wznd_refund_addmenu');

    function wznd_refund_addmenu() {

        wooznd_refund_request_update();
        wooznd_refund_request_approve();
        wooznd_refund_request_reject();


        $capability = 'manage_woocommerce';
        $wallets_slug = 'wznd-wallet';

        $request_pending_count = WooZndRefundDB::GetRequestsCount('', WOOZND_WALLET_REFUND_REQUEST_PENDING);
        $request_pending_text = ($request_pending_count > 0) ? ' <span class="awaiting-mod update-plugins count-1"><span class="processing-count">' . $request_pending_count . '</span></span>' : '';
        add_submenu_page($wallets_slug, esc_html__('Refund Requests', 'wooznd-smartpack'), esc_html__('Refund Requests', 'wooznd-smartpack') . $request_pending_text, $capability, $wallets_slug . '-refunds', 'wznd_refund_list');
    }

    function wznd_refund_list() {
        $wooznd_nonce_action = basename(__FILE__);
        include ('admin/templates/refunds-list.php');
    }

    function wooznd_refund_request_update() {
        if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['action_name']) && $_POST['action_name'] == 'update-refund') {

            $is_valid_nonce = ( isset($_POST['wznd_refund_nonce']) && wp_verify_nonce($_POST['wznd_refund_nonce'], basename(__FILE__)) ) ? true : false;
            if (!$is_valid_nonce) {
                return;
            }

            $req_id = sanitize_text_field($_POST['refund_id']);

            if (isset($_POST['delete']) && $_POST['delete'] == 'Delete') {
                WooZndRefundDB::DeleteRequest($req_id);
                return;
            }

            $status = sanitize_text_field($_POST['status']);

            if ($status == WOOZND_WALLET_REFUND_REQUEST_APROVED) {
                $req_amount = sanitize_text_field($_POST['request_amount']);
                $reason = sanitize_text_field($_POST['reason']);


                $trans_id = WooZndRefundDB::ProcessRequest($req_id, $req_amount, $reason, false);
                if ($trans_id > 0) {
                    $woo_ver = WC()->version;
                    if ($woo_ver < "3.0.0" && $woo_ver < "2.7.0") {
                        $args = array(
                            'order_id' => $req_id,
                            'amount' => $req_amount,
                            'reason' => $reason,
                            'refund_payment' => true,
                            'restock_items' => false,
                        );
                        wc_create_refund($args);
                    } else {
                        $order = new WC_Order($req_id);
                        wc_create_refund(
                                array('order_id' => $req_id,
                                    'amount' => $req_amount,
                                    'reason' => $reason,
                                    'date' => $order->get_date_modified()
                        ));
                    }
                }
            }

            if ($status == WOOZND_WALLET_REFUND_REQUEST_REJECTED) {
                WooZndRefundDB::CancelRequest($req_id);
            }
        }
    }

    function wooznd_refund_request_approve() {
        if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['action_name']) && $_POST['action_name'] == 'complete-refund') {

            $is_valid_nonce = ( isset($_POST['wznd_refund_nonce']) && wp_verify_nonce($_POST['wznd_refund_nonce'], basename(__FILE__)) ) ? true : false;
            if (!$is_valid_nonce) {
                return;
            }

            $req_id = sanitize_text_field($_POST['refund_id']);
            $req_amount = sanitize_text_field($_POST['request_amount']);

            $trans_id = WooZndRefundDB::ProcessRequest($req_id, $req_amount, '');
            if ($trans_id > 0) {
                $woo_ver = WC()->version;
                if ($woo_ver < "3.0.0" && $woo_ver < "2.7.0") {
                    $args = array(
                        'order_id' => $req_id,
                        'amount' => $req_amount,
                        'reason' => '',
                        'refund_payment' => true,
                        'restock_items' => false,
                    );
                    wc_create_refund($args);
                } else {
                    $order = new WC_Order($req_id);
                    wc_create_refund(
                            array('order_id' => $req_id,
                                'amount' => $req_amount,
                                'reason' => '',
                                'date' => $order->get_date_modified()
                    ));
                }
            }
        }
    }

    function wooznd_refund_request_reject() {
        if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['action_name']) && $_POST['action_name'] == 'cancel-refund') {
            $is_valid_nonce = ( isset($_POST['wznd_refund_nonce']) && wp_verify_nonce($_POST['wznd_refund_nonce'], basename(__FILE__)) ) ? true : false;

            if (!$is_valid_nonce) {
                return;
            }

            $req_id = sanitize_text_field($_POST['refund_id']);
            WooZndRefundDB::CancelRequest($req_id);
        }
    }

}


