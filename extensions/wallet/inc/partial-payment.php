<?php
if ( !class_exists( 'WooZendWalletPartialPayment' ) ) {

    class WooZendWalletPartialPayment {

        public static function Init() {

            if ( WooZndUtil::GetOption( 'enable_wallet_partial_payment', 'no' ) == 'yes' ) {
                add_action( 'woocommerce_cart_calculate_fees', array( new self(), 'PartialPayment' ) );

                add_action( 'woocommerce_order_status_completed', array( new self(), 'PartialPaymentCompleted' ) );
                add_action( 'woocommerce_order_status_processing', array( new self(), 'PartialPaymentCompleted' ) );
                add_action( 'woocommerce_order_status_on-hold', array( new self(), 'PartialPaymentCompleted' ) );

                add_action( 'woocommerce_review_order_before_payment', array( new self(), 'PartialPaymentSelect' ) );
                add_action( "wp_ajax_wooznd_partialpayment", array( new self(), "PartialPaymentSelectAjax" ) );
                add_filter( 'woocommerce_available_payment_gateways', array( new self(), 'FilterPaymentMethods' ), 1 );

                add_filter( 'woocommerce_cart_totals_get_fees_from_cart_taxes', array( new self(), 'fix_partial_payment_taxes' ), 10, 3 );
            }
        }

        public static function PartialPayment() {
            if ( is_admin() && !defined( 'DOING_AJAX' ) ) {
                return;
            }


            $wallet = WooZndWalletAccountDB::GetAccount( get_current_user_id() );
            if ( !isset( $wallet[ 'locked' ] ) ) {
                return;
            }
            if ( $wallet[ 'locked' ] == true ) {
                return;
            }
            $wallet_total = $wallet[ 'current_balance' ];
            $total = WC()->cart->cart_contents_total + WC()->cart->shipping_total;
            if ( $total <= 0 ) {
                return;
            }
            if ( $wallet_total <= 0 ) {
                return;
            }

            if ( $total <= $wallet_total ) {
                return;
            }

            if ( $wallet_total < WooZndUtil::GetOption( 'wallet_partial_payment_min', 0 ) ) {
                return;
            }

            if ( WooZndUtil::GetOption( 'make_ppm_' . get_current_user_id(), 'no' ) == 'no' && WooZndUtil::GetOption( 'show_wallet_partial_payment_box', 'no' ) == 'yes' ) {
                return;
            }


            foreach ( WC()->cart->cart_contents as $value ) {
                if ( $value[ 'product_id' ] == WooZndUtil::GetOption( 'deposit_product_id', 0 ) ) {
                    return;
                }
            }

            WooZndUtil::UpdateOption( 'partialsdsdsds_pm_' . $wallet[ 'id' ], 0 - $wallet_total );

            $args = array(
                'id' => 'zc_wallet_partial',
                'name' => WooZndUtil::GetOption( 'wallet_partial_payment_text', esc_html__( 'Wallet Funds', 'wooznd-smartpack' ) ),
                'amount' => (0 - $wallet_total),
                'taxable' => false,
            );

            WC()->cart->fees_api()->add_fee( $args );
        }

        public static function PartialPaymentCompleted( $order_id ) {

            $order = new WC_Order( $order_id );

            $user_id = 0;
            $woo_ver = WC()->version;
            if ( $woo_ver < "3.0.0" && $woo_ver < "2.7.0" ) {
                $user_id = $order->user_id;
            } else {
                $user_id = $order->get_user_id();
            }

            $amount = WooZndUtil::GetOption( 'partialsdsdsds_pm_' . $user_id, 0 );
            if ( $amount >= 0 ) {
                return;
            }


            if ( WooZndUtil::woo_order_status_suppassed( $order->get_status(), WooZndUtil::GetOption( 'make_partial_payment_on_order_status', 'on-hold' ) ) == false ) {
                return;
            }


            $user = get_user_by( 'id', $user_id );
            $wallet = WooZndWalletAccountDB::GetAccount( $user_id );
            $wallet_total = $wallet[ 'current_balance' ];


            if ( $wallet[ 'locked' ] == true ) {
                return;
            }


            if ( $amount != (0 - $wallet_total) ) {
                return;
            }


            if ( $wallet_total <= 0 ) {
                return;
            }

            if ( $wallet_total < WooZndUtil::GetOption( 'wallet_partial_payment_min', 0 ) ) {
                return;
            }

            if ( WooZndUtil::GetOption( 'make_ppm_' . get_current_user_id(), 'no' ) == 'no' && WooZndUtil::GetOption( 'show_wallet_partial_payment_box', 'no' ) == 'yes' ) {
                return;
            }







            WooZndUtil::DeleteOption( 'partialsdsdsds_pm_' . $wallet[ 'id' ] );
            $trans_id = WooZndWalletTransactionDB::DebitWallet( $user_id, $wallet_total, WOOZND_WALLET_TRANSANCTION_PAYMENT, $user->user_login, WooZndUtil::GetOption( 'wallet_partial_payment_remark', 'Partial Payment' ) );
            WooZndWalletTransactionDB::TransactionComplete( $trans_id, WooZndUtil::GetOption( 'system_login', 'system_login' ), WooZndUtil::GetOption( 'wallet_partial_payment_remark', esc_html__( 'Partial Payment', 'wooznd-smartpack' ) ) );
            WooZndWalletTransactionDB::SetTransactionOrderId( $trans_id, $order_id );
        }

        public static function PartialPaymentSelect() {

            if ( WooZndUtil::GetOption( 'show_wallet_partial_payment_box', 'no' ) == 'no' ) {
                return;
            }

            $total = WC()->cart->cart_contents_total + WC()->cart->shipping_total;

            if ( $total <= 0 ) {
                return;
            }

            $wallet = WooZndWalletAccountDB::GetAccount( get_current_user_id() );
            if ( !isset( $wallet[ 'locked' ] ) || $wallet[ 'locked' ] == true ) {
                return;
            }

            $wallet_total = isset( $wallet[ 'current_balance' ] ) ? $wallet[ 'current_balance' ] : 0;
            if ( $wallet_total <= 0 ) {
                return;
            }

            if ( $total <= $wallet_total ) {
                return;
            }

            if ( $wallet_total < WooZndUtil::GetOption( 'wallet_partial_payment_min', 0 ) ) {
                return;
            }



            foreach ( WC()->cart->cart_contents as $value ) {
                if ( $value[ 'product_id' ] == WooZndUtil::GetOption( 'deposit_product_id', 0 ) ) {
                    return;
                }
            }


            $box_title = WooZndUtil::GetOption( 'wallet_partial_payment_box_title', esc_html__('Partial Payment','wooznd-smartpack') );
            $box_desc = WooZndUtil::GetOption( 'wooznd_wallet_partial_payment_box_desc', esc_html__('Use the {{funds}} available funds in my wallet','wooznd-smartpack') );
            $box_label = WooZndUtil::GetOption( 'wooznd_wallet_partial_payment_box_label', esc_html__('Use my wallet funds','wooznd-smartpack') );
            ?>
            <div class="wooznd_partialpayment_box">
                <?php
                if ( !empty( $box_title ) ) {
                    ?>
                    <strong class="wooznd_partialpayment_title"><?php echo preg_replace( '/{{funds}}/', wc_price( $wallet[ 'current_balance' ] ), $box_title ) ?></strong>
                    <?php
                }
                ?>
                <?php
                if ( !empty( $box_desc ) ) {
                    ?>
                    <p><?php echo preg_replace( '/{{funds}}/', wc_price( $wallet[ 'current_balance' ] ), $box_desc ) ?></p>
                    <?php
                }
                ?>
                <input type="checkbox" id="wooznd_partialpayment" name="wooznd_partialpayment" value="yes"<?php echo (WooZndUtil::GetOption( 'make_ppm_' . get_current_user_id(), 'no' ) == 'yes') ? ' checked="checked"' : ''; ?>  />  
                <label for="wooznd_partialpayment"><?php echo preg_replace( '/{{funds}}/', wc_price( $wallet[ 'current_balance' ] ), $box_label ) ?></label>
            </div>
            <?php
        }

        public static function PartialPaymentSelectAjax() {
            $use_ppm = ($_POST[ 'use_ppm' ] == 1) ? 'yes' : 'no';
            WooZndUtil::UpdateOption( 'make_ppm_' . get_current_user_id(), $use_ppm );
            echo 'success';
            wp_die();
        }

        public static function FilterPaymentMethods( $gateways ) {
            if ( is_admin() ) {
                return $gateways;
            }

            $payment_methods = WooZndUtil::GetOption( 'partial_payment_methods', '' );
            if ( empty( $payment_methods ) ) {
                return $gateways;
            }

            $wallet = WooZndWalletAccountDB::GetAccount( get_current_user_id() );
            if ( !isset( $wallet[ 'locked' ] ) || $wallet[ 'locked' ] == true ) {
                return $gateways;
            }

            $wallet_total = isset( $wallet[ 'current_balance' ] ) ? $wallet[ 'current_balance' ] : 0;
            $total = WC()->cart->cart_contents_total + WC()->cart->shipping_total;
            if ( $total <= 0 ) {
                return $gateways;
            }

            if ( $wallet_total <= 0 ) {
                return $gateways;
            }

            if ( $total <= $wallet_total ) {
                return $gateways;
            }

            if ( $wallet_total < WooZndUtil::GetOption( 'wallet_partial_payment_min', 0 ) ) {
                return $gateways;
            }

            if ( WooZndUtil::GetOption( 'make_ppm_' . get_current_user_id(), 'no' ) == 'no' && WooZndUtil::GetOption( 'show_wallet_partial_payment_box', 'no' ) == 'yes' ) {
                return $gateways;
            }


            foreach ( WC()->cart->cart_contents as $value ) {
                if ( $value[ 'product_id' ] == WooZndUtil::GetOption( 'deposit_product_id', 0 ) ) {
                    return $gateways;
                }
            }


            $methods = [];
            foreach ( $payment_methods as $method ) {
                if ( isset( $gateways[ $method ] ) ) {
                    $methods[ $method ] = $gateways[ $method ];
                }
            }
            return $methods;
        }

        public static function fix_partial_payment_taxes( $fee_taxes, $fee, $cart_totals ) {

            if ( $fee->object->id == 'zc_wallet_partial' ) {
                return array();
            }

            return $fee_taxes;
        }

    }

}
