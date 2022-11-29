<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}


if ( !class_exists( 'WC_WooZndGiftCard' ) ) {

    class WC_WooZndGiftCard {

        public static function init() {
            if ( is_admin() ) {
                //add settings tab
                add_filter( 'woocommerce_settings_tabs_array', array( new self(), 'settings_tabs_array' ), 50 );
                //show settings tab
                add_action( 'woocommerce_settings_tabs_wooznd_giftcard', array( new self(), 'show_settings_tab' ) );
                //save settings tab
                add_action( 'woocommerce_update_options_wooznd_giftcard', array( new self(), 'update_settings_tab' ) );
            }
        }

        public static function settings_tabs_array( $settings_tabs ) {
            $settings_tabs[ 'wooznd_giftcard' ] = esc_html__( 'Gift Card', 'wooznd-smartpack' );
            return $settings_tabs;
        }

        public static function show_settings_tab() {
            woocommerce_admin_fields( self::get_settings() );
        }

        public static function update_settings_tab() {
            woocommerce_update_options( self::get_settings() );
        }

        private static function get_settings() {


            $pro_db = get_option( 'wooznd_giftcard_product_ids', '' );
            $products = [];
            if ( is_array( $pro_db ) ) {
                foreach ( $pro_db as $value ) {
                    $products[ $value ] = WooZndUtil::GetFormattedProductName( $value, false );
                }
            }

            $ex_pro_db = get_option( 'wooznd_giftcard_exclude_product_ids', '' );
            $ex_products = [];
            if ( is_array( $ex_pro_db ) ) {
                foreach ( $ex_pro_db as $value ) {
                    $ex_products[ $value ] = WooZndUtil::GetFormattedProductName( $value, false );
                }
            }

            $select_type = 'multiselect';

            $woo_ver = WC()->version;
            if ( $woo_ver < "3.0.0" && $woo_ver < "2.7.0" ) {
                $select_type = 'text';
            }
            $settings = array(
                'wooznd_giftcard_section_title' => array(
                    'name' => esc_html__( 'Gift Card Settings', 'wooznd-smartpack' ),
                    'type' => 'title',
                    'desc' => '',
                    'id' => 'wooznd_giftcard_section_title'
                ),
                'wooznd_enable_giftcard' => array(
                    'title' => esc_html__( 'Enable/Disable', 'wooznd-smartpack' ),
                    'desc' => esc_html__( 'Enable Gift Card', 'wooznd-smartpack' ),
                    'type' => 'checkbox',
                    'default' => 'yes',
                    'id' => 'wooznd_enable_giftcard'
                ),
                'wooznd_giftcard_discount_type' => array(
                    'title' => esc_html__( 'Discount type', 'wooznd-smartpack' ),
                    'type' => 'select',
                    'default' => 'fixed_cart',
                    'class' => 'wc-enhanced-select',
                    'css' => 'min-width:300px;',
                    'options' => array(
                        'fixed_cart' => esc_html__( 'Cart Discount', 'wooznd-smartpack' ),
                        'fixed_product' => esc_html__( 'Product Discount', 'wooznd-smartpack' ),
                    ),
                    'id' => 'wooznd_giftcard_discount_type'
                ),
                'wooznd_giftcard_codechart_type' => array(
                    'title' => esc_html__( 'Qrcode or Barcode', 'wooznd-smartpack' ),
                    'type' => 'select',
                    'default' => 'br',
                    'class' => 'wc-enhanced-select',
                    'css' => 'min-width:300px;',
                    'options' => array(
                        'br' => esc_html__( 'Barcode', 'wooznd-smartpack' ),
                        'qr' => esc_html__( 'Qrcode', 'wooznd-smartpack' ),
                    ),
                    'id' => 'wooznd_giftcard_codechart_type'
                ),
                'wooznd_giftcard_coupon_pattern' => array( //[N5],[A3],[C3],[Aa4],[Cc3],[a4],[c3]
                    'name' => esc_html__( 'Gift card coupon pattern', 'wooznd-smartpack' ),
                    'type' => "text",
                    'default' => 'WZND[N5][A4]',
                    'placeholder' => esc_html__( 'Coupon pattern', 'wooznd-smartpack' ),
                    'desc' => esc_html__( 'Code pattern to use when creating new gift card coupon, (e.g [A10], [C10], [N10])', 'wooznd-smartpack' ),
                    'desc_tip' => true,
                    'css' => 'min-width:350px;',
                    'id' => 'wooznd_giftcard_coupon_pattern'
                ),
                'wooznd_giftcard_apply_before_tax' => array(
                    'title' => esc_html__( 'Apply gift cards before tax', 'wooznd-smartpack' ),
                    'label' => esc_html__( 'Apply gift cards before tax', 'wooznd-smartpack' ),
                    'name' => esc_html__( 'Apply gift cards before tax', 'wooznd-smartpack' ),
                    'desc' => esc_html__( 'Apply gift cards before tax', 'wooznd-smartpack' ),
                    'desc_tip' => true,
                    'type' => 'checkbox',
                    'default' => 'no',
                    'id' => 'wooznd_giftcard_apply_before_tax'
                ),
                'wooznd_giftcard_free_shipping' => array(
                    'title' => esc_html__( 'Allow free shipping', 'wooznd-smartpack' ),
                    'label' => esc_html__( 'Allow free shipping', 'wooznd-smartpack' ),
                    'name' => esc_html__( 'Allow free shipping', 'wooznd-smartpack' ),
                    'desc' => esc_html__( 'Check this box if the gift card grants free shipping. A free shipping method must be enabled in your shipping zone and be set to require "a valid free shipping coupon" (see the "Free Shipping Requires" setting).', 'wooznd-smartpack' ),
                    'desc_tip' => true,
                    'type' => 'checkbox',
                    'default' => 'no',
                    'id' => 'wooznd_giftcard_free_shipping'
                ),
                'wooznd_giftcard_expiry_days' => array(
                    'name' => esc_html__( 'Gift card expiry after days', 'wooznd-smartpack' ),
                    'type' => "number",
                    'default' => '10',
                    'css' => 'min-width:350px;',
                    'id' => 'wooznd_giftcard_expiry_days'
                ),
                'wooznd_allow_giftcard_coupon' => array(
                    'title' => esc_html__( 'Use gift cards as coupon', 'wooznd-smartpack' ),
                    'label' => esc_html__( 'Use gift cards as coupon', 'wooznd-smartpack' ),
                    'name' => esc_html__( 'Use gift cards as coupon', 'wooznd-smartpack' ),
                    'desc' => esc_html__( 'Check this box if gift cards are to be use as coupon code. Gift cards can also be use as voucher payment method', 'wooznd-smartpack' ),
                    'desc_tip' => true,
                    'type' => 'checkbox',
                    'default' => 'yes',
                    'id' => 'wooznd_allow_giftcard_coupon'
                ),
                'wooznd_giftcard_sections_end' => array(
                    'type' => 'sectionend',
                    'id' => 'wooznd_giftcard_sections_end'
                ),
                //Usage Restriction
                'wooznd_giftcard_usagerestriction_title' => array(
                    'name' => esc_html__( 'Usage Restriction', 'wooznd-smartpack' ),
                    'type' => 'title',
                    'desc' => '',
                    'id' => 'wooznd_giftcard_usagerestriction_title'
                ),
                'wooznd_giftcard_minimum_amount' => array(
                    'name' => esc_html__( 'Minimum spend', 'wooznd-smartpack' ),
                    'type' => "number",
                    'default' => '',
                    'desc' => esc_html__( 'This field allows you to set the minimum spend (subtotal, including taxes) allowed to use gift cards.', 'wooznd-smartpack' ),
                    'desc_tip' => true,
                    'placeholder' => esc_html__( 'No Minimum', 'wooznd-smartpack' ),
                    'custom_attributes' => array(
                        'min' => '0',
                    ),
                    'css' => 'min-width:350px;',
                    'id' => 'wooznd_giftcard_minimum_amount'
                ),
                'wooznd_giftcard_maximum_amount' => array(
                    'name' => esc_html__( 'Maximum spend', 'wooznd-smartpack' ),
                    'type' => "number",
                    'default' => '',
                    'desc' => esc_html__( 'This field allows you to set the maximum spend (subtotal, including taxes) allowed when using gift cards.', 'wooznd-smartpack' ),
                    'desc_tip' => true,
                    'placeholder' => esc_html__( 'No Maximum', 'wooznd-smartpack' ),
                    'custom_attributes' => array(
                        'min' => '0',
                    ),
                    'css' => 'min-width:350px;',
                    'id' => 'wooznd_giftcard_maximum_amount'
                ),
                'wooznd_giftcard_individual_use' => array(
                    'title' => esc_html__( 'Individual use only', 'wooznd-smartpack' ),
                    'label' => esc_html__( 'Individual use only', 'wooznd-smartpack' ),
                    'name' => esc_html__( 'Individual use only', 'wooznd-smartpack' ),
                    'desc' => esc_html__( 'Check this box if a gift card cannot be used in conjunction with other gift cards or coupons.', 'wooznd-smartpack' ),
                    'desc_tip' => true,
                    'type' => 'checkbox',
                    'default' => 'no',
                    'id' => 'wooznd_giftcard_individual_use'
                ),
                'wooznd_giftcard_exclude_sale_items' => array(
                    'title' => esc_html__( 'Exclude sale items', 'wooznd-smartpack' ),
                    'label' => esc_html__( 'Exclude sale items', 'wooznd-smartpack' ),
                    'name' => esc_html__( 'Exclude sale items', 'wooznd-smartpack' ),
                    'desc' => esc_html__( 'Check this box if gift cards should not apply to items on sale. Per-item gift card will only work if the item is not on sale. Per-cart gift cards will only work if there are no sale items in the cart.', 'wooznd-smartpack' ),
                    'desc_tip' => true,
                    'type' => 'checkbox',
                    'default' => 'no',
                    'id' => 'wooznd_giftcard_exclude_sale_items'
                ),
                'wooznd_giftcard_product_ids' => array(
                    'name' => esc_html__( 'Products', 'wooznd-smartpack' ),
                    'title' => esc_html__( 'Products', 'wooznd-smartpack' ),
                    'type' => $select_type,
                    'class' => 'wc-product-search',
                    'desc' => esc_html__( 'Products which need to be in the cart to use gift cards or, for "Product Discounts", which products are discounted.', 'wooznd-smartpack' ),
                    'desc_tip' => true,
                    'options' => $products,
                    'custom_attributes' => array(
                        'data-multiple' => 'true',
                        'data-placeholder' => esc_html__( 'Search for a product&hellip;', 'wooznd-smartpack' ),
                        'data-action' => 'woocommerce_json_search_products_and_variations',
                        'data-selected' => WooZndUtil::GetFormattedProductName( get_option( 'wooznd_giftcard_product_ids', '' ) ),
                    ),
                    'id' => 'wooznd_giftcard_product_ids'
                ),
                'wooznd_giftcard_exclude_product_ids' => array(
                    'name' => esc_html__( 'Exclude products', 'wooznd-smartpack' ),
                    'title' => esc_html__( 'Exclude products', 'wooznd-smartpack' ),
                    'type' => $select_type,
                    'class' => 'wc-product-search',
                    'desc' => esc_html__( 'Products which must not be in the cart to use gift cards or, for "Product Discounts", which products are not discounted.', 'wooznd-smartpack' ),
                    'desc_tip' => true,
                    'options' => $ex_products,
                    'custom_attributes' => array(
                        'data-multiple' => 'true',
                        'data-placeholder' => esc_html__( 'Search for a product&hellip;', 'wooznd-smartpack' ),
                        'data-action' => 'woocommerce_json_search_products_and_variations',
                        'data-selected' => WooZndUtil::GetFormattedProductName( get_option( 'giftcard_exclude_product_ids', '' ) ),
                    ),
                    'id' => 'wooznd_giftcard_exclude_product_ids'
                ),
                'wooznd_giftcard_sectionc_end' => array(
                    'type' => 'sectionend',
                    'id' => 'wooznd_giftcardc_section_end'
                ),
                //Usage Limit
                'wooznd_giftcard_usagelimit_title' => array(
                    'name' => esc_html__( 'Usage Limit', 'wooznd-smartpack' ),
                    'type' => 'title',
                    'desc' => '',
                    'id' => 'wooznd_giftcard_usagelimit_title'
                ),
                'wooznd_giftcard_usage_limit' => array(
                    'name' => esc_html__( 'Usage limit per gift card', 'wooznd-smartpack' ),
                    'type' => "number",
                    'default' => '',
                    'desc' => esc_html__( 'How many times each gift card can be used before it is void.', 'wooznd-smartpack' ),
                    'desc_tip' => true,
                    'placeholder' => esc_html__( 'Unlimited usage', 'wooznd-smartpack' ),
                    'custom_attributes' => array(
                        'min' => '0',
                    ),
                    'css' => 'min-width:350px;',
                    'id' => 'wooznd_giftcard_usage_limit'
                ),
                'wooznd_giftcard_usage_limit_per_user' => array(
                    'name' => esc_html__( 'Usage limit per user', 'wooznd-smartpack' ),
                    'type' => "number",
                    'default' => '',
                    'desc' => esc_html__( 'How many times each gift card can be used by an invidual user. Uses billing email for guests, and user ID for logged in users.', 'wooznd-smartpack' ),
                    'desc_tip' => true,
                    'placeholder' => esc_html__( 'Unlimited usage', 'wooznd-smartpack' ),
                    'custom_attributes' => array(
                        'min' => '0',
                    ),
                    'css' => 'min-width:350px;',
                    'id' => 'wooznd_giftcard_usage_limit_per_user'
                ),
                'wooznd_giftcard_section_end' => array(
                    'type' => 'sectionend',
                    'id' => 'wooznd_giftcard_section_end'
                ),
                //Gift Card Management
                'wooznd_giftcard_management_titles' => array(
                    'name' => esc_html__( 'Gift Card Management & Refunds', 'wooznd-smartpack' ),
                    'type' => 'title',
                    'desc' => '',
                    'id' => 'wooznd_giftcard_management_titles'
                ),
                'wooznd_create_giftcard_coupon_order_status' => array(
                    'title' => esc_html__( 'Create coupon on order status change', 'wooznd-smartpack' ),
                    'desc' => esc_html__( 'Choose when to create gift card coupon', 'wooznd-smartpack' ),
                    'desc_tip' => true,
                    'type' => 'select',
                    'default' => 'processing',
                    'class' => 'wc-enhanced-select',
                    'css' => 'min-width:300px;',
                    'options' => array(
                        'on-hold' => esc_html__( 'On-Hold', 'wooznd-smartpack' ),
                        'processing' => esc_html__( 'Processing', 'wooznd-smartpack' ),
                        'completed' => esc_html__( 'Completed', 'wooznd-smartpack' ),
                    ),
                    'id' => 'wooznd_create_giftcard_coupon_order_status'
                ),
                'wooznd_remove_expired_giftcard' => array(
                    'title' => esc_html__( 'Remove expired gift cards', 'wooznd-smartpack' ),
                    'label' => esc_html__( 'Remove expired gift cards', 'wooznd-smartpack' ),
                    'name' => esc_html__( 'Remove expired gift cards', 'wooznd-smartpack' ),
                    'desc' => esc_html__( 'Remove expired gift cards', 'wooznd-smartpack' ),
                    'desc_tip' => true,
                    'type' => 'checkbox',
                    'default' => 'no',
                    'id' => 'wooznd_remove_expired_giftcard'
                ),
                'wooznd_remove_expired_giftcard_after_days' => array(
                    'name' => esc_html__( 'Remove expired gift cards after days', 'wooznd-smartpack' ),
                    'type' => "number",
                    'default' => '2',
                    'placeholder' => esc_html__( 'No of days', 'wooznd-smartpack' ),
                    'custom_attributes' => array(
                        'min' => '1',
                    ),
                    'css' => 'min-width:350px;',
                    'id' => 'wooznd_remove_expired_giftcard_after_days'
                ),
                'wooznd_giftcard_refund_mode' => array(
                    'title' => esc_html__( 'Gift card refund', 'wooznd-smartpack' ),
                    'type' => 'select',
                    'default' => 'auto',
                    'class' => 'wc-enhanced-select',
                    'css' => 'min-width:300px;',
                    'options' => array(
                        'no-refund' => esc_html__( 'No Refund', 'wooznd-smartpack' ),
                        'auto' => esc_html__( 'Refund Buyer Or User', 'wooznd-smartpack' ),
                        'buyer' => esc_html__( 'Refund Buyer', 'wooznd-smartpack' ),
                        'user' => esc_html__( 'Refund User', 'wooznd-smartpack' ),
                    ),
                    'id' => 'wooznd_giftcard_refund_mode'
                ),
                'wooznd_giftcard_management_end' => array(
                    'type' => 'sectionend',
                    'id' => 'wooznd_giftcard_management_end'
                ),
                //Payment Methods
                'wooznd_giftcard_payment_methods_title' => array(
                    'name' => esc_html__( 'Gift Cards Payment Methods', 'wooznd-smartpack' ),
                    'type' => 'title',
                    'desc' => '',
                    'id' => 'wooznd_giftcard_payment_methods_title'
                ),
                'wooznd_giftcard_payment_methods' => array(
                    'title' => esc_html__( 'Payment methods', 'wooznd-smartpack' ),
                    'desc' => esc_html__( 'Choose which payment method can be use for gift card products, leave this field blank to support all payment methods', 'wooznd-smartpack' ),
                    'desc_tip' => true,
                    'type' => 'multiselect',
                    'default' => '',
                    'class' => 'wc-enhanced-select',
                    'css' => 'min-width:300px;',
                    'options' => WooZndUtil::GetPaymentMethodList(),
                    'custom_attributes' => array(
                        'data-placeholder' => esc_html__( 'Select payment methods&hellip;', 'wooznd-smartpack' ),
                    ),
                    'id' => 'wooznd_giftcard_payment_methods',
                ),
                'wooznd_giftcard_payment_methods_end' => array(
                    'type' => 'sectionend',
                    'id' => 'wooznd_giftcard_payment_methods_end'
                ),
                //Send to Friend Option
                'wooznd_giftcard_sendtofriend_title' => array(
                    'name' => esc_html__( 'Send To Friend Email', 'wooznd-smartpack' ),
                    'type' => 'title',
                    'desc' => '',
                    'id' => 'wooznd_giftcard_sendtofriend_title'
                ),
                'wooznd_giftcard_sendtofriend_subject' => array(
                    'name' => esc_html__( 'Email subject', 'wooznd-smartpack' ),
                    'type' => "text",
                    'default' => 'You have recieved a gift card from [wznd_fromname]',
                    'placeholder' => esc_html__( 'Subject', 'wooznd-smartpack' ),
                    'css' => 'min-width:350px;',
                    'id' => 'wooznd_giftcard_sendtofriend_subject'
                ),
                'wooznd_giftcard_sendtofriend_message' => array(
                    'name' => esc_html__( 'Email message', 'wooznd-smartpack' ),
                    'type' => "textarea",
                    'default' => sanitize_textarea_field( __( 'Hi [wznd_toname], <br /> You have recieved a gift card from [wznd_fromname] with message <strong>[wznd_message]</strong> <br /> your gift card code is [wznd_coupon] and the value of this gift card is [wznd_amount]. please redeem your gift card at  [wznd_site_link] before its expiry date on [wznd_expirydate].', 'wooznd-smartpack' ) ),
                    'placeholder' => esc_html__( 'Message', 'wooznd-smartpack' ),
                    'css' => 'min-width:350px; min-height:200px;',
                    'id' => 'wooznd_giftcard_sendtofriend_message'
                ),
                'wooznd_giftcard_sendtofriend_attach_pdf' => array(
                    'title' => esc_html__( 'Attach gift card PDF', 'wooznd-smartpack' ),
                    'label' => esc_html__( 'Attach gift card PDF', 'wooznd-smartpack' ),
                    'name' => esc_html__( 'Attach gift card PDF', 'wooznd-smartpack' ),
                    'desc' => esc_html__( 'Attach gift card PDF', 'wooznd-smartpack' ),
                    'desc_tip' => true,
                    'type' => 'checkbox',
                    'default' => 'yes',
                    'id' => 'wooznd_giftcard_sendtofriend_attach_pdf'
                ),
                'wooznd_giftcard_sendtofriend_end' => array(
                    'type' => 'sectionend',
                    'id' => 'wooznd_giftcard_sendtofriend_end'
                ),
                //Refun Mail Option
                'wooznd_giftcard_refund_title' => array(
                    'name' => esc_html__( 'Gift Card Refund Notification', 'wooznd-smartpack' ),
                    'type' => 'title',
                    'desc' => '',
                    'id' => 'wooznd_giftcard_refund_title'
                ),
                'wooznd_giftcard_refund_subject' => array(
                    'name' => esc_html__( 'Email subject', 'wooznd-smartpack' ),
                    'type' => "text",
                    'default' => 'Your gift card balance has being refunded',
                    'placeholder' => esc_html__( 'Subject', 'wooznd-smartpack' ),
                    'css' => 'min-width:350px;',
                    'id' => 'wooznd_giftcard_refund_subject'
                ),
                'wooznd_giftcard_refund_message' => array(
                    'name' => esc_html__( 'Email message', 'wooznd-smartpack' ),
                    'type' => "textarea",
                    'default' => 'Hi [wznd_wallet_name], <br /> Your gift card <strong>[wznd_coupon]</strong> remaining balance <strong>[wznd_amount]</strong> has been refunded to you wallet <strong>[wznd_wallet_number]</strong>. You can use this money to purchase item at [wznd_site_link] any time.',
                    'placeholder' => esc_html__( 'Message', 'wooznd-smartpack' ),
                    'css' => 'min-width:350px; min-height:200px;',
                    'id' => 'wooznd_giftcard_refund_message'
                ),
                'wooznd_rest_section_end' => array(
                    'type' => 'sectionend',
                    'id' => 'wooznd_rest_section_end' )
            );
            return $settings;
        }

    }

    WC_WooZndGiftCard::init();
}

