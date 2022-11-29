<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}


if ( !class_exists( 'WC_WooZndReward' ) ) {

    class WC_WooZndReward {

        public static function init() {
            if ( is_admin() ) {
                //add settings tab
                add_filter( 'woocommerce_settings_tabs_array', array( new self(), 'settings_tabs_array' ), 50 );
                //show settings tab
                add_action( 'woocommerce_settings_tabs_wooznd_reward', array( new self(), 'show_settings_tab' ) );
                //save settings tab
                add_action( 'woocommerce_update_options_wooznd_reward', array( new self(), 'update_settings_tab' ) );
            }
        }

        public static function settings_tabs_array( $settings_tabs ) {
            $settings_tabs[ 'wooznd_reward' ] = esc_html__( 'Reward', 'wooznd-smartpack' );
            return $settings_tabs;
        }

        public static function show_settings_tab() {
            woocommerce_admin_fields( self::get_settings() );
        }

        public static function update_settings_tab() {
            woocommerce_update_options( self::get_settings() );
        }

        private static function get_settings() {


            $settings = array(
                //---------------
                // Other Samples
                //---------------
                'wooznd_reward_section_title' => array(
                    'name' => esc_html__( 'Purchase Reward Settings', 'wooznd-smartpack' ),
                    'type' => 'title',
                    'desc' => '',
                    'id' => 'wooznd_reward_section_title'
                ),
                'wooznd_enable_purchase_reward' => array(
                    'title' => esc_html__( 'Enable/Disable', 'wooznd-smartpack' ),
                    'desc' => esc_html__( 'Enable Purchase Reward', 'wooznd-smartpack' ),
                    'type' => 'checkbox',
                    'default' => 'yes',
                    'id' => 'wooznd_enable_purchase_reward'
                ),
                'wooznd_purchase_reward_remark' => array(
                    'title' => esc_html__( 'Reward  transactions remark', 'wooznd-smartpack' ),
                    'type' => "textarea",
                    'default' => 'Purchase Reward',
                    'custom_attributes' => array(
                        'cols' => '40',
                        'rows' => '5'
                    ),
                    'id' => 'wooznd_purchase_reward_remark'
                ),
                'wooznd_purchase_reward_payment_methods' => array(
                    'title' => esc_html__( 'Allowed payment methods', 'wooznd-smartpack' ),
                    'desc' => esc_html__( 'Choose which payment method can be use for purchase reward, leave this field blank to support all payment methods', 'wooznd-smartpack' ),
                    'desc_tip' => true,
                    'type' => 'multiselect',
                    'default' => '',
                    'class' => 'wc-enhanced-select',
                    'css' => 'min-width:300px;',
                    'options' => WooZndUtil::GetPaymentMethodList(),
                    'custom_attributes' => array(
                        'data-placeholder' => esc_html__( 'Select payment methods&hellip;', 'wooznd-smartpack' ),
                    ),
                    'id' => 'wooznd_purchase_reward_payment_methods',
                ),
                'wooznd_purchase_reward_payment_method_filter' => array(
                    'title' => esc_html__( 'Payment methods filter', 'wooznd-smartpack' ),
                    'desc' => esc_html__( 'Limit users to the above list of payment methods', 'wooznd-smartpack' ),
                    'type' => 'checkbox',
                    'default' => 'no',
                    'id' => 'wooznd_purchase_reward_payment_method_filter'
                ),
                'wooznd_purchase_reward_end' => array(
                    'type' => 'sectionend',
                    'id' => 'wooznd_purchase_reward_end'
                ),
                //Refun Mail Option
                'wooznd_purchase_reward_mail_title' => array(
                    'name' => esc_html__( 'Reward Notification', 'wooznd-smartpack' ),
                    'type' => 'title',
                    'desc' => '',
                    'id' => 'wooznd_purchase_reward_mail_title'
                ),
                'wooznd_purchase_reward_subject' => array(
                    'name' => esc_html__( 'Email subject', 'wooznd-smartpack' ),
                    'type' => "text",
                    'default' => esc_html__( 'Purchase reward', 'wooznd-smartpack' ),
                    'placeholder' => esc_html__( 'Subject', 'wooznd-smartpack' ),
                    'css' => 'min-width:350px;',
                    'id' => 'wooznd_purchase_reward_subject'
                ),
                'wooznd_purchase_reward_message' => array(
                    'name' => esc_html__( 'Email message', 'wooznd-smartpack' ),
                    'type' => "textarea",
                    'default' => wp_kses_post( __( 'Hi [wznd_wallet_name], <br /> Your wallet has been credited with [wznd_trans_credit] for purchasing [wznd_product_link], your new wallet balance is [wznd_wallet_current].', 'wooznd-smartpack' ) ),
                    'placeholder' => esc_html__( 'Message', 'wooznd-smartpack' ),
                    'css' => 'min-width:350px; min-height:200px;',
                    'id' => 'wooznd_purchase_reward_message'
                ),
                'wooznd_reward_section_end' => array(
                    'type' => 'sectionend',
                    'id' => 'wooznd_reward_section_end'
                )
            );
            return $settings;
        }

    }

    WC_WooZndReward::init();
}

