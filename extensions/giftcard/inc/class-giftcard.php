<?php

if ( !class_exists( 'WooZndGiftCard' ) ) {

    class WooZndGiftCard {

        public static function Init() {

            add_filter( 'woocommerce_hidden_order_itemmeta', array( new self(), 'HideOrderItemMetaFields' ) );

            add_filter( 'woocommerce_loop_add_to_cart_link', array( new self(), 'DisplayProductButton' ), 99, 2 );
            add_filter( 'woocommerce_get_price_html', array( new self(), 'DisplayProductPrice' ), 100, 2 );
            add_action( 'woocommerce_before_add_to_cart_button', array( new self(), 'DisplayGiftCardForm' ), 20 );
            add_filter( 'woocommerce_add_to_cart_validation', array( new self(), 'ValidateGiftCardForm' ), 10, 3 );
            add_filter( 'woocommerce_add_cart_item', array( new self(), 'SetGiftCardAddToCartPrice' ) );
            add_filter( 'woocommerce_get_cart_item_from_session', array( new self(), 'SetGiftCardSessionPrices' ), 20, 3 );
            add_action( 'woocommerce_before_calculate_totals', array( new self(), 'SetGiftCardPrice' ) );

            add_filter( 'woocommerce_available_payment_gateways', array( new self(), 'FilterPaymentMethods' ), 1 );

            // Get item data to display
            add_filter( 'woocommerce_get_item_data', array( new self(), 'GetGiftCardItemData' ), 10, 2 );

            add_filter( 'woocommerce_add_order_item_meta', array( new self(), 'AddGiftCardOrderItemMeta' ), 10, 3 );
            add_action( 'woocommerce_after_order_itemmeta', array( new self(), 'DisplayAdminGiftCardOrderMeta' ), 10, 4 );


            $woo_ver = WC()->version;
            if ( $woo_ver < "3.0.0" && $woo_ver < "2.7.0" ) {
                add_filter( 'woocommerce_order_items_meta_display', array( new self(), 'DisplayFrontEndGiftCardOrderMeta' ), 10, 2 );
            } else {
                add_filter( 'woocommerce_display_item_meta', array( new self(), 'DisplayFrontEndGiftCardOrderMeta' ), 10, 2 );
            }

            add_action( 'woocommerce_order_status_completed', array( new self(), 'ProcessGiftCardOrder' ) );
            add_action( 'woocommerce_order_status_processing', array( new self(), 'ProcessGiftCardOrder' ) );
            add_action( 'woocommerce_order_status_on-hold', array( new self(), 'ProcessGiftCardOrder' ) );

            add_action( 'woocommerce_init', array( new self(), 'InitActivity' ) );

            //================================
            //Gift Card Coupon Usage Processes
            //================================
            if ( WooZndUtil::GetOption( 'allow_giftcard_coupon', 'yes' ) == 'yes' ) {
                add_filter( 'woocommerce_checkout_coupon_message', array( new self(), 'DisplayCheckoutCouponMessage' ) );
                add_action( 'woocommerce_after_calculate_totals', array( new self(), 'SetCouponDiscountSession' ) );
                add_action( 'woocommerce_checkout_order_processed', array( new self(), 'ProcessGiftCardCouponDiscount' ) );
            }
        }

        public static function HideOrderItemMetaFields( $fields ) {
            $fields[] = '_wznd_gift_price';
            $fields[] = '_wznd_delivery_method';
            $fields[] = '_wznd_send_to_name';
            $fields[] = '_wznd_send_to_email';
            $fields[] = '_wznd_send_to_message';
            $fields[] = '_wznd_send_date';
            $fields[] = '_wznd_sender_email';
            $fields[] = '_wznd_sender_name';
            $fields[] = '_wznd_item_id';
            return $fields;
        }

        public static function DisplayProductButton( $link, $product ) {
            if ( get_post_meta( $product->get_id(), '_wznd_enable_giftcard', true ) == 'yes' ) {
                return sprintf( '<a rel="nofollow" href="%s" class="%s">%s</a>', esc_url( get_permalink( $product->get_id() ) ), esc_attr( isset( $class ) ? $class : 'button' ), esc_html__( 'Buy Now', 'wooznd-smartpack' ) );
            } else {
                return $link;
            }
        }

        public static function DisplayProductPrice( $price, $product ) {

            if ( get_post_meta( $product->get_id(), '_wznd_enable_giftcard', true ) == 'yes' ) {

                $price_type = get_post_meta( $product->get_id(), '_wznd_giftcard_price_type', true );

                switch ( $price_type ) {
                    case 'user':
                        return '<ins>' . esc_html__( 'Your price', 'wooznd-smartpack' ) . '</ins>';
                    case 'range':
                        $price_from = get_post_meta( $product->get_id(), '_wznd_giftcard_from_price', true );
                        $price_to = get_post_meta( $product->get_id(), '_wznd_giftcard_to_price', true );
                        return '<ins>' . wc_price( $price_from ) . ' - ' . wc_price( $price_to ) . '</ins>';
                    case 'select':
                        $prices = str_getcsv( get_post_meta( $product->get_id(), '_wznd_giftcard_select_price', true ), '|' );
                        $price_vals = array();
                        if ( is_array( $prices ) ) {
                            foreach ( $prices as $price ) {
                                $price_vals[] = wc_price( $price );
                            }
                        }
                        return '<ins>' . implode( ', ', $price_vals ) . '</ins>';
                    default:
                        return '<ins>' . wc_price( $product->get_price() ) . '</ins>';
                }
            } else {
                return $price;
            }
        }

        public static function DisplayGiftCardForm() {
            wp_nonce_field( basename( __FILE__ ), 'wznd_card_nonce_front' );
            $product = wc_get_product( get_the_ID() );

            if ( get_post_meta( $product->get_id(), '_wznd_enable_giftcard', true ) == 'yes' ) {
                $price_type = get_post_meta( $product->get_id(), '_wznd_giftcard_price_type', true );
                $delivary_mathod = get_post_meta( $product->get_id(), '_wznd_giftcard_delivery', true );
                $show_sender_name = get_post_meta( $product->get_id(), '_wznd_giftcard_show_sender_name', true );
                $show_sender_email = get_post_meta( $product->get_id(), '_wznd_giftcard_show_sender_email', true );
                $show_receiver_name = get_post_meta( $product->get_id(), '_wznd_giftcard_show_receiver_name', true );
                $show_receiver_email = get_post_meta( $product->get_id(), '_wznd_giftcard_show_receiver_email', true );
                $show_message = get_post_meta( $product->get_id(), '_wznd_giftcard_show_message', true );

                $hide_email_fields = (empty( $delivary_mathod ) && $delivary_mathod == '') ? ' wznd_gift_card_show_if_email' : '';

                include 'views/form.php';
            }
        }

        public static function ValidateGiftCardForm( $passed, $product_id, $qty ) {
            $error_list = array();

            $is_valid_nonce = ( isset( $_POST[ 'wznd_card_nonce_front' ] ) && wp_verify_nonce( $_POST[ 'wznd_card_nonce_front' ], basename( __FILE__ ) ) ) ? true : false;
            if ( !$is_valid_nonce && get_post_meta( $product_id, '_wznd_enable_giftcard', true ) == 'yes' ) {
                $passed = false;
                $error_list[] = esc_html__( 'Unknown error.', 'wooznd-smartpack' );
            }

            if ( isset( $_POST[ 'gift_price' ] ) && $_POST[ 'gift_price' ] == '' ) {
                $passed = false;
                $error_list[] = esc_html__( '"Gift card price" field is empty.', 'wooznd-smartpack' );
            }

            $delivary_mathod = WOOZND_GIFTCARD_DELIVERY_EMAIL;
            if ( isset( $_POST[ 'delivary_method' ] ) ) {
                $delivary_mathod = $_POST[ 'delivary_method' ];
            } else {
                $delivary_mathod = get_post_meta( $product_id, '_wznd_giftcard_delivery', true );
            }

            $show_receiver_name = get_post_meta( $product_id, '_wznd_giftcard_show_receiver_name', true );
            $show_receiver_email = get_post_meta( $product_id, '_wznd_giftcard_show_receiver_email', true );
            $show_message = get_post_meta( $product_id, '_wznd_giftcard_show_message', true );



            if ( $delivary_mathod == WOOZND_GIFTCARD_DELIVERY_EMAIL && $show_receiver_name == 'yes' && isset( $_POST[ 'send_to_name' ] ) && $_POST[ 'send_to_name' ] == '' ) {
                $passed = false;
                $error_list[] = esc_html__( '"Recipient name" field is empty.', 'wooznd-smartpack' );
            }

            if ( $delivary_mathod == WOOZND_GIFTCARD_DELIVERY_EMAIL && $show_receiver_email == 'yes' && isset( $_POST[ 'send_to_email' ] ) && $_POST[ 'send_to_email' ] == '' ) {
                $passed = false;
                $error_list[] = esc_html__( '"Recipient email" field is empty.', 'wooznd-smartpack' );
            }

            if ( $delivary_mathod == WOOZND_GIFTCARD_DELIVERY_EMAIL && $show_message == 'yes' && isset( $_POST[ 'send_to_message' ] ) && $_POST[ 'send_to_message' ] == '' ) {
                $passed = false;
                $error_list[] = esc_html__( '"Message field" is empty.', 'wooznd-smartpack' );
            }

            if ( isset( $_POST[ 'send_date' ] ) && $_POST[ 'send_date' ] == '' ) {
                $passed = false;
                $error_list[] = esc_html__( '"Gift card date" field is empty.', 'wooznd-smartpack' );
            }

            foreach ( $error_list as $errorz ) {
                wc_add_notice( $errorz, 'error' );
            }

            return $passed;
        }

        public static function SetGiftCardAddToCartPrice( $item_data ) {
            $is_valid_nonce = ( isset( $_POST[ 'wznd_card_nonce_front' ] ) && wp_verify_nonce( $_POST[ 'wznd_card_nonce_front' ], basename( __FILE__ ) ) ) ? true : false;
            if ( !$is_valid_nonce ) {
                return $item_data;
            }
            if ( !isset( $_POST[ 'gift_price' ] ) || empty( $_POST[ 'gift_price' ] ) ) {
                return $item_data;
            }

            $gift_price = isset( $_POST[ 'gift_price' ] ) ? abs( $_POST[ 'gift_price' ] ) : 0;
            $gift_delivery = isset( $_POST[ 'delivary_method' ] ) ? sanitize_text_field( $_POST[ 'delivary_method' ] ) : '';
            $gift_from_name = isset( $_POST[ 'sender_name' ] ) ? sanitize_text_field( $_POST[ 'sender_name' ] ) : '';
            $gift_from_email = isset( $_POST[ 'sender_email' ] ) ? sanitize_text_field( $_POST[ 'sender_email' ] ) : '';
            $gift_to_name = isset( $_POST[ 'send_to_name' ] ) ? sanitize_text_field( $_POST[ 'send_to_name' ] ) : '';
            $gift_to_email = isset( $_POST[ 'send_to_email' ] ) ? sanitize_email( $_POST[ 'send_to_email' ] ) : '';
            $gift_to_message = isset( $_POST[ 'send_to_message' ] ) ? sanitize_textarea_field( $_POST[ 'send_to_message' ] ) : '';
            $gift_send_date = isset( $_POST[ 'send_date' ] ) ? sanitize_text_field( $_POST[ 'send_date' ] ) : current_time( 'Y-m-d' );
            $item_data[ 'data' ]->set_price( $gift_price );
            $p_id = $item_data[ 'product_id' ];
            WC()->session->set( '_wznd_gift_price' . $p_id, $gift_price );
            WC()->session->set( '_wznd_gift_delivery' . $p_id, $gift_delivery );
            WC()->session->set( '_wznd_send_from_name' . $p_id, $gift_from_name );
            WC()->session->set( '_wznd_send_from_email' . $p_id, $gift_from_email );
            WC()->session->set( '_wznd_send_to_name' . $p_id, $gift_to_name );
            WC()->session->set( '_wznd_send_to_email' . $p_id, $gift_to_email );
            WC()->session->set( '_wznd_send_to_message' . $p_id, $gift_to_message );
            WC()->session->set( '_wznd_send_date' . $p_id, $gift_send_date );
            return $item_data;
        }

        public static function SetGiftCardSessionPrices( $item_data, $values, $key ) {

            $p_id = $item_data[ 'product_id' ];

            if ( get_post_meta( $p_id, '_wznd_enable_giftcard', true ) == 'yes' ) {
                $gift_price = WC()->session->get( '_wznd_gift_price' . $p_id, 0 );
                $gift_delivery = WC()->session->get( '_wznd_gift_delivery' . $p_id, WOOZND_GIFTCARD_DELIVERY_EMAIL );
                $gift_from_name = WC()->session->get( '_wznd_send_from_name' . $p_id, '' );
                $gift_from_email = WC()->session->get( '_wznd_send_from_email' . $p_id, '' );
                $gift_to_name = WC()->session->get( '_wznd_send_to_name' . $p_id, '' );
                $gift_to_email = WC()->session->get( '_wznd_send_to_email' . $p_id, '' );
                $gift_to_message = WC()->session->get( '_wznd_send_to_message' . $p_id, '' );
                $gift_send_date = WC()->session->get( '_wznd_send_date' . $p_id, '' );


                $item_data[ '_wznd_gift_price' ] = isset( $values[ '_wznd_gift_price' ] ) ? $values[ '_wznd_gift_price' ] : $gift_price;
                $item_data[ '_wznd_gift_delivery' ] = isset( $values[ '_wznd_gift_delivery' ] ) ? $values[ '_wznd_gift_delivery' ] : $gift_delivery;
                $item_data[ '_wznd_send_from_name' ] = isset( $values[ '_wznd_send_from_name' ] ) ? $values[ '_wznd_send_from_name' ] : $gift_from_name;
                $item_data[ '_wznd_send_from_email' ] = isset( $values[ '_wznd_send_from_email' ] ) ? $values[ '_wznd_send_from_email' ] : $gift_from_email;
                $item_data[ '_wznd_send_to_name' ] = isset( $values[ '_wznd_send_to_name' ] ) ? $values[ '_wznd_send_to_name' ] : $gift_to_name;
                $item_data[ '_wznd_send_to_email' ] = isset( $values[ '_wznd_send_to_email' ] ) ? $values[ '_wznd_send_to_email' ] : $gift_to_email;
                $item_data[ '_wznd_send_to_message' ] = isset( $values[ '_wznd_send_to_message' ] ) ? $values[ '_wznd_send_to_message' ] : $gift_to_message;
                $item_data[ '_wznd_send_date' ] = isset( $values[ '_wznd_send_date' ] ) ? $values[ '_wznd_send_date' ] : $gift_send_date;


                $woo_ver = WC()->version;
                if ( $woo_ver < "3.0.0" && $woo_ver < "2.7.0" ) {
                    $item_data[ 'data' ]->price = WC()->session->get( '_wznd_gift_price' . $item_data[ 'product_id' ], 0 );
                } else {
                    $item_data[ 'data' ]->set_price( WC()->session->get( '_wznd_gift_price' . $item_data[ 'product_id' ], 0 ) );
                }
            }

            return $item_data;
        }

        public static function SetGiftCardPrice( $cart_object ) {
            $woo_ver = WC()->version;

            foreach ( $cart_object->cart_contents as $value ) {
                if ( get_post_meta( $value[ 'product_id' ], '_wznd_enable_giftcard', true ) == 'yes' ) {
                    $custom_price = WC()->session->get( '_wznd_gift_price' . $value[ 'product_id' ], 0 );

                    if ( $woo_ver < "3.0.0" && $woo_ver < "2.7.0" ) {
                        $value[ 'data' ]->price = $custom_price;
                    } else {
                        $value[ 'data' ]->set_price( $custom_price );
                    }
                }
            }
        }

        public static function GetGiftCardItemData( $item_data, $cart_item ) {
            $product_id = $cart_item[ 'product_id' ];

            if ( get_post_meta( $product_id, '_wznd_enable_giftcard', true ) == 'yes' ) {
                if ( (isset( $cart_item[ '_wznd_send_to_name' ] ) && $cart_item[ '_wznd_send_to_name' ] != '') || (isset( $cart_item[ '_wznd_send_to_email' ] ) && $cart_item[ '_wznd_send_to_email' ] != '') ) {
                    $to_name = $cart_item[ '_wznd_send_to_name' ];
                    if ( isset( $cart_item[ '_wznd_send_to_email' ] ) ) {
                        $to_name = $cart_item[ '_wznd_send_to_name' ] . ' (' . $cart_item[ '_wznd_send_to_email' ] . ')';
                    }
                    $item_data[ '_wznd_send_to_name' ][ 'key' ] = esc_html__( 'To', 'wooznd-smartpack' );
                    $item_data[ '_wznd_send_to_name' ][ 'value' ] = $to_name;
                }

                if ( (isset( $cart_item[ '_wznd_send_from_name' ] ) && $cart_item[ '_wznd_send_from_name' ] != '') || (isset( $cart_item[ '_wznd_send_from_email' ] ) && $cart_item[ '_wznd_send_from_email' ] != '') ) {
                    $from_name = $cart_item[ '_wznd_send_from_name' ];
                    if ( $cart_item[ '_wznd_send_from_email' ] ) {
                        $from_name = $cart_item[ '_wznd_send_from_name' ] . ' (' . $cart_item[ '_wznd_send_from_email' ] . ')';
                    }
                    $item_data[ '_wznd_send_from_name' ][ 'key' ] = esc_html__( 'From', 'wooznd-smartpack' );
                    $item_data[ '_wznd_send_from_name' ][ 'value' ] = $from_name;
                }

                if ( isset( $cart_item[ '_wznd_send_to_message' ] ) && $cart_item[ '_wznd_send_to_message' ] != '' ) {
                    $item_data[ '_wznd_send_to_message' ][ 'key' ] = esc_html__( 'Gift Card Message', 'wooznd-smartpack' );
                    $item_data[ '_wznd_send_to_message' ][ 'value' ] = $cart_item[ '_wznd_send_to_message' ];
                }
            }

            return $item_data;
        }

        public static function AddGiftCardOrderItemMeta( $item_id, $values ) {

            if ( isset( $values[ '_wznd_gift_price' ] ) ) {
                $user_email = wp_get_current_user()->user_email;
                $full_name = wp_get_current_user()->first_name . ' ' . wp_get_current_user()->last_name;
                if ( isset( $values[ '_wznd_send_from_email' ] ) ) {
                    $user_email = $values[ '_wznd_send_from_email' ];
                }
                if ( isset( $values[ '_wznd_send_from_name' ] ) ) {
                    $full_name = $values[ '_wznd_send_from_name' ];
                }

                $delivery_method = WOOZND_GIFTCARD_DELIVERY_EMAIL;

                wc_add_order_item_meta( $item_id, '_wznd_item_id', $item_id );
                wc_add_order_item_meta( $item_id, '_wznd_delivery_method', $delivery_method );
                wc_add_order_item_meta( $item_id, '_wznd_sender_email', $user_email );
                wc_add_order_item_meta( $item_id, '_wznd_sender_name', $full_name );

                wc_add_order_item_meta( $item_id, '_wznd_gift_price', $values[ '_wznd_gift_price' ] );
            }

            if ( isset( $values[ '_wznd_send_to_email' ] ) ) {
                wc_add_order_item_meta( $item_id, '_wznd_send_to_email', $values[ '_wznd_send_to_email' ] );
            }
            if ( isset( $values[ '_wznd_send_to_name' ] ) ) {
                wc_add_order_item_meta( $item_id, '_wznd_send_to_name', $values[ '_wznd_send_to_name' ] );
            }

            if ( isset( $values[ '_wznd_send_to_message' ] ) ) {
                wc_add_order_item_meta( $item_id, '_wznd_send_to_message', $values[ '_wznd_send_to_message' ] );
            }
            if ( isset( $values[ '_wznd_send_date' ] ) ) {
                wc_add_order_item_meta( $item_id, '_wznd_send_date', $values[ '_wznd_send_date' ] );
            }
        }

        public static function DisplayAdminGiftCardOrderMeta( $item_id, $item, $product ) {
            if ( !isset( $product ) ) {
                return;
            }
            $all_meta_data = get_metadata( 'order_item', $item_id, "", "" );
            $coupon = WooZndGiftCardDB::GetCouponCodeByGiftCardId( $item_id );

            $product_id = 0;
            if ( method_exists( $product, 'get_id' ) ) {
                $product_id = $product->get_id();
            } else {
                $product_id = $product->id;
            }

            if ( get_post_meta( $product_id, '_wznd_enable_giftcard', true ) == 'yes' ) {

                include 'views/order-itemmeta.php';
            }
        }

        public static function DisplayFrontEndGiftCardOrderMeta( $output, $class_meta ) {
            $meta_list = array();
            $item_id = 0;
            if ( get_post_meta( $class_meta->get_product_id(), '_wznd_enable_giftcard', true ) == 'yes' ) {
                $coupon;
                $woo_ver = WC()->version;
                if ( $woo_ver < "3.0.0" && $woo_ver < "2.7.0" ) {

                    $formatted_meta = $class_meta->get_formatted( '' );

                    foreach ( $formatted_meta as $meta ) {

                        if ( $meta[ 'label' ] == '_wznd_send_to_email' && wp_kses_post( wpautop( $meta[ 'value' ] ) ) != '' ) {
                            $meta_list[] = '<dt class="variation-wznd_send_to_email">' . esc_html__( 'To:', 'wooznd-smartpack' ) . '</dt>';
                            $meta_list[] = '<dd class="variation-wznd_send_to_email">' . wp_kses_post( wpautop( $meta[ 'value' ] ) ) . '</dd>';
                        }
                        if ( $meta[ 'label' ] == '_wznd_sender_email' && wp_kses_post( wpautop( $meta[ 'value' ] ) ) != '' ) {
                            $meta_list[] = '<dt class="variation-wznd_sender_email">' . esc_html__( 'From:', 'wooznd-smartpack' ) . '</dt>';
                            $meta_list[] = '<dd class="variation-wznd_sender_email">' . wp_kses_post( wpautop( $meta[ 'value' ] ) ) . '</dd>';
                        }
                        if ( $meta[ 'label' ] == '_wznd_send_to_message' && wp_kses_post( wpautop( $meta[ 'value' ] ) ) != '' ) {
                            $meta_list[] = '<dt class="variation-wznd_send_to_message">' . esc_html__( 'Message:', 'wooznd-smartpack' ) . '</dt>';
                            $meta_list[] = '<dd class="variation-wznd_send_to_message">' . wp_kses_post( wpautop( $meta[ 'value' ] ) ) . '</dd>';
                        }

                        if ( $meta[ 'label' ] == '_wznd_item_id' ) {
                            $item_id = $meta[ 'value' ];

                            $coupon = WooZndGiftCardDB::GetCouponCodeByGiftCardId( $meta[ 'value' ] );
                            if ( !empty( $coupon ) ) {
                                $meta_list[] = '<dt class="variation-wznd_item_id">' . esc_html__( 'Gift Card Coupon:', 'wooznd-smartpack' ) . '</dt>';
                                $meta_list[] = '<dd class="variation-wznd_item_id">' . strtoupper( $coupon ) . '</dd>';
                            }
                        }
                    }
                } else {

                    $formatted_meta = $class_meta->get_formatted_meta_data( '' );

                    foreach ( $formatted_meta as $meta ) {
                        if ( $meta->key == '_wznd_send_to_email' && $meta->display_value != '' ) {
                            $meta_list[] = '<dt class="variation-wznd_send_to_email">' . esc_html__( 'To:', 'wooznd-smartpack' ) . '</dt>';
                            $meta_list[] = '<dd class="variation-wznd_send_to_email">' . $meta->display_value . '</dd>';
                        }

                        if ( $meta->key == '_wznd_sender_email' && $meta->display_value != '' ) {
                            $meta_list[] = '<dt class="variation-wznd_sender_email">' . esc_html__( 'From:', 'wooznd-smartpack' ) . '</dt>';
                            $meta_list[] = '<dd class="variation-wznd_sender_email">' . $meta->display_value . '</dd>';
                        }
                        if ( $meta->key == '_wznd_send_to_message' && $meta->display_value != '' ) {
                            $meta_list[] = '<dt class="variation-wznd_send_to_message">' . esc_html__( 'Message:', 'wooznd-smartpack' ) . '</dt>';
                            $meta_list[] = '<dd class="variation-wznd_send_to_message">' . $meta->display_value . '</dd>';
                        }

                        if ( $meta->key == '_wznd_item_id' ) {
                            $coupon = WooZndGiftCardDB::GetCouponCodeByGiftCardId( $meta->value );
                            if ( !empty( $coupon ) ) {
                                $meta_list[] = '<dt class="variation-wznd_item_id">' . esc_html__( 'Gift Card Coupon:', 'wooznd-smartpack' ) . '</dt>';
                                $meta_list[] = '<dd class="variation-wznd_item_id">' . strtoupper( $coupon ) . '</dd>';
                            }
                        }
                    }

                    $item_id = $class_meta->get_id();
                }

                if ( !empty( $coupon ) ) {
                    $meta_list[] = '<dt class="variation-wznd_Download">' . esc_html__( 'Download:', 'wooznd-smartpack' ) . '</dt>';
                    $meta_list[] = '<dd class="variation-wznd_item_id"><a href="' . WP_CONTENT_URL . '/uploads/woo-smart-pack/giftcards/giftcard' . $item_id . '.pdf' . '" target="_blank">' . esc_html__( 'Download Gift Card', 'wooznd-smartpack' ) . '</a></dd>';
                } else {
                    $meta_list[] = '<dt class="variation-wznd_Download">' . esc_html__( 'Download:', 'wooznd-smartpack' ) . '</dt>';
                    $meta_list[] = '<dd class="variation-wznd_item_id"><span>' . esc_html__( 'Processing gift card', 'wooznd-smartpack' ) . '</span></dd>';
                }
            }
            $output = '<dl class="variation">' . implode( '', $meta_list ) . '</dl>';
            return $output;
        }

        public static function ProcessGiftCardOrder( $order_id ) {
            $order = wc_get_order( $order_id );

            if ( !$order ) {

                return;
            }

            if ( WooZndUtil::woo_order_status_suppassed( $order->get_status(), WooZndUtil::GetOption( 'create_giftcard_coupon_order_status', 'processing' ) ) == false ) {
                return;
            }

            if ( count( $order->get_items() ) > 0 ) {
                foreach ( $order->get_items() as $item ) {

                    $amount = 0;
                    $qty = $item[ 'qty' ];
                    $delivery_method = WOOZND_GIFTCARD_DELIVERY_EMAIL;
                    $send_to_name = '';
                    $send_to_email = '';
                    $send_to_message = '';
                    $send_date = '';
                    $sender_email = '';
                    $sender_name = '';
                    $item_id = '';

                    if ( $item[ 'type' ] == 'line_item' ) {
                        $product = $order->get_product_from_item( $item );
                        foreach ( $item[ 'item_meta' ] as $key => $value ) {
                            $woo_ver = WC()->version;
                            if ( $woo_ver < "3.0.0" && $woo_ver < "2.7.0" ) {
                                if ( $key == '_wznd_gift_price' ) {
                                    $amount = (wc_format_decimal( $value[ 0 ] ) * $qty);
                                }
                                if ( $key == '_wznd_delivery_method' ) {
                                    $delivery_method = $value[ 0 ];
                                }
                                if ( $key == '_wznd_send_to_name' ) {
                                    $send_to_name = $value[ 0 ];
                                }
                                if ( $key == '_wznd_send_to_email' ) {
                                    $send_to_email = $value[ 0 ];
                                }
                                if ( $key == '_wznd_send_to_message' ) {
                                    $send_to_message = $value[ 0 ];
                                }
                                if ( $key == '_wznd_send_date' ) {
                                    $send_date = $value[ 0 ];
                                }
                                if ( $key == '_wznd_sender_email' ) {
                                    $sender_email = $value[ 0 ];
                                }
                                if ( $key == '_wznd_sender_name' ) {
                                    $sender_name = $value[ 0 ];
                                }
                                if ( $key == '_wznd_item_id' ) {
                                    $item_id = $value[ 0 ];
                                }
                            } else {
                                if ( $key == '_wznd_gift_price' ) {
                                    $amount = (wc_format_decimal( $value ) * $qty);
                                }
                                if ( $key == '_wznd_delivery_method' ) {
                                    $delivery_method = $value;
                                }
                                if ( $key == '_wznd_send_to_name' ) {
                                    $send_to_name = $value;
                                }
                                if ( $key == '_wznd_send_to_email' ) {
                                    $send_to_email = $value;
                                }
                                if ( $key == '_wznd_send_to_message' ) {
                                    $send_to_message = $value;
                                }
                                if ( $key == '_wznd_send_date' ) {
                                    $send_date = $value;
                                }
                                if ( $key == '_wznd_sender_email' ) {
                                    $sender_email = $value;
                                }
                                if ( $key == '_wznd_sender_name' ) {
                                    $sender_name = $value;
                                }
                                if ( $key == '_wznd_item_id' ) {
                                    $item_id = $value;
                                }
                            }
                        }

                        if ( $amount > 0 ) {
                            global $wooznd_giftcard;
                            WooZndUtil::UpdateOption( 'giftcard_buzy', 'yes' );
                            $attr = array();
                            $attr[ 'id' ] = $item_id;
                            $attr[ 'amount' ] = $amount;
                            $attr[ 'coupon_amount' ] = $amount;
                            $attr[ 'delivery_method' ] = $delivery_method;
                            $attr[ 'receiver_name' ] = $send_to_name;
                            $attr[ 'receiver_email' ] = $send_to_email;
                            $attr[ 'message' ] = $send_to_message;
                            $attr[ 'sender_name' ] = $sender_name;
                            $attr[ 'sender_email' ] = $sender_email;
                            $attr[ 'send_date' ] = $send_date;

                            WooZndGiftCardDB::CreateGiftCardFromProduct( $product->get_id(), $attr );

                            $template_id = get_post_meta( $product->get_id(), '_wznd_giftcard_template', true );
                            $coupon_code = WooZndGiftCardDB::GetCouponCodeByGiftCardId( $item_id );

                            if ( $coupon_code != '' ) {
                                $wooznd_giftcard = WooZndGiftCardDB::GetGiftCard( $item_id );
                                wznd_qrcode_or_barcode( $coupon_code );
                                wznd_create_giftcard_pdf( $item_id, $template_id );
                            }
                            WooZndUtil::UpdateOption( 'giftcard_buzy', 'no' );
                        }
                    }
                }
            }
        }

        public static function DisplayCheckoutCouponMessage() {
            return esc_html__( 'Have a Gift Card or Coupon Code?', 'wooznd-smartpack' ) . ' <a href="#" class="showcoupon">' . esc_html__( 'Click here to enter your code', 'wooznd-smartpack' ) . '</a>';
        }

        public static function SetCouponDiscountSession( $cart_object ) {
            foreach ( WC()->cart->get_applied_coupons() as $coupon ) {
                WC()->session->set( '_wznd_' . $coupon, WC()->cart->get_coupon_discount_amount( $coupon ) );
            }
        }

        public static function ProcessGiftCardCouponDiscount( $order_id ) {

            $order = wc_get_order( $order_id );

            if ( !$order ) {

                return;
            }

            if ( $order->get_used_coupons() ) {
                foreach ( $order->get_used_coupons() as $coupon ) {
                    $amount = WC()->session->get( '_wznd_' . $coupon, 0 );
                    WooZndGiftCardDB::DebitGiftCardAmount( $coupon, $amount );
                    WC()->session->set( '_wznd_' . $coupon, 0 );
                }
            }
        }

        public static function FilterPaymentMethods( $gateways ) {

            $found = false;
            if ( isset( WC()->cart->cart_contents ) ) {
                foreach ( WC()->cart->cart_contents as $value ) {
                    if ( get_post_meta( $value[ 'product_id' ], "_wznd_enable_giftcard", true ) == 'yes' ) {
                        $found = true;
                    }
                }
            }

            if ( $found == false ) {
                return $gateways;
            }


            if ( is_admin() ) {
                return $gateways;
            }

            $payment_methods = WooZndUtil::GetOption( 'giftcard_payment_methods', '' );
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

        public static function ProcessGiftCardsActivities() {
            if ( WooZndUtil::GetOption( 'giftcard_buzy', 'no' ) == 'no' ) {
                self::SendDueCards();
                self::RefundCards();
                self::RemoveExpiredCards();
            }
        }

        public static function InitActivity() {

            if ( WooZndUtil::GetOption( 'run_gift_activity', false ) == true ) {
                return;
            }

            WooZndUtil::UpdateOption( 'run_gift_activity', true );
            self::ProcessGiftCardsActivities();
            WooZndUtil::UpdateOption( 'run_gift_activity', false );
        }

        private static function SendDueCards() {
            $gifts = WooZndGiftCardDB::GetGiftCards( '', WOOZND_GIFTCARD_STATUS_PENDING, 0, 10 );

            foreach ( $gifts as $card ) {
                if ( $card[ 'delivery_method' ] != WOOZND_GIFTCARD_DELIVERY_OFFLINE && $card[ 'delivery_method' ] != WOOZND_GIFTCARD_DELIVERY_SHIP ) {
                    do_action( 'wooznd_giftcard_before_sendcard', $card[ 'id' ], $card[ 'coupon_id' ] );
                }
                $current_time = date_format( DateTime::createFromFormat( "Y-m-d", current_time( 'Y-m-d' ) ), 'U' );
                $gift_time = date_format( DateTime::createFromFormat( "Y-m-d H:i:s", $card[ 'send_date' ] ), 'U' );
                if ( $current_time >= $gift_time ) {
                    WooZndGiftCardDB::UpdateGiftCardStatus( $card[ 'id' ], WOOZND_GIFTCARD_STATUS_SENT );
                }
                if ( $card[ 'delivery_method' ] != WOOZND_GIFTCARD_DELIVERY_OFFLINE && $card[ 'delivery_method' ] != WOOZND_GIFTCARD_DELIVERY_SHIP ) {
                    do_action( 'wooznd_giftcard_after_sendcard', $card[ 'id' ], $card[ 'coupon_id' ] );
                }
            }
        }

        private static function RefundCards() {
            if ( WooZndUtil::GetOption( 'giftcard_refund_mode', 'auto' ) == 'no-refund' ) {
                return;
            }
            $gifts = WooZndGiftCardDB::GetExpiredGiftCards( '', -1, 0, 10 );
            foreach ( $gifts as $card ) {

                WooZndGiftCardDB::UpdateGiftCardStatus( $card[ 'id' ], WOOZND_GIFTCARD_STATUS_REFUNDED );
                $user_id = 0;
                $amount = $card[ 'coupon_amount' ];
                try {
                    $card_from_email = trim( $card[ 'from_email' ] );
                    $card_to_email = trim( $card[ 'to_email' ] );

                    $card_user = get_user_by( 'email', $card_from_email );

                    if ( $card[ 'status' ] == WOOZND_GIFTCARD_STATUS_USED ) {
                        $card_user = get_user_by( 'email', $card_to_email );
                    }

                    if ( WooZndUtil::GetOption( 'giftcard_refund_mode', 'auto' ) == 'buyer' ) {
                        $card_user = get_user_by( 'email', $card_from_email );
                    }

                    if ( WooZndUtil::GetOption( 'giftcard_refund_mode', 'auto' ) == 'user' ) {
                        $card_user = get_user_by( 'email', $card_to_email );
                    }
                    if ( isset( $card_user->ID ) ) {
                        $user_id = $card_user->ID;
                    }
                } catch ( Exception $ex ) {
                    
                }


                if ( $user_id > 0 && $amount > 0 && $card[ 'status' ] != WOOZND_GIFTCARD_STATUS_REFUNDED ) {
                    $amount = $card[ 'coupon_amount' ];
                    $trans_id = WooZndWalletTransactionDB::CreditWallet( $user_id, $amount, WOOZND_WALLET_TRANSANCTION_REFUND, WooZndUtil::GetOption( 'system_login', 'system_admin' ), WooZndUtil::GetOption( 'giftcard_refund_remark', esc_html__( 'Gift card refund', 'wooznd-smartpack' ) ) );
                    if ( $trans_id > 0 ) {
                        WooZndWalletTransactionDB::TransactionComplete( $trans_id, WooZndUtil::GetOption( 'system_login', 'system_admin' ), WooZndUtil::GetOption( 'giftcard_refund_remark', esc_html__( 'Gift card refund', 'wooznd-smartpack' ) ) );
                        do_action( 'wooznd_giftcard_refunded', $trans_id, $card[ 'id' ], $card[ 'coupon_id' ] );
                    }
                }
            }
        }

        private static function RemoveExpiredCards() {

            if ( WooZndUtil::GetOption( 'remove_expired_giftcard', 'no' ) == 'no' ) {
                return;
            }

            $gifts = WooZndGiftCardDB::GetExpiredGiftCards( '', -1, 0, 10 );

            foreach ( $gifts as $card ) {
                $current_time = date_format( DateTime::createFromFormat( "Y-m-d", current_time( 'Y-m-d' ) ), 'U' );
                $gift_time = date_format( DateTime::createFromFormat( "Y-m-d H:i:s", $card[ 'expiry_date' ] ), 'U' );
                if ( ($current_time + (WooZndUtil::GetOption( 'remove_expired_giftcard_after_days', 2 ) * DAY_IN_SECONDS)) > $gift_time ) {
                    WooZndGiftCardDB::DeleteGiftCard( $card[ 'id' ] );
                }
            }
        }

    }

}
