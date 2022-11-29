<?php
add_action( 'plugins_loaded', 'init_giftcard_gateway_class' );

function init_giftcard_gateway_class() {

    class GiftCard_Gateway extends WC_Payment_Gateway {

        private $_remove_failed_orders;

        public function __construct() {
            $this->id = 'ws_voucher';
            $this->method_title = esc_html__( 'Voucher Payments', 'wooznd-smartpack' );
            $this->method_description = esc_html__( 'Allows payment using gift card coupons.', 'wooznd-smartpack' );
            $this->has_fields = true;

            $this->_remove_failed_orders = ($this->get_option( 'remove_failed_order' ) == 'yes');
            $this->supports = apply_filters( 'wooznd_wallet_gateway_supports', array(
                'products'
            ) );


            $this->init_form_fields();
            $this->init_settings();


            $this->title = $this->get_option( 'title' );
            $this->description = $this->get_option( 'description' );

            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        }

        /**
         * Initialise Gateway Settings Form Fields
         */
        public function init_form_fields() {
            $this->form_fields = array(
                'enabled' => array(
                    'title' => esc_html__( 'Enable/Disable', 'wooznd-smartpack' ),
                    'type' => 'checkbox',
                    'label' => esc_html__( 'Enable Voucher Payment', 'wooznd-smartpack' ),
                    'default' => 'no'
                ),
                'title' => array(
                    'title' => esc_html__( 'Title', 'wooznd-smartpack' ),
                    'type' => 'text',
                    'description' => esc_html__( 'This controls the title which the user sees during checkout.', 'wooznd-smartpack' ),
                    'default' => esc_html__( 'Voucher Payment', 'wooznd-smartpack' ),
                    'desc_tip' => true,
                ),
                'low_message' => array(
                    'title' => esc_html__( 'Insufficient Funds Text', 'wooznd-smartpack' ),
                    'type' => 'textarea',
                    'description' => esc_html__( 'This controls the insufficient funds message which the user sees when someone tries to puchase item without having enough money in their gift card.', 'wooznd-smartpack' ),
                    'default' => esc_html__( 'Insufficient funds.', 'wooznd-smartpack' ),
                    'desc_tip' => true,
                ),
                'description' => array(
                    'title' => esc_html__( 'Description', 'wooznd-smartpack' ),
                    'description' => esc_html__( 'This controls the description which the user sees during checkout.', 'wooznd-smartpack' ),
                    'type' => 'textarea',
                    'default' => esc_html__('You can make payments using gift card voucher', 'wooznd-smartpack' ),
                    'desc_tip' => true,
                ),
            );
        }

        function payment_fields() {
            $coupon = '';
            if ( isset( $_POST[ 'giftcard_coupon' ] ) ) {
                $coupon = sanitize_text_field( $_POST[ 'giftcard_coupon' ] );
            }
            ?>
            <div class="woo_wallet_panel">
                <div class="woo_wallet_message">
                    <?php
                    echo $this->description;
                    ?>
                </div>
                <br />
                <div class="wooznd_pay_input">
                    <input type="password" name="giftcard_coupon" value="<?php echo esc_attr( $coupon ); ?>" />
                    <!--                    <br /><br />
                                        <input type="submit" value="Check" />-->
                </div>
            </div>
            <?php
        }

        public function process_payment( $order_id ) {

            $order = wc_get_order( $order_id );

            if ( !$order ) {

                return;
            }

            $total = $order->get_total();
            $coupon = sanitize_text_field( $_POST[ 'giftcard_coupon' ] );
            $card = WooZndGiftCardDB::GetGiftCardByCode( $coupon );

            $card_balance = $card[ 'coupon_amount' ];


            if ( $card_balance >= $total ) {

                if ( WooZndGiftCardDB::DebitGiftCardAmount( $coupon, $total ) ) {

                    $order->payment_complete( $coupon );
                }

                WC()->cart->empty_cart();

                // Return thankyou redirect
                return array(
                    'result' => 'success',
                    'redirect' => $this->get_return_url( $order )
                );
            } else {
                if ( $this->_remove_failed_orders == true ) {
                    wp_delete_post( $order_id, true );
                }
                wc_add_notice( $this->get_option( 'low_message' ), 'error' );
                return;
            }
        }

    }

    //Payment Gateway
    function wznd_add_giftcard_gateway( $gateways ) {
        $gateways[] = 'GiftCard_Gateway';
        return $gateways;
    }

    add_filter( 'woocommerce_payment_gateways', 'wznd_add_giftcard_gateway' );
}
