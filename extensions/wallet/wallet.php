<?php

include_once('inc/wallet-activate.php');
include_once('inc/email.php');
include_once('inc/class-util.php');
include_once('inc/class-account-db.php');
include_once('inc/class-transaction-db.php');
include_once('shortcodes.php');
include_once('inc/payment-gateway.php');
include_once('inc/class-wallet.php');
include_once('inc/partial-payment.php');
include_once('inc/wallet_settings.php');
include_once('front-end/pages.php');
include_once('widgets/widgets.php');

add_action('woocommerce_init', 'WooZendWallet::Init');
add_action('woocommerce_init', 'WooZendWalletPartialPayment::Init');

//Admin Menu
add_action("admin_menu", 'wznd_wallet_addmenu');

function wznd_wallet_addmenu() {

    wooznd_woowallet_credit_wallet();
    wooznd_woowallet_debit_wallet();
    wooznd_woowallet_update_wallet();
    wooznd_woowallet_new_wallet();
    wooznd_update_transaction();
    wooznd_complete_transaction();
    wooznd_cancel_transaction();

    $capability = 'manage_woocommerce';
    $wallets_slug = 'wznd-wallet';

    $trans_pending_count = WooZndWalletTransactionDB::GetTransactionsCount(WOOZND_WALLET_TRANSANCTION_STATUS_PENDING);
    $trans_pending_text = ($trans_pending_count > 0) ? ' <span class="awaiting-mod update-plugins count-1"><span class="processing-count">' . $trans_pending_count . '</span></span>' : '';

    add_menu_page(esc_html__('All Wallets', 'wooznd-smartpack'), esc_html__('Wallets', 'wooznd-smartpack'), $capability, $wallets_slug, 'wznd_wallet_all_accounts', 'dashicons-money');
    add_submenu_page($wallets_slug, esc_html__('All Wallets', 'wooznd-smartpack'), esc_html__('All Wallets', 'wooznd-smartpack'), $capability, $wallets_slug);
    add_submenu_page($wallets_slug, esc_html__('Transactions', 'wooznd-smartpack'), esc_html__('Transactions', 'wooznd-smartpack') . $trans_pending_text, $capability, $wallets_slug . '-trans', 'wznd_wallet_all_transactions');
}

function wznd_wallet_all_accounts() {
    $wooznd_nonce_action = basename(__FILE__);
    include dirname(__FILE__) . '/admin/templates/wallet-list.php';
}

function wznd_wallet_all_transactions() {
    $wooznd_nonce_action = basename(__FILE__);
    include dirname(__FILE__) . '/admin/templates/transactions-list.php';
}

// Wallet Functions
function wooznd_woowallet_credit_wallet() {

    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['action_name']) && $_POST['action_name'] == 'credit') {

        $is_valid_nonce = ( isset($_POST['wznd_wallet_nonce']) && wp_verify_nonce($_POST['wznd_wallet_nonce'], basename(__FILE__)) ) ? true : false;
        if (!$is_valid_nonce) {
            return;
        }

        $issued_by = wp_get_current_user()->user_login;

        $status = sanitize_text_field($_POST['status']);

        $trans_id = WooZndWalletTransactionDB::CreditWallet(sanitize_text_field($_POST['user_id']), sanitize_text_field($_POST['amount']), absint($_POST['transtype']), $issued_by, sanitize_text_field($_POST['remark']));

        if ($trans_id > 0) {
            if ($status == WOOZND_WALLET_TRANSANCTION_STATUS_ONHOLD) {
                WooZndWalletTransactionDB::TransactionOnHold($trans_id, '', $_POST['remark']);
            }

            if ($status == WOOZND_WALLET_TRANSANCTION_STATUS_PROCESSING) {
                WooZndWalletTransactionDB::TransactionProcessing($trans_id, '', $_POST['remark']);
            }

            if ($status == WOOZND_WALLET_TRANSANCTION_STATUS_COMPLETED) {
                WooZndWalletTransactionDB::TransactionComplete($trans_id, $issued_by, $_POST['remark']);
            }

            do_action('wooznd_wallet_admin_transaction_' . WalletUtil::TransactionTypeString($_POST['transtype']) . '_processed', $trans_id, sanitize_text_field($_POST['user_id']), $issued_by);
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php echo esc_html__('Done!', 'wooznd-smartpack'); ?></p>
            </div>
            <?php
        } else {
            ?>
            <div class="notice notice-error is-dismissible">
                <p><?php echo esc_html__('Error!', 'wooznd-smartpack'); ?></p>
            </div>
            <?php
        }
    }
}

function wooznd_woowallet_debit_wallet() {
    if (!isset($_POST['action_name'])) {
        return;
    }
    if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['action_name'] == 'debit') {

        $is_valid_nonce = ( isset($_POST['wznd_wallet_nonce']) && wp_verify_nonce($_POST['wznd_wallet_nonce'], basename(__FILE__)) ) ? true : false;
        if (!$is_valid_nonce) {
            return;
        }

        $issued_by = wp_get_current_user()->user_login;

        $status = sanitize_text_field($_POST['status']);

        $trans_id = WooZndWalletTransactionDB::DebitWallet(sanitize_text_field($_POST['user_id']), sanitize_text_field($_POST['amount']), absint($_POST['transtype']), $issued_by, sanitize_text_field($_POST['remark']));

        if ($trans_id > 0) {
            if ($status == WOOZND_WALLET_TRANSANCTION_STATUS_ONHOLD) {
                WooZndWalletTransactionDB::TransactionOnHold($trans_id, '', sanitize_text_field($_POST['remark']));
            }

            if ($status == WOOZND_WALLET_TRANSANCTION_STATUS_PROCESSING) {
                WooZndWalletTransactionDB::TransactionProcessing($trans_id, '', sanitize_text_field($_POST['remark']));
            }

            if ($status == WOOZND_WALLET_TRANSANCTION_STATUS_COMPLETED) {
                WooZndWalletTransactionDB::TransactionComplete($trans_id, $issued_by, sanitize_text_field($_POST['remark']));
            }

            do_action('wooznd_wallet_admin_transaction_' . WalletUtil::TransactionTypeString($_POST['transtype']) . '_processed', $trans_id, sanitize_text_field($_POST['user_id']), $issued_by);
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php echo esc_html__('Done!', 'wooznd-smartpack'); ?></p>
            </div>
            <?php
        } else {
            ?>
            <div class="notice notice-error is-dismissible">
                <p><?php echo esc_html__('Error!', 'wooznd-smartpack'); ?></p>
            </div>
            <?php
        }
    }
}

function wooznd_woowallet_update_wallet() {
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['action_name']) && $_POST['action_name'] == 'update') {

        $is_valid_nonce = ( isset($_POST['wznd_wallet_nonce']) && wp_verify_nonce($_POST['wznd_wallet_nonce'], basename(__FILE__)) ) ? true : false;
        if (!$is_valid_nonce) {
            return;
        }
        $user_id = sanitize_text_field($_POST['user_id']);
        if (isset($_POST['delete']) && $_POST['delete'] == 'Delete') {
            WooZndWalletAccountDB::DeleteAccount($user_id);
            return;
        }


        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name = sanitize_text_field($_POST['last_name']);
        $ledger = sanitize_text_field($_POST['ledger_balance']);
        $current = sanitize_text_field($_POST['current_balance']);
        $spent = sanitize_text_field($_POST['total_spent']);
        $locked = sanitize_text_field($_POST['locked']);
        $remark = sanitize_text_field($_POST['remark']);

        if (WooZndWalletAccountDB::UpdateWallet($user_id, $first_name, $last_name, $ledger, $current, $spent, $locked, $remark) == true) {
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php echo esc_html__('Done!', 'wooznd-smartpack'); ?></p>
            </div>
            <?php
        } else {
            ?>
            <div class="notice notice-error is-dismissible">
                <p><?php echo esc_html__('Error!', 'wooznd-smartpack'); ?></p>
            </div>
            <?php
        }
    }
}

function wooznd_woowallet_new_wallet() {
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['action_name']) && $_POST['action_name'] == 'addnew') {
        $is_valid_nonce = ( isset($_POST['wznd_wallet_nonce']) && wp_verify_nonce($_POST['wznd_wallet_nonce'], basename(__FILE__)) ) ? true : false;
        if (!$is_valid_nonce) {
            return;
        }

        $n_user = get_user_by('login', sanitize_text_field($_POST['username']));

        if ($n_user == false) {
            $n_user = get_user_by('email', sanitize_text_field($_POST['username']));
        }

        if ($n_user != false) {
            if (!WooZndWalletAccountDB::AccountExists($n_user->ID)) {

                $first_name = $n_user->first_name;
                $last_name = $n_user->user_lastname;
                $ledger_balance = !empty(sanitize_text_field($_POST['ledger_balance'])) ? sanitize_text_field($_POST['ledger_balance']) : 0;
                $current_balance = !empty(sanitize_text_field($_POST['current_balance'])) ? sanitize_text_field($_POST['current_balance']) : 0;
                $total_spent = !empty(sanitize_text_field($_POST['total_spent'])) ? sanitize_text_field($_POST['total_spent']) : 0;
                $locked = sanitize_text_field($_POST['locked']);
                $remark = sanitize_text_field($_POST['remark']);
                $email = $n_user->user_email;

                if (WooZndWalletAccountDB::CreateAccount($n_user->ID, $first_name, $last_name, $email, $remark) == true) {
                    WooZndWalletAccountDB::UpdateWallet($n_user->ID, $first_name, $last_name, $ledger_balance, $current_balance, $total_spent, $locked, $remark);
                    ?>
                    <div class="notice notice-success is-dismissible">
                        <p><?php echo esc_html__('Done!', 'wooznd-smartpack'); ?></p>
                    </div>
                    <?php
                } else {
                    ?>
                    <div class="notice notice-error is-dismissible">
                        <p><?php echo esc_html__('error!', 'wooznd-smartpack'); ?></p>
                    </div>
                    <?php
                }
            } else {
                ?>
                <div class="notice notice-error is-dismissible">
                    <p><?php echo esc_html__('A wallet account for this user already exist!', 'wooznd-smartpack'); ?></p>
                </div>
                <?php
            }
        } else {
            ?>
            <div class="notice notice-error is-dismissible">
                <p><?php echo esc_html__('I can NOT create a wallet account for unregistered users!', 'wooznd-smartpack'); ?></p>
            </div>
            <?php
        }
    }
}

//Transactions Functions
function wooznd_update_transaction() {
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['action_name']) && $_POST['action_name'] == 'update-transaction') {
        $is_valid_nonce = ( isset($_POST['wznd_wallet_nonce']) && wp_verify_nonce($_POST['wznd_wallet_nonce'], basename(__FILE__)) ) ? true : false;
        if (!$is_valid_nonce) {
            return;
        }
        $trans_id = !empty(sanitize_text_field($_POST['trans_id'])) ? sanitize_text_field($_POST['trans_id']) : 0;
        if (isset($_POST['delete']) && $_POST['delete'] == 'Delete') {
            WooZndWalletTransactionDB::DeleteTransaction($trans_id);
            return;
        }

        $user_login = wp_get_current_user()->user_login;
        $account_id = !empty(sanitize_text_field($_POST['user_id'])) ? sanitize_text_field($_POST['user_id']) : 0;
        $status = !empty(sanitize_text_field($_POST['status'])) ? sanitize_text_field($_POST['status']) : WOOZND_WALLET_TRANSANCTION_STATUS_PENDING;
        $remark = !empty(sanitize_text_field($_POST['remark'])) ? sanitize_text_field($_POST['remark']) : '';

        $result = false;

        if ($status == WOOZND_WALLET_TRANSANCTION_STATUS_PENDING) {
            $result = WooZndWalletTransactionDB::TransactionPending($trans_id, $remark);
        }

        if ($status == WOOZND_WALLET_TRANSANCTION_STATUS_PROCESSING) {
            $result = WooZndWalletTransactionDB::TransactionProcessing($trans_id, $remark);
        }

        if ($status == WOOZND_WALLET_TRANSANCTION_STATUS_ONHOLD) {
            $result = WooZndWalletTransactionDB::TransactionOnHold($trans_id, $remark);
        }

        if ($status == WOOZND_WALLET_TRANSANCTION_STATUS_COMPLETED) {
            $result = WooZndWalletTransactionDB::TransactionComplete($trans_id, $user_login, $remark);
        }

        if ($status == WOOZND_WALLET_TRANSANCTION_STATUS_CANCELLED) {
            $result = WooZndWalletTransactionDB::TransactionCancel($trans_id, $user_login, $remark);
        }
        $status_text = WalletUtil::TransactionStatusString($status, true);
        if ($result == true) {
            do_action('wooznd_wallet_admin_transaction_status_' . $status_text, $trans_id, $account_id, $user_login);
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php echo esc_html__('Done!', 'wooznd-smartpack'); ?></p>
            </div>
            <?php
        }
    }
}

function wooznd_complete_transaction() {
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['action_name']) && $_POST['action_name'] == 'complete-transaction') {

        $is_valid_nonce = ( isset($_POST['wznd_wallet_nonce']) && wp_verify_nonce($_POST['wznd_wallet_nonce'], basename(__FILE__)) ) ? true : false;
        if (!$is_valid_nonce) {
            return;
        }

        $user_login = wp_get_current_user()->user_login;
        $trans_id = !empty(sanitize_text_field($_POST['trans_id'])) ? sanitize_text_field($_POST['trans_id']) : 0;
        $account_id = !empty(sanitize_text_field($_POST['user_id'])) ? sanitize_text_field($_POST['user_id']) : 0;

        if (WooZndWalletTransactionDB::TransactionComplete($trans_id, $user_login)) {
            do_action('wooznd_wallet_admin_transaction_status_completed', $trans_id, $account_id, $user_login);
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php echo esc_html__('Done!', 'wooznd-smartpack'); ?></p>
            </div>
            <?php
        }
    }
}

function wooznd_cancel_transaction() {
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['action_name']) && $_POST['action_name'] == 'cancel-transaction') {

        $is_valid_nonce = ( isset($_POST['wznd_wallet_nonce']) && wp_verify_nonce($_POST['wznd_wallet_nonce'], basename(__FILE__)) ) ? true : false;
        if (!$is_valid_nonce) {
            return;
        }

        $user_login = wp_get_current_user()->user_login;
        $trans_id = !empty(sanitize_text_field($_POST['trans_id'])) ? sanitize_text_field($_POST['trans_id']) : 0;
        $account_id = !empty(sanitize_text_field($_POST['user_id'])) ? sanitize_text_field($_POST['user_id']) : 0;

        if (WooZndWalletTransactionDB::TransactionCancel($trans_id, $user_login)) {
            do_action('wooznd_wallet_admin_transaction_status_cancelled', $trans_id, $account_id, $user_login);
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php echo esc_html__('Done!', 'wooznd-smartpack'); ?></p>
            </div>
            <?php
        }
    }
}
