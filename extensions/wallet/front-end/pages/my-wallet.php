<?php

if (!WooZndWalletAccountDB::AccountExists(get_current_user_id())) {
    return;
}


$wallet_title = esc_html__('My Wallet', 'wooznd-smartpack');
$ledger_label = esc_html__('Ledger Balance:', 'wooznd-smartpack');
$current_label = esc_html__('Current Balance:', 'wooznd-smartpack');
$total_spent_label = esc_html__('Total Spent:', 'wooznd-smartpack');
include 'template/wallet.php';


$deposit_title = esc_html__('Wallet Deposit', 'wooznd-smartpack');
$placeholder = esc_html__('Enter Amount', 'wooznd-smartpack');
$button_text = esc_html__('Deposit', 'wooznd-smartpack');
include 'template/deposit.php';


$transaction_title = esc_html__('Transactions', 'wooznd-smartpack');
$page_size = 10;
include 'template/transactions.php';
