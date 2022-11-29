<?php

add_action( 'wooznd_wallet_created', 'wznd_wallet_created', 10, 2 );

function wznd_wallet_created( $account_id ) {
    global $wooznd_wallet;
    $wooznd_wallet = WooZndWalletAccountDB::GetAccount( $account_id );
    $subject = WooZndUtil::GetOption( 'new_wallet_mail_subject', esc_html__( 'Your new wallet has been created', 'wooznd-smartpack' ) );
    $message = WooZndUtil::GetOption( 'new_wallet_mail_message', wp_kses_post( __( 'Hi [wznd_wallet_name], <br /> Your new wallet has been created, you can deposit any amount into your wallet and later use this funds to purchase product & services on our website.', 'wooznd-smartpack' ) ) );
    WooZndUtil::SendMail( $wooznd_wallet[ 'email' ], do_shortcode( $subject ), do_shortcode( $message ) );
}

add_action( 'wooznd_wallet_rewarded', 'wznd_wallet_rewarded', 10, 2 );

function wznd_wallet_rewarded( $account_id, $transaction_id ) {
    global $wooznd_wallet, $wooznd_transaction;
    $wooznd_transaction = WooZndWalletTransactionDB::GetTransaction( $transaction_id );
    $wooznd_wallet = WooZndWalletAccountDB::GetAccount( $account_id );

    $subject = WooZndUtil::GetOption( 'new_wallet_reward_mail_subject', esc_html__( 'New Wallet Reward', 'wooznd-smartpack' ) );
    $message = WooZndUtil::GetOption( 'new_wallet_reward_mail_message', wp_kses_post( __( 'Hi [wznd_wallet_name], <br /> Your new wallet has been credited with [wznd_trans_credit] as part of our on going promo, your new wallet balance is [wznd_wallet_current].', 'wooznd-smartpack' ) ) );
    WooZndUtil::SendMail( $wooznd_wallet[ 'email' ], do_shortcode( $subject ), do_shortcode( $message ) );
}

add_action( 'wooznd_wallet_admin_transaction_credit_processed', 'wznd_wallet_admin_transaction_processed', 10, 3 );
add_action( 'wooznd_wallet_admin_transaction_debit_processed', 'wznd_wallet_admin_transaction_processed', 10, 3 );
add_action( 'wooznd_wallet_admin_transaction_deposit_processed', 'wznd_wallet_admin_transaction_processed', 10, 3 );
add_action( 'wooznd_wallet_admin_transaction_withdrawal_processed', 'wznd_wallet_admin_transaction_processed', 10, 3 );
add_action( 'wooznd_wallet_admin_transaction_payment_processed', 'wznd_wallet_admin_transaction_processed', 10, 3 );
add_action( 'wooznd_wallet_admin_transaction_bill_processed', 'wznd_wallet_admin_transaction_processed', 10, 3 );
add_action( 'wooznd_wallet_admin_transaction_refund_processed', 'wznd_wallet_admin_transaction_processed', 10, 3 );
add_action( 'wooznd_wallet_admin_transaction_transfer_processed', 'wznd_wallet_admin_transaction_processed', 10, 3 );

function wznd_wallet_admin_transaction_processed( $transaction_id, $account_id, $issued_by ) {
    global $wooznd_wallet, $wooznd_transaction;
    $wooznd_transaction = WooZndWalletTransactionDB::GetTransaction( $transaction_id );
    $wooznd_wallet = WooZndWalletAccountDB::GetAccount( $account_id );

    $subject = WooZndUtil::GetOption( 'wallet_transactions_mail_subject', esc_html__( 'New Transactions: [wznd_trans_receipt]', 'wooznd-smartpack' ) );
    $message = WooZndUtil::GetOption( 'wallet_transactions_mail_message', wp_kses_post( __( 'Hi [wznd_wallet_name], <br /> A [wznd_trans_type] transaction ([wznd_trans_receipt]) has occured on your wallet, your new wallet balance is [wznd_wallet_current].', 'wooznd-smartpack' ) ) );
    WooZndUtil::SendMail( $wooznd_wallet[ 'email' ], do_shortcode( $subject ), do_shortcode( $message ) );
}

add_action( 'wooznd_wallet_admin_transaction_status_pending', 'wznd_wallet_admin_transaction_status', 10, 2 );
add_action( 'wooznd_wallet_admin_transaction_status_onhold', 'wznd_wallet_admin_transaction_status', 10, 2 );
add_action( 'wooznd_wallet_admin_transaction_status_processing', 'wznd_wallet_admin_transaction_status', 10, 2 );
add_action( 'wooznd_wallet_admin_transaction_status_completed', 'wznd_wallet_admin_transaction_status', 10, 2 );
add_action( 'wooznd_wallet_admin_transaction_status_cancelled', 'wznd_wallet_admin_transaction_status', 10, 2 );

function wznd_wallet_admin_transaction_status( $transaction_id, $account_id ) {
    global $wooznd_wallet, $wooznd_transaction;
    $wooznd_transaction = WooZndWalletTransactionDB::GetTransaction( $transaction_id );
    $wooznd_wallet = WooZndWalletAccountDB::GetAccount( $account_id );

    $subject = WooZndUtil::GetOption( 'wallet_transactions_status_mail_subject', esc_html__( 'Transactions [wznd_trans_receipt] status', 'wooznd-smartpack' ) );
    $message = WooZndUtil::GetOption( 'wallet_transactions_status_mail_message', wp_kses_post( __( 'Hi [wznd_wallet_name], <br /> A [wznd_trans_type] transaction ([wznd_trans_receipt]) is now [wznd_trans_status], your new wallet balance is [wznd_wallet_current].', 'wooznd-smartpack' ) ) );
    WooZndUtil::SendMail( $wooznd_wallet[ 'email' ], do_shortcode( $subject ), do_shortcode( $message ) );
}

add_action( 'wooznd_wallet_deposit_processed', 'wznd_wallet_deposit_processed', 10, 3 );

function wznd_wallet_deposit_processed( $transaction_id, $order_id, $account_id ) {
    global $wooznd_wallet, $wooznd_transaction, $wooznd_order;

    $wooznd_transaction = WooZndWalletTransactionDB::GetTransaction( $transaction_id );
    $wooznd_order = new WC_Order( $order_id );
    $wooznd_wallet = WooZndWalletAccountDB::GetAccount( $account_id );

    $subject = WooZndUtil::GetOption( 'wallet_deposit_mail_subject', esc_html__( 'New Funds Deposit', 'wooznd-smartpack' ) );
    $message = WooZndUtil::GetOption( 'wallet_deposit_mail_message', wp_kses_post( __( 'Hi [wznd_wallet_name], <br /> Your wallet has been credited with [wznd_trans_credit] funds deposit, your new wallet balance is [wznd_wallet_current].', 'wooznd-smartpack' ) ) );
    WooZndUtil::SendMail( $wooznd_wallet[ 'email' ], do_shortcode( $subject ), do_shortcode( $message ) );
}
