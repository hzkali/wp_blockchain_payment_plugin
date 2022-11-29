<?php

if ( !class_exists( 'WooZendWallet' ) ) {

    class WooZendWallet {

        public static function Init() {


            //Funds Deposit
            add_action( 'template_redirect', array( new self(), 'AddProductToCart' ) );
            add_action( 'woocommerce_before_calculate_totals', array( new self(), 'ApplyDepositPrice' ) );
            add_action( 'woocommerce_get_cart_item_from_session', array( new self(), 'GetDepositPriceSession' ), 99, 2 );
            add_filter( 'woocommerce_add_order_item_meta', array( new self(), 'AddDePositPriceToOrderItemMeta' ), 10, 3 );
            add_filter( 'woocommerce_hidden_order_itemmeta', array( new self(), 'HideOrderItemMetaFields' ) );

            add_action( 'woocommerce_order_status_completed', array( new self(), 'DepositOrderCompleted' ) );
            add_action( 'woocommerce_order_status_processing', array( new self(), 'DepositOrderCompleted' ) );
            add_action( 'woocommerce_order_status_on-hold', array( new self(), 'DepositOrderCompleted' ) );

            add_filter( 'woocommerce_available_payment_gateways', array( new self(), 'FilterPaymentMethods' ), 1 );

            add_filter( 'woocommerce_is_purchasable', array( new self(), 'IsPurchasable' ), 10, 2 );
        }

        //Funds Deposit
        public static function AddProductToCart() {
            if ( isset( $_POST[ 'wznd_wallet_deposit' ] ) ) {
                // select ID
                $product_id = WooZndUtil::GetOption( 'deposit_product_id', 0 );
                if ( $product_id > 0 ) {
                    WC()->session->set( '_wznd_deposit_price', 0 );
                    WC()->cart->add_to_cart( $product_id );
                    WC()->session->set( '_wznd_deposit_price', abs( $_POST[ 'wznd_wallet_deposit' ] ) );
                    exit( wp_redirect( wc_get_cart_url() ) );
                }
            }
        }

        public static function IsPurchasable( $is_pirchasable, $product ) {

            if ( !$product->exists() ) {

                return $is_pirchasable;
            }

            $deposit_product_id = WooZndUtil::GetOption( 'deposit_product_id', 0 );

            if ( !$deposit_product_id ) {

                return $is_pirchasable;
            }

            $product_id = $product->get_id();

            if ( !$product_id ) {

                return $is_pirchasable;
            }

            if ( $product_id == $deposit_product_id ) {

                return true;
            }


            return $is_pirchasable;
        }

        public static function ApplyDepositPrice( $cart_object ) {
            foreach ( $cart_object->cart_contents as $value ) {
                if ( $value[ 'product_id' ] == WooZndUtil::GetOption( 'deposit_product_id', 0 ) ) {
                    $woo_ver = WC()->version;
                    if ( $woo_ver < "3.0.0" && $woo_ver < "2.7.0" ) {
                        $value[ 'data' ]->price = WC()->session->get( '_wznd_deposit_price', 0 );
                    } else {
                        $value[ 'data' ]->set_price( WC()->session->get( '_wznd_deposit_price', 0 ) );
                    }
                }
            }
        }

        public static function GetDepositPriceSession( $item, $values ) {
            $deposit_price = WC()->session->get( '_wznd_deposit_price', 0 );
            if ( $deposit_price > 0 && $item[ 'product_id' ] == WooZndUtil::GetOption( 'deposit_product_id', 0 ) ) {
                $item[ '_wznd_deposit_price' ] = isset( $values[ '_wznd_deposit_price' ] ) ? $values[ '_wznd_deposit_price' ] : $deposit_price;
            }
            return $item;
        }

        public static function AddDePositPriceToOrderItemMeta( $item_id, $values ) {
            if ( isset( $values[ '_wznd_deposit_price' ] ) ) {
                wc_add_order_item_meta( $item_id, '_wznd_deposit_price', $values[ '_wznd_deposit_price' ] );
            }
        }

        public static function HideOrderItemMetaFields( $fields ) {
            $fields[] = '_wznd_deposit_price';
            return $fields;
        }

        public static function DepositOrderCompleted( $order_id ) {
            $wallet_credited = get_post_meta( $order_id, 'wallet_credited', true );
            if ( $wallet_credited == 'on' ) {
                return;
            }
            $order = new WC_Order( $order_id );

            if ( WooZndUtil::woo_order_status_suppassed( $order->get_status(), WooZndUtil::GetOption( 'make_deposit_on_order_status', 'processing' ) ) == false ) {
                return;
            }

            $amount = 0;
            if ( count( $order->get_items() ) > 0 ) {
                foreach ( $order->get_items() as $item ) {
                    //Deposit
                    if ( $item[ 'type' ] == 'line_item' ) {
                        $qty = $item[ 'qty' ];
                        //Deposit
                        foreach ( $item[ 'item_meta' ] as $key => $value ) {
                            if ( $key == '_wznd_deposit_price' ) {
                                $woo_ver = WC()->version;
                                if ( $woo_ver < "3.0.0" && $woo_ver < "2.7.0" ) {
                                    //echo $value[0];
                                    $deposit = abs( $value[ 0 ] );
                                    $amount += ($deposit * $qty);
                                } else {
                                    $deposit = abs( $value );
                                    $amount += ($deposit * $qty);
                                }
                            }
                        }
                    }
                }
            }

            $user = $order->get_user();
            $user_id = $user->ID;

            if ( $amount > 0 ) {
                $trans_id = WooZndWalletTransactionDB::CreditWallet( $user_id, $amount, WOOZND_WALLET_TRANSANCTION_DEPOSIT, $user->user_login, WooZndUtil::GetOption( 'wallet_deposit_remark', esc_html__( 'Funds Deposit', 'wooznd-smartpack' ) ) );
                if ( $trans_id > 0 ) {
                    WooZndWalletTransactionDB::TransactionComplete( $trans_id, $user->user_login, WooZndUtil::GetOption( 'wallet_deposit_remark', esc_html__( 'Funds Deposit', 'wooznd-smartpack' ) ) );
                    WooZndWalletTransactionDB::SetTransactionOrderId( $trans_id, $order_id );
                    do_action( 'wooznd_wallet_deposit_processed', $trans_id, $order_id, $user_id );
                    update_post_meta( $order_id, 'wallet_credited', 'on' );
                }
            }
        }

        public static function FilterPaymentMethods( $gateways ) {
            if ( is_admin() ) {
                return $gateways;
            }

            $found = false;
            if ( isset( WC()->cart->cart_contents ) ) {
                foreach ( WC()->cart->cart_contents as $value ) {
                    if ( $value[ 'product_id' ] == WooZndUtil::GetOption( 'deposit_product_id', 0 ) ) {
                        $found = true;
                    }
                }
            }

            if ( $found == false ) {
                return $gateways;
            }




            $payment_methods = WooZndUtil::GetOption( 'deposit_payment_methods', '' );
            if ( empty( $payment_methods ) ) {
                return $gateways;
            }
            $methods = [];
            foreach ( $payment_methods as $method ) {
                if ( isset( $gateways[ $method ] ) ) {
                    $methods[ $method ] = $gateways[ $method ];
                }
            }
            return $methods;
        }

    }

}


