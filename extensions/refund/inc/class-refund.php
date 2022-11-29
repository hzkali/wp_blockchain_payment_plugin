<?php

if ( !class_exists( 'WooZndRefund' ) ) {

    class WooZndRefund {

        public static function Init() {
            add_filter( 'wooznd_wallet_process_refund', array( new self(), 'ProcessOrderRefund' ), 10, 3 );
            add_filter( 'wooznd_wallet_gateway_supports', array( new self(), 'GatewaySupports' ), 10, 1 );

            //Refund Meta
            add_action( 'woocommerce_order_item_add_action_buttons', array( new self(), 'OrderRefundMetaBox' ), 10, 1 );
            add_action( 'woocommerce_process_shop_order_meta', array( new self(), 'UpdatOrderRefundMeta' ), 40, 1 );

            //Refund Front-End
            add_filter( 'woocommerce_my_account_my_orders_actions', array( new self(), 'RecentOrdersActions' ), 10, 2 );
            add_action( 'init', array( new self(), 'AddRefundEndPoint' ) );
            add_action( 'template_redirect', array( new self(), 'AddRefundEndPointProcess' ) );

            //Mail
            add_action( 'wooznd_order_renfund_processed', array( new self(), 'OrderRefundProcessed' ), 10, 3 );
        }

        //Payment Gateway Refund
        public static function ProcessOrderRefund( $amount, $reason, $order_id ) {
            $trans_id = 0;
            if ( WooZndRefundDB::RequestExist( $order_id ) == true ) {
                $trans_id = WooZndRefundDB::ProcessRequest( $order_id, $amount, $reason, false );
            } else {
                $trans_id = WooZndRefundDB::RefundWallet( $order_id, $amount, $reason );
            }
            if ( $trans_id > 0 ) {
                return true;
            }
            return false;
        }

        public static function GatewaySupports( $supports ) {
            $supports[] = 'refunds';
            return $supports;
        }

        //Refunds MetaBox
        public static function OrderRefundMetaBox( $order ) {
            if ( WooZndUtil::GetOption( 'enable_refund', 'yes' ) == 'no' ) {
                return;
            }
            $btn_prompt = esc_html__( 'Are you sure you wish to process this refund? this action cannot be undone.', 'wooznd-smartpack' );
            $btn_text = preg_replace( '/{{price}}/', (wc_price( 0 ) ), esc_html__( 'Refund {{price}} via Wallet', 'wooznd-smartpack' ) );
            echo '<div class="woo_wallet_hidden">';
            echo '<button type="submit" name="wallet_refund" value="yes" class="button button-primary wallet-refund" data-warnme="' . $btn_prompt . '"><span class="wc-order-refund-amount">' . $btn_text . '</span></button>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo '</div>';
        }

        public static function UpdatOrderRefundMeta( $order_id ) {
            if ( WooZndUtil::GetOption( 'enable_refund', 'yes' ) == 'no' ) {
                return;
            }
            if ( is_admin() ) {
                if ( isset( $_POST[ 'wallet_refund' ] ) && $_POST[ 'wallet_refund' ] == 'yes' ) {
                    $amount = isset( $_POST[ 'refund_amount' ] ) ? sanitize_text_field( $_POST[ 'refund_amount' ] ) : 0;
                    $reason = isset( $_POST[ 'refund_reason' ] ) ? sanitize_text_field( $_POST[ 'refund_reason' ] ) : '';

                    $trans_id = 0;
                    if ( WooZndRefundDB::RequestExist( $order_id ) == true ) {
                        $trans_id = WooZndRefundDB::ProcessRequest( $order_id, $amount, $reason, false );
                    } else {
                        $trans_id = WooZndRefundDB::RefundWallet( $order_id, $amount, $reason );
                    }
                    if ( $trans_id > 0 ) {

                        $woo_ver = WC()->version;
                        if ( $woo_ver < "3.0.0" && $woo_ver < "2.7.0" ) {
                            $args = array(
                                'order_id' => $order_id,
                                'amount' => $amount,
                                'reason' => $reason,
                                'refund_payment' => true,
                                'restock_items' => false,
                            );
                            $refund = wc_create_refund( $args );
                        } else {
                            $order = new WC_Order( $order_id );
                            wc_create_refund(
                                    array( 'order_id' => $order_id,
                                        'amount' => $amount,
                                        'reason' => $reason,
                                        'date' => $order->get_date_modified()
                            ) );
                        }
                    }
                }
            }
        }

        //Refunds Front-End
        public static function RecentOrdersActions( $actions, $order ) {
            if ( WooZndUtil::GetOption( 'enable_refund', 'yes' ) == 'no' ) {
                return $actions;
            }
            if ( !is_user_logged_in() ) {
                return $actions;
            }
            $order_id = 0;
            $woo_ver = WC()->version;
            if ( $woo_ver < "3.0.0" && $woo_ver < "2.7.0" ) {
                $order_id = $order->id;
            } else {
                $order_id = $order->get_id();
            }
            $url = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) . 'wooznd_refund_order?rf_order_id=' . $order->get_order_number();
            $name = esc_html__( 'Refund', 'wooznd-smartpack' );
            $url = wp_nonce_url( $url, basename( __FILE__ ), 'refnd' );

            $order_request = WooZndRefundDB::GetRequestById( $order_id );
            if ( isset( $order_request[ 'order_id' ] ) ) {
                if ( $order_request[ 'status' ] == WOOZND_WALLET_REFUND_REQUEST_APROVED ) {
                    $url = $order->get_view_order_url();
                    $name = esc_html__( 'Refund Completed', 'wooznd-smartpack' );
                } else if ( $order_request[ 'status' ] == WOOZND_WALLET_REFUND_REQUEST_REJECTED ) {
                    $url = $order->get_view_order_url();
                    $name = esc_html__( 'Refund Rejected', 'wooznd-smartpack' );
                } else if ( $order_request[ 'status' ] == WOOZND_WALLET_REFUND_REQUEST_PENDING ) {
                    $url = $order->get_view_order_url();
                    $name = esc_html__( 'Pending Refund', 'wooznd-smartpack' );
                }
            } else if ( $order->get_status() == 'refunded' ) {
                $url = $order->get_view_order_url();
                $name = esc_html__( 'Refund Completed', 'wooznd-smartpack' );
            } else if ( $order->get_status() == 'on-hold' ) {
                return $actions;
            } else if ( $order->get_status() == 'pending' ) {
                return $actions;
            }

            $actions[ 'wznd_refund_order' ] = array(
                'url' => $url,
                'name' => $name,
            );
            return $actions;
        }

        public static function AddRefundEndPoint() {
            add_rewrite_endpoint( 'wooznd_refund_order', EP_PAGES );
        }

        public static function AddRefundEndPointProcess() {
            if ( WooZndUtil::GetOption( 'enable_refund', 'yes' ) == 'no' ) {
                return;
            }

            if ( is_user_logged_in() && is_account_page() && !empty( $_GET[ 'rf_order_id' ] ) ) {
                $is_valid_nonce = ( isset( $_GET[ 'refnd' ] ) && wp_verify_nonce( $_GET[ 'refnd' ], basename( __FILE__ ) ) ) ? true : false;
                if ( !$is_valid_nonce ) {
                    wp_redirect( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) . '/' . get_option( 'woocommerce_myaccount_orders_endpoint' ) );
                    exit;
                }
                if ( !empty( $_GET[ 'rf_order_id' ] ) ) {
                    $order = new WC_Order( sanitize_text_field( $_GET[ 'rf_order_id' ] ) );
                    WooZndRefundDB::CreateRequest( $_GET[ 'rf_order_id' ], wp_get_current_user()->ID, $order->get_total() + $order->get_total_discount() );
                }
                wp_redirect( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) . '/' . get_option( 'woocommerce_myaccount_orders_endpoint' ) );
                exit;
            }
        }

        public static function OrderRefundProcessed( $transaction_id, $order_id, $account_id ) {
            global $wooznd_transaction, $order, $wooznd_wallet;
            $wooznd_transaction = WooZndWalletTransactionDB::GetTransaction( $transaction_id );
            $order = new WC_Order( $order_id );
            $wooznd_wallet = WooZndWalletAccountDB::GetAccount( $account_id );
            $subject = WooZndUtil::GetOption( 'refun_mail_subject', esc_html__( 'Order Refund', 'wooznd-smartpack' ) );
            $message = WooZndUtil::GetOption( 'refun_mail_message', wp_kses_post( __( 'Hi [wznd_wallet_name], <br /> Your wallet has been credited with [wznd_trans_amount] for the refund of order [wznd_order_link], your new wallet balance is [wznd_wallet_current].', 'wooznd-smartpack' ) ) );
            WooZndUtil::SendMail( $wooznd_wallet[ 'email' ], do_shortcode( $subject ), do_shortcode( $message ) );
        }

        public static function RefundStatusString( $refund_status, $lower_case = true ) {
            switch ( $refund_status ) {
                case WOOZND_WALLET_REFUND_REQUEST_PENDING:
                    if ( $lower_case == true ) {
                        return 'pending';
                    }
                    return 'Pending';
                case WOOZND_WALLET_REFUND_REQUEST_APROVED:
                    if ( $lower_case == true ) {
                        return 'aproved';
                    }
                    return 'Aproved';
                case WOOZND_WALLET_REFUND_REQUEST_REJECTED:
                    if ( $lower_case == true ) {
                        return 'rejected';
                    }
                    return 'Rejected';
                default :
                    return '';
            }
        }

    }

}

