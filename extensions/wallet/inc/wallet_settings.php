<?php

if (!defined('ABSPATH')) {
    exit;
}


if (!class_exists('WC_WooZndWallet')) {

    class WC_WooZndWallet {

        public static function init() {
            if (is_admin()) {
                //add settings tab
                add_filter('woocommerce_settings_tabs_array', array(new self(), 'settings_tabs_array'), 50);
                //show settings tab
                add_action('woocommerce_settings_tabs_wooznd_wallet', array(new self(), 'show_settings_tab'));
                //save settings tab
                add_action('woocommerce_update_options_wooznd_wallet', array(new self(), 'update_settings_tab'));

            }
        }

        public static function settings_tabs_array($settings_tabs) {
            $settings_tabs['wooznd_wallet'] = esc_html__('Wallet', 'wooznd-smartpack');
            return $settings_tabs;
        }

        public static function show_settings_tab() {
            woocommerce_admin_fields(self::get_settings());
        }

        public static function update_settings_tab() {

            if (isset($_POST['wooznd_deposit_product_id'])) {
                WooZndUtil::UpdateOption('deposit_product_id', $_POST['wooznd_deposit_product_id']);
            } else {
                delete_option('wooznd_deposit_product_id');
            }

            woocommerce_update_options(self::get_settings());
        }

        private static function get_settings() {
            $args = array(
                'role' => 'administrator',
                'orderby' => 'meta_key=first_name',
                'order' => 'ASC',
                'fields' => array('id', 'user_login')
            );

            $admin_users_db = (new WP_User_Query($args))->get_results();
            $admin_users = array();

            foreach ($admin_users_db as $user) {
                $admin_users[$user->id] = $user->user_login;
            }
            $deposit_p = array();

            $prod = WooZndUtil::GetOption('deposit_product_id', 0);
            if ($prod > 0) {
                $deposit_p = array(
                    $prod => WooZndUtil::GetFormattedProductName($prod, false)
                );
            }

            $d_prod = array(
                'name' => esc_html__('Deposit product', 'wooznd-smartpack'),
                'type' => "select",
                'class' => 'wc-product-search',
                'desc' => esc_html__('Select a product to use for funds deposit', 'wooznd-smartpack'),
                'desc_tip' => true,
                'placeholder' => esc_html__('Search for a product&hellip;', 'wooznd-smartpack'),
                'custom_attributes' => array(
                    'data-placeholder' => esc_html__('Search for a product&hellip;', 'wooznd-smartpack'),
                    'data-selected' => WooZndUtil::GetFormattedProductName(WooZndUtil::GetOption('deposit_product_id', 0), false),
                ),
                'options' => $deposit_p,
                'id' => 'wooznd_deposit_product_id'
            );
            $woo_ver = WC()->version;
            if ($woo_ver < "3.0.0" && $woo_ver < "2.7.0") {
                $d_prod = array(
                    'name' => esc_html__('Deposit product', 'wooznd-smartpack'),
                    'type' => "text",
                    'class' => 'wc-product-search',
                    'desc' => esc_html__('Select a product to use for funds deposit', 'wooznd-smartpack'),
                    'desc_tip' => true,
                    'custom_attributes' => array(
                        'data-multiple' => 'false',
                        'data-placeholder' => esc_html__('Search for a product&hellip;', 'wooznd-smartpack'),
                        'data-action' => 'woocommerce_json_search_products_and_variations',
                        'data-selected' => WooZndUtil::GetFormattedProductName(WooZndUtil::GetOption('deposit_product_id', 0), false),
                    ),
                    'id' => 'wooznd_deposit_product_id'
                );
            }

            $settings = array(
                //---------------
                // Wallet Settings
                //---------------
                'wooznd_wallet_section_title' => array(
                    'name' => esc_html__('Wallet Settings', 'wooznd-smartpack'),
                    'type' => 'title',
                    'desc' => '',
                    'id' => 'wooznd_wallet_section_title'
                ),
                'wooznd_auto_create_new_wallet' => array(
                    'title' => esc_html__('Auto create wallet', 'wooznd-smartpack'),
                    'desc' => esc_html__('Automatically create new wallet for users', 'wooznd-smartpack'),
                    'desc_tip' => true,
                    'type' => 'checkbox',
                    'default' => 'yes',
                    'id' => 'wooznd_auto_create_new_wallet'
                ),
                'wooznd_wallet_account_number_start' => array(
                    'title' => esc_html__('Account number start from', 'wooznd-smartpack'),
                    'type' => "text",
                    'css' => 'min-width:350px;',
                    'id' => 'wooznd_wallet_account_number_start'
                ),
                'wooznd_new_wallet_remark' => array(
                    'title' => esc_html__('New wallet remark', 'wooznd-smartpack'),
                    'type' => "text",
                    'default' => 'New Account',
                    'css' => 'min-width:350px;',
                    'id' => 'wooznd_new_wallet_remark'
                ),
                'wooznd_transactions_receipt_format' => array(
                    'title' => esc_html__('Receipt number format', 'wooznd-smartpack'),
                    'type' => "text",
                    'desc' => esc_html__('You can use the following variables: {{account_number}} and {{transaction_id}}', 'wooznd-smartpack'),
                    'desc_tip' => true,
                    'default' => 'TRX{{account_number}}{{transaction_id}}',
                    'css' => 'min-width:350px;',
                    'id' => 'wooznd_transactions_receipt_format'
                ),
                'wooznd_basicsettings_end' => array(
                    'type' => 'sectionend',
                    'id' => 'wooznd_basicsettings_end'
                ),
                //Funds Deposit
                'wooznd_funds_deposit_title' => array(
                    'name' => esc_html__('Funds Deposit', 'wooznd-smartpack'),
                    'type' => 'title',
                    'desc' => '',
                    'id' => 'wooznd_funds_deposit_title'
                ),
                'wooznd_deposit_product_id' => $d_prod,
                'wooznd_make_deposit_on_order_status' => array(
                    'title' => esc_html__('Complete deposit on order status change', 'wooznd-smartpack'),
                    'desc' => esc_html__('Choose when to make complete deposit order', 'wooznd-smartpack'),
                    'desc_tip' => true,
                    'type' => 'select',
                    'default' => 'processing',
                    'class' => 'wc-enhanced-select',
                    'css' => 'min-width:300px;',
                    'options' => array(
                        'on-hold' => esc_html__('On-Hold', 'wooznd-smartpack'),
                        'processing' => esc_html__('Processing', 'wooznd-smartpack'),
                        'completed' => esc_html__('Completed', 'wooznd-smartpack'),
                    ),
                    'id' => 'wooznd_make_deposit_on_order_status'
                ),
                'wooznd_wallet_deposit_remark' => array(
                    'title' => esc_html__('Deposit remark', 'wooznd-smartpack'),
                    'type' => "text",
                    'default' => 'Funds Deposit',
                    'css' => 'min-width:350px;',
                    'id' => 'wooznd_wallet_deposit_remark'
                ),
                'wooznd_deposit_payment_methods' => array(
                    'title' => esc_html__('Payment methods', 'wooznd-smartpack'),
                    'desc' => esc_html__('Choose which payment method can be use for funds deposit, leave this field blank to support all payment methods', 'wooznd-smartpack'),
                    'desc_tip' => true,
                    'type' => 'multiselect',
                    'default' => '',
                    'class' => 'wc-enhanced-select',
                    'css' => 'min-width:300px;',
                    'options' => WooZndUtil::GetPaymentMethodList(),
                    'custom_attributes' => array(
                        'data-placeholder' => esc_html__( 'Select payment methods&hellip;', 'wooznd-smartpack' ),
                    ),
                    'id' => 'wooznd_deposit_payment_methods',
                ),
                'wooznd_funds_deposit_end' => array(
                    'type' => 'sectionend',
                    'id' => 'wooznd_funds_deposit_end'
                ),
                //New Wallets Reward
                'wooznd_partial_payment_title' => array(
                    'name' => esc_html__('Partial Payment', 'wooznd-smartpack'),
                    'type' => 'title',
                    'desc' => '',
                    'id' => 'wooznd_partial_payment_title'
                ),
                'wooznd_enable_wallet_partial_payment' => array(
                    'title' => esc_html__('Enable/Disable', 'wooznd-smartpack'),
                    'desc' => esc_html__('Enable partial payment', 'wooznd-smartpack'),
                    'desc_tip' => true,
                    'type' => 'checkbox',
                    'default' => 'no',
                    'id' => 'wooznd_enable_wallet_partial_payment'
                ),
                'wooznd_wallet_partial_payment_text' => array(
                    'title' => esc_html__('Cart text', 'wooznd-smartpack'),
                    'type' => "text",
                    'default' => 'Wallet Funds',
                    'css' => 'min-width:350px;',
                    'id' => 'wooznd_wallet_partial_payment_text'
                ),
                'wooznd_wallet_partial_payment_min' => array(
                    'title' => esc_html__('Wallet funds threshold', 'wooznd-smartpack'),
                    'type' => "number",
                    'default' => '0',
                    'desc' => esc_html__('Controls the minimum wallet balance for partial payment to apply on cart', 'wooznd-smartpack'),
                    'desc_tip' => true,
                    'css' => 'min-width:350px;',
                    'custom_attributes' => array(
                        'min' => '0',
                        'step' => '1'
                    ),
                    'id' => 'wooznd_wallet_partial_payment_min'
                ),
                'wooznd_make_partial_payment_on_order_status' => array(
                    'title' => esc_html__('Complete payment on order status change', 'wooznd-smartpack'),
                    'desc' => esc_html__('Choose when to make complete partial payment order', 'wooznd-smartpack'),
                    'desc_tip' => true,
                    'type' => 'select',
                    'default' => 'on-hold',
                    'class' => 'wc-enhanced-select',
                    'css' => 'min-width:300px;',
                    'options' => array(
                        'completed' => esc_html__('Completed', 'wooznd-smartpack'),
                        'processing' => esc_html__('Processing', 'wooznd-smartpack'),
                        'on-hold' => esc_html__('On-Hold', 'wooznd-smartpack'),
                    ),
                    'id' => 'wooznd_make_partial_payment_on_order_status'
                ),
                'wooznd_wallet_partial_payment_remark' => array(
                    'title' => esc_html__('Payment remark', 'wooznd-smartpack'),
                    'type' => "text",
                    'default' => 'Partial Payment',
                    'css' => 'min-width:350px;',
                    'id' => 'wooznd_wallet_partial_payment_remark'
                ),
                'wooznd_show_wallet_partial_payment_box' => array(
                    'title' => esc_html__('Show partial payment box', 'wooznd-smartpack'),
                    'desc' => esc_html__('Allows users to choose when to apply partial payment on their cart.', 'wooznd-smartpack'),
                    'desc_tip' => true,
                    'type' => 'checkbox',
                    'default' => 'no',
                    'id' => 'wooznd_show_wallet_partial_payment_box'
                ),
                'wooznd_wallet_partial_payment_box_title' => array(
                    'title' => esc_html__('Partial payment box title', 'wooznd-smartpack'),
                    'type' => "text",
                    'default' => 'Partial Payment',
                    'css' => 'min-width:350px;',
                    'id' => 'wooznd_wallet_partial_payment_box_title'
                ),
                'wooznd_wallet_partial_payment_box_desc' => array(
                    'title' => esc_html__('Partial payment box description', 'wooznd-smartpack'),
                    'type' => "textarea",
                    'default' => 'Use the {{funds}} available funds in my wallet',
                    'css' => 'min-width:350px;',
                    'custom_attributes' => array(
                        'cols' => '40',
                        'rows' => '2'
                    ),
                    'id' => 'wooznd_wallet_partial_payment_box_desc'
                ),
                'wooznd_wallet_partial_payment_box_label' => array(
                    'title' => esc_html__('Partial payment box checkbox text', 'wooznd-smartpack'),
                    'type' => "text",
                    'default' => 'Use my wallet funds',
                    'css' => 'min-width:350px;',
                    'id' => 'wooznd_wallet_partial_payment_box_label'
                ),
                'wooznd_partial_payment_methods' => array(
                    'title' => esc_html__('Allowed payment methods', 'wooznd-smartpack'),
                    'desc' => esc_html__('Choose which payment method can be use with partial payment, leave this field blank to support all payment methods', 'wooznd-smartpack'),
                    'desc_tip' => true,
                    'type' => 'multiselect',
                    'default' => '',
                    'class' => 'wc-enhanced-select',
                    'css' => 'min-width:300px;',
                    'options' => WooZndUtil::GetPaymentMethodList(),
                    'custom_attributes' => array(
                        'data-placeholder' => esc_html__( 'Select payment methods&hellip;', 'wooznd-smartpack' ),
                    ),
                    'id' => 'wooznd_partial_payment_methods',
                ),
                'wooznd_partial_payment_end' => array(
                    'type' => 'sectionend',
                    'id' => 'wooznd_partial_payment_end'
                ),
                //New Wallets Reward
                'wooznd_new_wallet_title' => array(
                    'name' => esc_html__('New Wallet Reward', 'wooznd-smartpack'),
                    'type' => 'title',
                    'desc' => '',
                    'id' => 'wooznd_new_wallet_title'
                ),
                'wooznd_new_wallet_freecredit' => array(
                    'name' => esc_html__('Reward amount', 'wooznd-smartpack'),
                    'type' => "number",
                    'default' => '0',
                    'desc' => esc_html__('Amount to credit every new wallet', 'wooznd-smartpack'),
                    'desc_tip' => true,
                    'placeholder' => esc_html__('Reward amount', 'wooznd-smartpack'),
                    'custom_attributes' => array(
                        'min' => '0',
                    ),
                    'css' => 'min-width:350px;',
                    'id' => 'wooznd_new_wallet_freecredit'
                ),
                'wooznd_new_wallet_freecredit_remark' => array(
                    'title' => esc_html__('Reward remark', 'wooznd-smartpack'),
                    'type' => "textarea",
                    'default' => 'free',
                    'css' => 'min-width:350px;',
                    'custom_attributes' => array(
                        'cols' => '40',
                        'rows' => '2'
                    ),
                    'id' => 'wooznd_new_wallet_freecredit_remark'
                ),
                'wooznd_new_wallet_freecredit_status' => array(
                    'title' => esc_html__('Reward transaction status', 'wooznd-smartpack'),
                    'type' => 'select',
                    'default' => WOOZND_WALLET_TRANSANCTION_STATUS_PENDING,
                    'class' => 'wc-enhanced-select',
                    'css' => 'min-width:300px;',
                    'options' => array(
                        WOOZND_WALLET_TRANSANCTION_STATUS_PENDING => WalletUtil::TransactionStatusString(WOOZND_WALLET_TRANSANCTION_STATUS_PENDING, false),
                        WOOZND_WALLET_TRANSANCTION_STATUS_ONHOLD => WalletUtil::TransactionStatusString(WOOZND_WALLET_TRANSANCTION_STATUS_ONHOLD, false),
                        WOOZND_WALLET_TRANSANCTION_STATUS_PROCESSING => WalletUtil::TransactionStatusString(WOOZND_WALLET_TRANSANCTION_STATUS_PROCESSING, false),
                        WOOZND_WALLET_TRANSANCTION_STATUS_COMPLETED => WalletUtil::TransactionStatusString(WOOZND_WALLET_TRANSANCTION_STATUS_COMPLETED, false),
                    ),
                    'id' => 'wooznd_new_wallet_freecredit_status'
                ),
                'wooznd_new_wallet_end' => array(
                    'type' => 'sectionend',
                    'id' => 'wooznd_new_wallet_end'
                ),
                //Security
                'wooznd_security_title' => array(
                    'name' => esc_html__('Wallets Data Security', 'wooznd-smartpack'),
                    'type' => 'title',
                    'desc' => '',
                    'id' => 'wooznd_security_title'
                ),
                'wooznd_system_id' => array(
                    'title' => esc_html__('System user', 'wooznd-smartpack'),
                    'type' => 'select',
                    'default' => '1',
                    'class' => 'wc-enhanced-select',
                    'css' => 'min-width:300px;',
                    'options' => $admin_users,
                    'id' => 'wooznd_system_id'
                ),
                'wooznd_encryption_key' => array(
                    'title' => esc_html__('Encryption key', 'wooznd-smartpack'),
                    'type' => "text",
                    'default' => WooZndUtil::GetOption('encryption_key', ''),
                    'css' => 'min-width:350px;',
                    'id' => 'wooznd_encryption_key'
                ),
                'wooznd_encryption_key_vi' => array(
                    'title' => esc_html__('Encryption key VI', 'wooznd-smartpack'),
                    'type' => "text",
                    'default' => WooZndUtil::GetOption('encryption_key_vi', ''),
                    'css' => 'min-width:350px;',
                    'id' => 'wooznd_encryption_key_vi'
                ),
                'wooznd_security_end' => array(
                    'type' => 'sectionend',
                    'id' => 'wooznd_security_end'
                ),
                //New Wallet Mail
                'wooznd_new_wallet_mail_title' => array(
                    'name' => esc_html__('New Wallet Notification', 'wooznd-smartpack'),
                    'type' => 'title',
                    'desc' => '',
                    'id' => 'wooznd_purchase_reward_mail_title'
                ),
                'wooznd_new_wallet_mail_subject' => array(
                    'name' => esc_html__('Email subject', 'wooznd-smartpack'),
                    'type' => "text",
                    'default' => 'Your new wallet has been created',
                    'placeholder' => esc_html__('Subject', 'wooznd-smartpack'),
                    'css' => 'min-width:350px;',
                    'id' => 'wooznd_new_wallet_mail_subject'
                ),
                'wooznd_new_wallet_mail_message' => array(
                    'name' => esc_html__('Email message', 'wooznd-smartpack'),
                    'type' => "textarea",
                    'default' => 'Hi [wznd_wallet_name], <br /> Your new wallet has been created, you can deposit any amount into your wallet and later use this funds to purchase product & services on our website.',
                    'placeholder' => esc_html__('Message', 'wooznd-smartpack'),
                    'css' => 'min-width:350px; min-height:200px;',
                    'id' => 'wooznd_new_wallet_mail_message'
                ),
                'wooznd_new_wallet_mail_end' => array(
                    'type' => 'sectionend',
                    'id' => 'wooznd_new_wallet_mail_end'
                ),
                //New Wallet Reward
                'wooznd_wallet_reward_title' => array(
                    'name' => esc_html__('New Wallet Reward Notification', 'wooznd-smartpack'),
                    'type' => 'title',
                    'desc' => '',
                    'id' => 'wooznd_wallet_reward_title'
                ),
                'wooznd_new_wallet_reward_mail_subject' => array(
                    'name' => esc_html__('Email subject', 'wooznd-smartpack'),
                    'type' => "text",
                    'default' => 'New Wallet Reward',
                    'placeholder' => esc_html__('Subject', 'wooznd-smartpack'),
                    'css' => 'min-width:350px;',
                    'id' => 'wooznd_new_wallet_reward_mail_subject'
                ),
                'wooznd_new_wallet_reward_mail_message' => array(
                    'name' => esc_html__('Email message', 'wooznd-smartpack'),
                    'type' => "textarea",
                    'default' => 'Hi [wznd_wallet_name], <br /> Your new wallet has been credited with [wznd_trans_credit] as part of our on going promo, your new wallet balance is [wznd_wallet_current].',
                    'placeholder' => esc_html__('Message', 'wooznd-smartpack'),
                    'css' => 'min-width:350px; min-height:200px;',
                    'id' => 'wooznd_new_wallet_reward_mail_message'
                ),
                'wooznd_new_wallet_reward_mail_end' => array(
                    'type' => 'sectionend',
                    'id' => 'wooznd_new_wallet_reward_mail_end'
                ),
                //Wallet Deposit
                'wooznd_wallet_deposit_title' => array(
                    'name' => esc_html__('Funds Deposit Notification', 'wooznd-smartpack'),
                    'type' => 'title',
                    'desc' => '',
                    'id' => 'wooznd_wallet_reward_title'
                ),
                'wooznd_wallet_deposit_mail_subject' => array(
                    'name' => esc_html__('Email subject', 'wooznd-smartpack'),
                    'type' => "text",
                    'default' => 'New Funds Deposit',
                    'placeholder' => esc_html__('Subject', 'wooznd-smartpack'),
                    'css' => 'min-width:350px;',
                    'id' => 'wooznd_wallet_deposit_mail_subject'
                ),
                'wooznd_wallet_deposit_mail_message' => array(
                    'name' => esc_html__('Email message', 'wooznd-smartpack'),
                    'type' => "textarea",
                    'default' => 'Hi [wznd_wallet_name], <br /> Your wallet has been credited with [wznd_trans_credit] funds deposit, your new wallet balance is [wznd_wallet_current].',
                    'placeholder' => esc_html__('Message', 'wooznd-smartpack'),
                    'css' => 'min-width:350px; min-height:200px;',
                    'id' => 'wooznd_wallet_deposit_mail_message'
                ),
                'wooznd_wallet_deposit_mail_end' => array(
                    'type' => 'sectionend',
                    'id' => 'wooznd_wallet_deposit_mail_end'
                ),
                //Wallet Transaction
                'wooznd_wallet_transaction_title' => array(
                    'name' => esc_html__('Transactions Notification', 'wooznd-smartpack'),
                    'type' => 'title',
                    'desc' => '',
                    'id' => 'wooznd_wallet_transaction_title'
                ),
                'wooznd_wallet_transactions_mail_subject' => array(
                    'name' => esc_html__('Email subject', 'wooznd-smartpack'),
                    'type' => "text",
                    'default' => 'New Transactions: [wznd_trans_receipt]',
                    'placeholder' => esc_html__('Subject', 'wooznd-smartpack'),
                    'css' => 'min-width:350px;',
                    'id' => 'wooznd_wallet_transactions_mail_subject'
                ),
                'wooznd_wallet_transactions_mail_message' => array(
                    'name' => esc_html__('Email message', 'wooznd-smartpack'),
                    'type' => "textarea",
                    'default' => 'Hi [wznd_wallet_name], <br /> A [wznd_trans_type] transaction ([wznd_trans_receipt]) has occured on your wallet, your new wallet balance is [wznd_wallet_current].',
                    'placeholder' => esc_html__('Message', 'wooznd-smartpack'),
                    'css' => 'min-width:350px; min-height:200px;',
                    'id' => 'wooznd_wallet_transactions_mail_message'
                ),
                'wooznd_wallet_transactions_mail_end' => array(
                    'type' => 'sectionend',
                    'id' => 'wooznd_wallet_transactions_mail_end'
                ),
                //Wallet Transaction Status
                'wooznd_wallet_transaction_status_title' => array(
                    'name' => esc_html__('Transactions Status Notification', 'wooznd-smartpack'),
                    'type' => 'title',
                    'desc' => '',
                    'id' => 'wooznd_wallet_transaction_status_title'
                ),
                'wooznd_wallet_transactions_status_mail_subject' => array(
                    'name' => esc_html__('Email subject', 'wooznd-smartpack'),
                    'type' => "text",
                    'default' => 'Transactions [wznd_trans_receipt] status',
                    'placeholder' => esc_html__('Subject', 'wooznd-smartpack'),
                    'css' => 'min-width:350px;',
                    'id' => 'wooznd_wallet_transactions_status_mail_subject'
                ),
                'wooznd_wallet_transactions_status_mail_message' => array(
                    'name' => esc_html__('Email message', 'wooznd-smartpack'),
                    'type' => "textarea",
                    'default' => 'Hi [wznd_wallet_name], <br /> A [wznd_trans_type] transaction ([wznd_trans_receipt]) is now [wznd_trans_status], your new wallet balance is [wznd_wallet_current].',
                    'placeholder' => esc_html__('Message', 'wooznd-smartpack'),
                    'css' => 'min-width:350px; min-height:200px;',
                    'id' => 'wooznd_wallet_transactions_status_mail_message'
                ),
                'wooznd_wallet_section_end' => array(
                    'type' => 'sectionend',
                    'id' => 'wooznd_wallet_section_end'
                )
            );
            return $settings;
        }

    }

    WC_WooZndWallet::init();
}

