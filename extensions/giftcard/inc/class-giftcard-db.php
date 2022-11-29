<?php

if ( !defined( 'WOOZND_GIFTCARD_STATUS_PENDING' ) ) {

    define( 'WOOZND_GIFTCARD_STATUS_PENDING', 0 );
}
if ( !defined( 'WOOZND_GIFTCARD_STATUS_SENT' ) ) {

    define( 'WOOZND_GIFTCARD_STATUS_SENT', 1 );
}
if ( !defined( 'WOOZND_GIFTCARD_STATUS_USED' ) ) {

    define( 'WOOZND_GIFTCARD_STATUS_USED', 2 );
}
if ( !defined( 'WOOZND_GIFTCARD_STATUS_EXHAUSTED' ) ) {

    define( 'WOOZND_GIFTCARD_STATUS_EXHAUSTED', 3 );
}
if ( !defined( 'WOOZND_GIFTCARD_STATUS_REFUNDED' ) ) {

    define( 'WOOZND_GIFTCARD_STATUS_REFUNDED', 4 );
}
if ( !defined( 'WOOZND_GIFTCARD_DELIVERY_OFFLINE' ) ) {

    define( 'WOOZND_GIFTCARD_DELIVERY_OFFLINE', 0 );
}
if ( !defined( 'WOOZND_GIFTCARD_DELIVERY_SHIP' ) ) {

    define( 'WOOZND_GIFTCARD_DELIVERY_SHIP', 1 );
}
if ( !defined( 'WOOZND_GIFTCARD_DELIVERY_EMAIL' ) ) {

    define( 'WOOZND_GIFTCARD_DELIVERY_EMAIL', 2 );
}


if ( !class_exists( 'WooZndGiftCardDB' ) ) {

    class WooZndGiftCardDB {

        public static function CreateGiftCardFromProduct( $product_id, $props ) {
            if ( self::giftcard_item_exist( $props[ 'id' ] ) ) {
                return true;
            }
            $attrs = self::GetAttributesFromProduct( $product_id, self::GetAttributesFromSettings( $props ) );
            $attrs[ 'coupon_code' ] = WooZndUtil::GenRandomPattern( $attrs[ 'coupon_pattern' ] );
            $attrs[ 'expiry_date' ] = WooZndUtil::GetCurrentTimeOffset( absint( $attrs[ 'expiry_days' ] ), 'Y-m-d', $attrs[ 'send_date' ] );
            $attrs[ 'description' ] = esc_html__( 'Gift Card Coupon', 'wooznd-smartpack' );
            $attrs[ 'coupon_id' ] = self::create_giftcard_coupon( $attrs );

            if ( $attrs[ 'coupon_id' ] > 0 ) {
                $attrs[ 'status' ] = WOOZND_GIFTCARD_STATUS_PENDING;

                return self::create_giftcard_record( $attrs );
            } else {
                return false;
            }
        }

        public static function CreateGiftCard( $attrs ) {
            $attrs[ 'coupon_id' ] = self::create_giftcard_coupon( $attrs );

            if ( $attrs[ 'coupon_id' ] > 0 ) {
                $attrs[ 'status' ] = WOOZND_GIFTCARD_STATUS_PENDING;

                return self::create_giftcard_record( $attrs );
            } else {
                return false;
            }
        }

        public static function GetGiftCards( $search = '', $status = -1, $startrow = 0, $limit = 25, $orderby = "", $ordertype = 'DESC' ) {
            global $wpdb;
            $retults = array();
            try {

                $order = "ORDER BY {$wpdb->prefix}wooznd_giftcard_items.send_date " . $ordertype;
                if ( $orderby == 'coupon' ) {
                    $order = "ORDER BY {$wpdb->posts}.post_title " . $ordertype;
                }
                if ( $orderby == 'expiry' ) {
                    $order = "ORDER BY {$wpdb->prefix}wooznd_giftcard_items.expiry_date " . $ordertype;
                }
                if ( $orderby == 'amount' ) {
                    $order = "ORDER BY {$wpdb->prefix}wooznd_giftcard_items.amount " . $ordertype;
                }

                $filter = '';
                if ( $status >= WOOZND_GIFTCARD_STATUS_PENDING ) {
                    $q = ($filter == '') ? 'WHERE ' : ' ';
                    $q .= "({$wpdb->prefix}wooznd_giftcard_items.status = %d) ";
                    $filter .= $wpdb->prepare( $q, $status );
                }


                if ( !empty( $search ) ) {
                    $q = ($filter == '') ? 'WHERE ' : 'AND ';
                    $q .= "({$wpdb->posts}.post_title LIKE %s) ";
                    $filter .= $wpdb->prepare( $q, $search );
                }

                $take = $wpdb->prepare( " LIMIT %d,%d", $startrow, $limit );


                $sql = "SELECT {$wpdb->prefix}wooznd_giftcard_items.*, {$wpdb->posts}.post_title AS coupon FROM {$wpdb->prefix}wooznd_giftcard_items LEFT OUTER JOIN {$wpdb->posts} ON {$wpdb->prefix}wooznd_giftcard_items.coupon_id = {$wpdb->posts}.id " . $filter . $order . $take;

                $rows = $wpdb->get_results( $sql, ARRAY_A );
                if ( is_array( $rows ) ) {
                    foreach ( $rows as $row ) {
                        $rw = $row;

                        $rw[ 'coupon_amount' ] = get_post_meta( $row[ 'coupon_id' ], 'coupon_amount', true );
                        $rw[ 'discount_type' ] = get_post_meta( $row[ 'coupon_id' ], 'discount_type', true );
                        $rw[ 'apply_before_tax' ] = get_post_meta( $row[ 'coupon_id' ], 'apply_before_tax', true );
                        $rw[ 'free_shipping' ] = get_post_meta( $row[ 'coupon_id' ], 'free_shipping', true );
                        $rw[ 'minimum_amount' ] = get_post_meta( $row[ 'coupon_id' ], 'minimum_amount', true );
                        $rw[ 'maximum_amount' ] = get_post_meta( $row[ 'coupon_id' ], 'maximum_amount', true );
                        $rw[ 'exclude_sale_items' ] = get_post_meta( $row[ 'coupon_id' ], 'exclude_sale_items', true );
                        $rw[ 'individual_use' ] = get_post_meta( $row[ 'coupon_id' ], 'individual_use', true );
                        $rw[ 'usage_limit_per_user' ] = get_post_meta( $row[ 'coupon_id' ], 'usage_limit_per_user', true );
                        $rw[ 'usage_limit' ] = get_post_meta( $row[ 'coupon_id' ], 'usage_limit', true );

                        $retults[] = $rw;
                    }
                }
            } catch ( Exception $ex ) {
                $retults = array();
            }
            return $retults;
        }

        public static function GetExpiredGiftCards( $search = '', $status = -1, $startrow = 0, $limit = 25, $orderby = "", $ordertype = 'DESC' ) {
            global $wpdb;
            $retults = array();
            try {

                $order = "ORDER BY {$wpdb->prefix}wooznd_giftcard_items.send_date " . $ordertype;
                if ( $orderby == 'coupon' ) {
                    $order = "ORDER BY {$wpdb->posts}.post_title " . $ordertype;
                }
                if ( $orderby == 'expiry' ) {
                    $order = "ORDER BY {$wpdb->prefix}wooznd_giftcard_items.expiry_date " . $ordertype;
                }
                if ( $orderby == 'amount' ) {
                    $order = "ORDER BY {$wpdb->prefix}wooznd_giftcard_items.amount " . $ordertype;
                }

                $filter = '';
                if ( $status >= WOOZND_GIFTCARD_STATUS_PENDING ) {
                    $q = ($filter == '') ? 'WHERE ' : ' ';
                    $q .= "({$wpdb->prefix}wooznd_giftcard_items.status = %d) ";
                    $filter .= $wpdb->prepare( $q, $status );
                }


                if ( !empty( $search ) ) {
                    $q = ($filter == '') ? 'WHERE ' : 'AND ';
                    $q .= "({$wpdb->posts}.post_title LIKE %s) ";
                    $filter .= $wpdb->prepare( $q, $search );
                }

                $q = ($filter == '') ? 'WHERE ' : 'AND ';
                $q .= "({$wpdb->prefix}wooznd_giftcard_items.expiry_date < %s) ";
                $filter .= $wpdb->prepare( $q, current_time( 'Y-m-d' ) );

                $take = $wpdb->prepare( " LIMIT %d,%d", $startrow, $limit );

                $sql = "SELECT {$wpdb->prefix}wooznd_giftcard_items.*, {$wpdb->posts}.post_title AS coupon FROM {$wpdb->prefix}wooznd_giftcard_items LEFT OUTER JOIN {$wpdb->posts} ON {$wpdb->prefix}wooznd_giftcard_items.coupon_id = {$wpdb->posts}.id " . $filter . $order . $take;

                $rows = $wpdb->get_results( $sql, ARRAY_A );
                if ( is_array( $rows ) ) {
                    foreach ( $rows as $row ) {
                        $rw = $row;
                        $rw[ 'coupon_amount' ] = get_post_meta( $row[ 'coupon_id' ], 'coupon_amount', true );
                        $rw[ 'discount_type' ] = get_post_meta( $row[ 'coupon_id' ], 'discount_type', true );
                        $rw[ 'apply_before_tax' ] = get_post_meta( $row[ 'coupon_id' ], 'apply_before_tax', true );
                        $rw[ 'free_shipping' ] = get_post_meta( $row[ 'coupon_id' ], 'free_shipping', true );
                        $rw[ 'minimum_amount' ] = get_post_meta( $row[ 'coupon_id' ], 'minimum_amount', true );
                        $rw[ 'maximum_amount' ] = get_post_meta( $row[ 'coupon_id' ], 'maximum_amount', true );
                        $rw[ 'exclude_sale_items' ] = get_post_meta( $row[ 'coupon_id' ], 'exclude_sale_items', true );
                        $rw[ 'individual_use' ] = get_post_meta( $row[ 'coupon_id' ], 'individual_use', true );
                        $rw[ 'usage_limit_per_user' ] = get_post_meta( $row[ 'coupon_id' ], 'usage_limit_per_user', true );
                        $rw[ 'usage_limit' ] = get_post_meta( $row[ 'coupon_id' ], 'usage_limit', true );
                        $retults[] = $rw;
                    }
                }
            } catch ( Exception $ex ) {
                $retults = array();
            }
            return $retults;
        }

        public static function GetGiftCardsCount( $search = '', $status = -1 ) {
            global $wpdb;
            try {

                $filter = '';
                if ( $status >= WOOZND_GIFTCARD_STATUS_PENDING ) {
                    $q = ($filter == '') ? 'WHERE ' : ' ';
                    $q .= "({$wpdb->prefix}wooznd_giftcard_items.status = %d)";
                    $filter = $wpdb->prepare( $q, $status );
                }

                if ( !empty( $search ) ) {
                    $q = ($filter == '') ? 'WHERE ' : 'AND ';
                    $q .= "({$wpdb->posts}.post_title LIKE %s)";
                    $filter .= $wpdb->prepare( $q, $search );
                }

                $sql = "SELECT COUNT({$wpdb->prefix}wooznd_giftcard_items.id) FROM {$wpdb->prefix}wooznd_giftcard_items LEFT OUTER JOIN {$wpdb->posts} ON {$wpdb->prefix}wooznd_giftcard_items.coupon_id = {$wpdb->posts}.id " . $filter;
                return $wpdb->get_var( $sql );
            } catch ( Exception $ex ) {
                return 0;
            }
        }

        public static function GetExpiredGiftCardsCount( $search = '', $status = -1 ) {
            global $wpdb;
            try {

                $filter = '';
                if ( $status >= WOOZND_GIFTCARD_STATUS_PENDING ) {
                    $q = ($filter == '') ? 'WHERE ' : ' ';
                    $q .= "({$wpdb->prefix}wooznd_giftcard_items.status = %d)";
                    $filter = $wpdb->prepare( $q, $status );
                }


                if ( !empty( $search ) ) {
                    $q = ($filter == '') ? 'WHERE ' : 'AND ';
                    $q .= "({$wpdb->posts}.post_title LIKE %s)";
                    $filter .= $wpdb->prepare( $q, $search );
                }

                $q = ($filter == '') ? 'WHERE ' : 'AND ';
                $q .= "({$wpdb->prefix}wooznd_giftcard_items.expiry_date < %s)";
                $filter .= $wpdb->prepare( $q, current_time( 'Y-m-d' ) );
                $sql = "SELECT COUNT({$wpdb->prefix}wooznd_giftcard_items.id) FROM {$wpdb->prefix}wooznd_giftcard_items LEFT OUTER JOIN {$wpdb->posts} ON {$wpdb->prefix}wooznd_giftcard_items.coupon_id = {$wpdb->posts}.id " . $filter;
                return $wpdb->get_var( $sql );
            } catch ( Exception $ex ) {
                return 0;
            }
        }

        public static function GetGiftCardByCode( $coupon_code ) {
            $coupon_id = self::get_coupon_id_from_coupon_code( $coupon_code );
            if ( $coupon_id > 0 ) {
                return self::GetGiftCard( self::get_giftcard_id_from_coupon_id( $coupon_id ) );
            }
            return array();
        }

        public static function GetGiftCard( $id ) {
            if ( $id <= 0 ) {
                return false;
            }
            global $wpdb;
            try {

                $sql = "SELECT * FROM {$wpdb->prefix}wooznd_giftcard_items "
                        . "WHERE (id=%d)";
                $sql = $wpdb->prepare( $sql, $id );
                $row = $wpdb->get_row( $sql, ARRAY_A );
                if ( isset( $row[ 'id' ] ) ) {
                    $result = $row;
                    $result[ 'coupon' ] = self::GetCouponCodeByGiftCardId( $id );
                    $result[ 'coupon_amount' ] = get_post_meta( $row[ 'coupon_id' ], 'coupon_amount', true );
                    $result[ 'discount_type' ] = get_post_meta( $row[ 'coupon_id' ], 'discount_type', true );
                    $result[ 'apply_before_tax' ] = get_post_meta( $row[ 'coupon_id' ], 'apply_before_tax', true );
                    $result[ 'free_shipping' ] = get_post_meta( $row[ 'coupon_id' ], 'free_shipping', true );
                    $result[ 'minimum_amount' ] = get_post_meta( $row[ 'coupon_id' ], 'minimum_amount', true );
                    $result[ 'maximum_amount' ] = get_post_meta( $row[ 'coupon_id' ], 'maximum_amount', true );
                    $result[ 'exclude_sale_items' ] = get_post_meta( $row[ 'coupon_id' ], 'exclude_sale_items', true );
                    $result[ 'individual_use' ] = get_post_meta( $row[ 'coupon_id' ], 'individual_use', true );
                    $result[ 'usage_limit_per_user' ] = get_post_meta( $row[ 'coupon_id' ], 'usage_limit_per_user', true );
                    $result[ 'usage_limit' ] = get_post_meta( $row[ 'coupon_id' ], 'usage_limit', true );
                    return $result;
                }
                return array();
            } catch ( Exception $ex ) {
                return array();
            }
        }

        public static function GetCouponCodeByGiftCardId( $id ) {
            if ( $id <= 0 ) {
                return '';
            }
            global $wpdb;
            try {
                $coupon_id = self::get_coupon_id_from_giftcard_id( $id );
                if ( $coupon_id > 0 ) {
                    $sql = "SELECT post_title FROM {$wpdb->posts} "
                            . "WHERE (ID=%d)";
                    $sql = $wpdb->prepare( $sql, $coupon_id );
                    $coupon = $wpdb->get_var( $sql );
                    return $coupon;
                }
                return '';
            } catch ( Exception $ex ) {
                return '';
            }
        }

        public static function GetCouponCodeById( $coupon_id ) {
            if ( $coupon_id <= 0 ) {
                return '';
            }
            global $wpdb;
            try {
                $sql = "SELECT post_title FROM {$wpdb->posts} "
                        . "WHERE (ID=%d)";
                $sql = $wpdb->prepare( $sql, $coupon_id );
                $coupon = $wpdb->get_var( $sql );
                return $coupon;
            } catch ( Exception $ex ) {
                return '';
            }
        }

        public static function DebitGiftCardAmount( $coupon, $amount ) {

            $coupon_data = new WC_Coupon( $coupon );
            if ( !empty( $coupon_data->id ) && self::IsGiftCardCoupon( $coupon_data->id ) ) {
                $old_amount = get_post_meta( $coupon_data->id, 'coupon_amount', true );
                $new_amount = $old_amount - $amount;
                $status = WOOZND_GIFTCARD_STATUS_USED;
                if ( $new_amount < 0 ) {
                    $new_amount = 0;
                    $status = WOOZND_GIFTCARD_STATUS_EXHAUSTED;
                }

                if ( $coupon_data->is_type( array( 'percent', 'percent_product' ) ) ) {
                    $new_amount = 0;
                    $status = WOOZND_GIFTCARD_STATUS_EXHAUSTED;
                }
                $giftcard_id = self::get_giftcard_id_from_coupon_id( $coupon_data->id );
                self::UpdateGiftCardStatus( $giftcard_id, $status );
                update_post_meta( $coupon_data->id, 'coupon_amount', $new_amount );
                return true;
            } else {
                return false;
            }
        }

        public static function DeleteGiftCard( $id ) {
            if ( $id <= 0 ) {
                return false;
            }
            global $wpdb;
            try {

                $coupon_id = self::get_coupon_id_from_giftcard_id( $id );
                $sql_p = "DELETE FROM {$wpdb->posts} WHERE (id=%d)";
                $sql_p = $wpdb->prepare( $sql_p, $coupon_id );
                $nof = $wpdb->query( $sql_p );
                if ( $nof > 0 ) {
                    $sql = "DELETE FROM {$wpdb->prefix}wooznd_giftcard_items "
                            . "WHERE (id=%d)";
                    $sql = $wpdb->prepare( $sql, $id );
                    $nofr = $wpdb->query( $sql );
                    return ($nofr > 0);
                }
                return false;
            } catch ( Exception $ex ) {
                return false;
            }
        }

        public static function UpdateGiftCard( $attrs ) {
            if ( !isset( $attrs[ 'id' ] ) ) {
                return false;
            }
            if ( isset( $attrs[ 'id' ] ) && $attrs[ 'id' ] <= 0 ) {
                return false;
            }
            self::update_giftcard_coupon( $attrs );

            return self::update_giftcard_record( $attrs );
        }

        public static function UpdateGiftCardStatus( $id, $status = WOOZND_GIFTCARD_STATUS_PENDING ) {
            if ( $id <= 0 ) {
                return false;
            }
            global $wpdb;
            try {

                $sql = "UPDATE {$wpdb->prefix}wooznd_giftcard_items SET "
                        . "status=%d "
                        . "WHERE (id=%d)";
                $sql = $wpdb->prepare( $sql, $status, $id );

                $nofr = $wpdb->query( $sql );
                return ($nofr > 0);
            } catch ( Exception $ex ) {
                return false;
            }
        }

        public static function UpdateReceiver( $id, $to_name, $to_email ) {

            if ( $id <= 0 ) {
                return false;
            }
            if ( empty( $to_name ) ) {
                return false;
            }
            if ( empty( $to_email ) ) {
                return false;
            }
            $giftcard = self::GetGiftCard( $id );

            if ( $giftcard[ 'status' ] >= WOOZND_GIFTCARD_STATUS_USED ) {
                return false;
            }
            $status = WOOZND_GIFTCARD_STATUS_PENDING;
            global $wpdb;
            try {

                $sql = "UPDATE {$wpdb->prefix}wooznd_giftcard_items SET "
                        . "to_email=%s, "
                        . "to_name=%s, "
                        . "status=%d "
                        . "WHERE (id=%d)";
                $sql = $wpdb->prepare( $sql, $to_email, $to_name, $status, $id );
                $nofr = $wpdb->query( $sql );
                return ($nofr > 0);
            } catch ( Exception $ex ) {
                return false;
            }
        }

        public static function IsGiftCardCoupon( $coupon_id ) {
            if ( $coupon_id <= 0 ) {
                return false;
            }
            global $wpdb;
            try {

                $sql = "SELECT id FROM {$wpdb->prefix}wooznd_giftcard_items "
                        . "WHERE (coupon_id=%d)";
                $sql = $wpdb->prepare( $sql, $coupon_id );
                $id = $wpdb->get_var( $sql );
                return ($id > 0);
            } catch ( Exception $ex ) {

                return false;
            }
        }

        public static function GetAttributesFromProduct( $product_id = 0, $defualt = array() ) {
            $result = $defualt;
            if ( $product_id <= 0 ) {
                return $result;
            }

            $meta_product_ids = get_post_meta( $product_id, "_wznd_giftcard_product_ids", true );
            $meta_exclude_product_ids = get_post_meta( $product_id, "_wznd_giftcard_exclude_product_ids", true );
            $meta_product_categories = get_post_meta( $product_id, "_wznd_giftcard_product_categoties", true );
            $meta_exclude_product_categories = get_post_meta( $product_id, "_wznd_giftcard_exclude_categoties", true );


            $meta_distcount_type = get_post_meta( $product_id, "_wznd_giftcard_discount_type", true );
            if ( !empty( $meta_distcount_type ) ) {
                $result[ 'discount_type' ] = $meta_distcount_type;
            }

            $meta_coupon_pattern = get_post_meta( $product_id, "_wznd_giftcard_coupon_pattern", true );
            if ( !empty( $meta_coupon_pattern ) ) {
                $result[ 'coupon_pattern' ] = $meta_coupon_pattern;
            }
            if ( !empty( $meta_product_ids ) ) {
                $result[ 'product_ids' ] = $meta_product_ids;
            }
            if ( !empty( $meta_exclude_product_ids ) ) {
                $result[ 'exclude_product_ids' ] = $meta_exclude_product_ids;
            }
            if ( !empty( $meta_product_categories ) ) {
                $result[ 'product_categories' ] = $meta_product_categories;
            }
            if ( !empty( $meta_exclude_product_categories ) ) {
                $result[ 'exclude_product_categories' ] = $meta_exclude_product_categories;
            }

            $meta_expiry_days = get_post_meta( $product_id, "_wznd_giftcard_expiry_days", true );
            if ( !empty( $meta_expiry_days ) ) {
                $result[ 'expiry_days' ] = $meta_expiry_days;
            }

            $gift_card_pdf_template_id = get_post_meta( $product_id, "_wznd_giftcard_template", true );
            if ( !empty( $gift_card_pdf_template_id ) ) {
                $result[ 'pdf_template_id' ] = $gift_card_pdf_template_id;
            }

            $gift_card_email_template_id = get_post_meta( $product_id, "_wznd_giftcard_email_template", true );
            if ( !empty( $gift_card_email_template_id ) ) {
                $result[ 'email_template_id' ] = $gift_card_email_template_id;
            }

            if ( isset( $result[ 'product_ids' ] ) && !is_array( $result[ 'product_ids' ] ) ) {
                $result[ 'product_ids' ] = array_filter( array_map( 'intval', explode( ',', $result[ 'product_ids' ] ) ) );
            }
            if ( isset( $result[ 'exclude_product_ids' ] ) && !is_array( $result[ 'exclude_product_ids' ] ) ) {
                $result[ 'exclude_product_ids' ] = array_filter( array_map( 'intval', explode( ',', $result[ 'exclude_product_ids' ] ) ) );
            }
            return $result;
        }

        public static function GetAttributesFromSettings( $defualt = array() ) {
            $result = $defualt;
            $result[ 'coupon_pattern' ] = WooZndUtil::GetOption( 'giftcard_coupon_pattern', 'WZND[N5][A4]' );
            $result[ 'discount_type' ] = WooZndUtil::GetOption( 'giftcard_discount_type', 'fixed_cart' );
            $result[ 'individual_use' ] = WooZndUtil::GetOption( 'giftcard_individual_use', 'no' );
            $result[ 'usage_limit' ] = WooZndUtil::GetOption( 'giftcard_usage_limit', '' );
            $result[ 'usage_limit_per_user' ] = WooZndUtil::GetOption( 'giftcard_usage_limit_per_user', '' );
            $result[ 'apply_before_tax' ] = WooZndUtil::GetOption( 'giftcard_apply_before_tax', 'no' );
            $result[ 'free_shipping' ] = WooZndUtil::GetOption( 'giftcard_free_shipping', 'no' );
            $result[ 'exclude_sale_items' ] = WooZndUtil::GetOption( 'giftcard_exclude_sale_items', 'no' );
            $result[ 'product_ids' ] = WooZndUtil::GetOption( 'giftcard_product_ids', array() );
            $result[ 'exclude_product_ids' ] = WooZndUtil::GetOption( 'giftcard_exclude_product_ids', array() );
            $result[ 'product_categories' ] = WooZndUtil::GetOption( 'giftcard_product_categories', array() );
            $result[ 'exclude_product_categories' ] = WooZndUtil::GetOption( 'giftcard_exclude_product_categories', array() );
            $result[ 'minimum_amount' ] = WooZndUtil::GetOption( 'giftcard_minimum_amount', '' );
            $result[ 'maximum_amount' ] = WooZndUtil::GetOption( 'giftcard_maximum_amount', '' );
            $result[ 'expiry_days' ] = WooZndUtil::GetOption( 'giftcard_expiry_days', '10' );

            if ( !is_array( $result[ 'product_ids' ] ) ) {
                $result[ 'product_ids' ] = array_filter( array_map( 'intval', explode( ',', $result[ 'product_ids' ] ) ) );
            }
            if ( !is_array( $result[ 'exclude_product_ids' ] ) ) {
                $result[ 'exclude_product_ids' ] = array_filter( array_map( 'intval', explode( ',', $result[ 'exclude_product_ids' ] ) ) );
            }

            return $result;
        }

        private static function get_giftcard_id_from_coupon_id( $coupon_id ) {
            if ( $coupon_id <= 0 ) {
                return 0;
            }
            global $wpdb;
            try {

                $sql = "SELECT id FROM {$wpdb->prefix}wooznd_giftcard_items "
                        . "WHERE (coupon_id=%d)";
                $sql = $wpdb->prepare( $sql, $coupon_id );

                $id = $wpdb->get_var( $sql );

                return $id;
            } catch ( Exception $ex ) {

                return 0;
            }
        }

        private static function get_coupon_id_from_giftcard_id( $id ) {
            if ( $id <= 0 ) {
                return 0;
            }
            global $wpdb;
            try {

                $sql = "SELECT coupon_id FROM {$wpdb->prefix}wooznd_giftcard_items "
                        . "WHERE (id=%d)";
                $sql = $wpdb->prepare( $sql, $id );
                $coupon_id = $wpdb->get_var( $sql );

                return $coupon_id;
            } catch ( Exception $ex ) {

                return 0;
            }
        }

        public static function get_coupon_id_from_coupon_code( $coupon_code ) {
            if ( empty( $coupon_code ) ) {
                return 0;
            }
            global $wpdb;
            try {

                $sql = "SELECT id FROM $wpdb->posts "
                        . "WHERE (post_type=%s) AND (post_title=%s)";
                $sql = $wpdb->prepare( $sql, 'shop_coupon', $coupon_code );
                $id = $wpdb->get_var( $sql );
                return ($id > 0) ? $id : 0;
            } catch ( Exception $ex ) {

                return 0;
            }
        }

        private static function giftcard_item_exist( $id ) {
            if ( $id <= 0 ) {
                return false;
            }
            global $wpdb;
            try {

                $sql = "SELECT coupon_id FROM {$wpdb->prefix}wooznd_giftcard_items "
                        . "WHERE (id=%d)";
                $sql = $wpdb->prepare( $sql, $id );
                $coupon_id = $wpdb->get_var( $sql );
                return ($coupon_id > 0);
            } catch ( Exception $ex ) {

                return false;
            }
        }

        private static function create_giftcard_coupon( $attrs ) {


            $woo_ver = WC()->version;

            $coupon_code = '';
            if ( $woo_ver < "3.0.0" && $woo_ver < "2.7.0" ) {
                $coupon_code = wc_format_coupon_code( $attrs[ 'coupon_code' ] );
            } else {
                $coupon_code = apply_filters( 'woocommerce_coupon_code', $attrs[ 'coupon_code' ] );
            }

            $coupon_data = array( 'post_author' => WooZndUtil::GetOption( 'system_id', 1 ),
                'post_title' => $coupon_code,
                'post_status' => 'publish',
                'post_content' => isset( $attrs[ 'description' ] ) ? $attrs[ 'description' ] : esc_html__( 'Gift Card' ),
                'post_excerpt' => isset( $attrs[ 'description' ] ) ? $attrs[ 'description' ] : esc_html__( 'Gift Card' ),
                'post_type' => 'shop_coupon'
            );
            $new_coupon_id = wp_insert_post( $coupon_data );


            if ( $woo_ver < "3.0.0" && $woo_ver < "2.7.0" ) {
                // Add meta
                update_post_meta( $new_coupon_id, 'coupon_amount', $attrs[ 'coupon_amount' ] );
                update_post_meta( $new_coupon_id, 'discount_type', wc_clean( $attrs[ 'discount_type' ] ) ); // fixed_cart, percent, fixed_product, percent_product
                update_post_meta( $new_coupon_id, 'individual_use', $attrs[ 'individual_use' ] ); //yes or no
                update_post_meta( $new_coupon_id, 'product_ids', implode( ',', array_filter( array_map( 'intval', $attrs[ 'product_ids' ] ) ) ) ); //product ids (seperated by comma)
                update_post_meta( $new_coupon_id, 'exclude_product_ids', implode( ',', array_filter( array_map( 'intval', $attrs[ 'exclude_product_ids' ] ) ) ) ); //product ids (seperated by comma)
                update_post_meta( $new_coupon_id, 'usage_limit', absint( $attrs[ 'usage_limit' ] ) ); //integer
                update_post_meta( $new_coupon_id, 'usage_limit_per_user', absint( $attrs[ 'usage_limit_per_user' ] ) ); //integer
                update_post_meta( $new_coupon_id, 'expiry_date', wc_clean( $attrs[ 'expiry_date' ] ) ); //mysql format
                update_post_meta( $new_coupon_id, 'apply_before_tax', $attrs[ 'apply_before_tax' ] ); //yes or no
                update_post_meta( $new_coupon_id, 'free_shipping', $attrs[ 'free_shipping' ] ); //yes or no
                update_post_meta( $new_coupon_id, 'exclude_sale_items', $attrs[ 'exclude_sale_items' ] ); //yes or no
                update_post_meta( $new_coupon_id, 'product_categories', array_map( 'intval', $attrs[ 'product_categories' ] ) ); //array of integer
                update_post_meta( $new_coupon_id, 'exclude_product_categories', array_map( 'intval', $attrs[ 'exclude_product_categories' ] ) ); //array of integer
                update_post_meta( $new_coupon_id, 'minimum_amount', wc_format_decimal( $attrs[ 'minimum_amount' ] ) ); //integer
                update_post_meta( $new_coupon_id, 'maximum_amount', wc_format_decimal( $attrs[ 'maximum_amount' ] ) ); //integer
                do_action( 'woocommerce_coupon_options_save', $new_coupon_id );
            } else {
                $coupon = new WC_Coupon( $new_coupon_id );
                $coupon->set_props( array(
                    'amount' => $attrs[ 'coupon_amount' ],
                    'discount_type' => wc_clean( $attrs[ 'discount_type' ] ),
                    'individual_use' => (isset( $attrs[ 'individual_use' ] ) && $attrs[ 'individual_use' ] == 'yes'),
                    'product_ids' => array_filter( array_map( 'intval', ( array ) $attrs[ 'product_ids' ] ) ),
                    'excluded_product_ids' => array_filter( array_map( 'intval', ( array ) $attrs[ 'exclude_product_ids' ] ) ),
                    'usage_limit' => absint( $attrs[ 'usage_limit' ] ),
                    'usage_limit_per_user' => absint( $attrs[ 'usage_limit_per_user' ] ),
                    'date_expires' => wc_clean( $attrs[ 'expiry_date' ] ),
                    'apply_before_tax' => (isset( $attrs[ 'apply_before_tax' ] ) && $attrs[ 'apply_before_tax' ] == 'yes'),
                    'free_shipping' => (isset( $attrs[ 'free_shipping' ] ) && $attrs[ 'free_shipping' ] == 'yes'),
                    'exclude_sale_items' => (isset( $attrs[ 'exclude_sale_items' ] ) && $attrs[ 'exclude_sale_items' ] == 'yes'),
                    'product_categories' => array_filter( array_map( 'intval', $attrs[ 'product_categories' ] ) ),
                    'excluded_product_categories' => array_filter( array_map( 'intval', $attrs[ 'exclude_product_categories' ] ) ),
                    'minimum_amount' => wc_format_decimal( $attrs[ 'minimum_amount' ] ),
                    'maximum_amount' => wc_format_decimal( $attrs[ 'maximum_amount' ] ),
                ) );
                $coupon->save();
                do_action( 'woocommerce_coupon_options_save', $new_coupon_id, $coupon );
            }
            return $new_coupon_id;
        }

        private static function update_giftcard_coupon( $attrs ) {
            $woo_ver = WC()->version;

            $coupon_code = '';
            if ( $woo_ver < "3.0.0" && $woo_ver < "2.7.0" ) {
                $coupon_code = wc_format_coupon_code( $attrs[ 'coupon_code' ] );
            } else {
                $coupon_code = apply_filters( 'woocommerce_coupon_code', $attrs[ 'coupon_code' ] );
            }

            $coupon_id = $attrs[ 'coupon_code' ];


            if ( $woo_ver < "3.0.0" && $woo_ver < "2.7.0" ) {
                // Add meta
                update_post_meta( $coupon_id, 'coupon_amount', $attrs[ 'coupon_amount' ] );
                update_post_meta( $coupon_id, 'discount_type', wc_clean( $attrs[ 'discount_type' ] ) ); // fixed_cart, percent, fixed_product, percent_product
                update_post_meta( $coupon_id, 'individual_use', $attrs[ 'individual_use' ] ); //yes or no
                update_post_meta( $coupon_id, 'product_ids', implode( ',', array_filter( array_map( 'intval', $attrs[ 'product_ids' ] ) ) ) ); //product ids (seperated by comma)
                update_post_meta( $coupon_id, 'exclude_product_ids', implode( ',', array_filter( array_map( 'intval', $attrs[ 'exclude_product_ids' ] ) ) ) ); //product ids (seperated by comma)
                update_post_meta( $coupon_id, 'usage_limit', absint( $attrs[ 'usage_limit' ] ) ); //integer
                update_post_meta( $coupon_id, 'usage_limit_per_user', absint( $attrs[ 'usage_limit_per_user' ] ) ); //integer
                update_post_meta( $coupon_id, 'expiry_date', wc_clean( $attrs[ 'expiry_date' ] ) ); //mysql format
                update_post_meta( $coupon_id, 'apply_before_tax', $attrs[ 'apply_before_tax' ] ); //yes or no
                update_post_meta( $coupon_id, 'free_shipping', $attrs[ 'free_shipping' ] ); //yes or no
                update_post_meta( $coupon_id, 'exclude_sale_items', $attrs[ 'exclude_sale_items' ] ); //yes or no
                update_post_meta( $coupon_id, 'product_categories', array_map( 'intval', $attrs[ 'product_categories' ] ) ); //array of integer
                update_post_meta( $coupon_id, 'exclude_product_categories', array_map( 'intval', $attrs[ 'exclude_product_categories' ] ) ); //array of integer
                update_post_meta( $coupon_id, 'minimum_amount', wc_format_decimal( $attrs[ 'minimum_amount' ] ) ); //integer
                update_post_meta( $coupon_id, 'maximum_amount', wc_format_decimal( $attrs[ 'maximum_amount' ] ) ); //integer

                do_action( 'woocommerce_coupon_options_save', $coupon_id );
            } else {
                $coupon = new WC_Coupon( $coupon_id );
                $coupon->set_props( array(
                    'amount' => $attrs[ 'coupon_amount' ],
                    'discount_type' => wc_clean( $attrs[ 'discount_type' ] ),
                    'individual_use' => (isset( $attrs[ 'individual_use' ] ) && $attrs[ 'individual_use' ] == 'yes'),
                    'product_ids' => array_filter( array_map( 'intval', ( array ) $attrs[ 'product_ids' ] ) ),
                    'excluded_product_ids' => array_filter( array_map( 'intval', ( array ) $attrs[ 'exclude_product_ids' ] ) ),
                    'usage_limit' => absint( $attrs[ 'usage_limit' ] ),
                    'usage_limit_per_user' => absint( $attrs[ 'usage_limit_per_user' ] ),
                    'date_expires' => wc_clean( $attrs[ 'expiry_date' ] ),
                    'apply_before_tax' => (isset( $attrs[ 'apply_before_tax' ] ) && $attrs[ 'apply_before_tax' ] == 'yes'),
                    'free_shipping' => (isset( $attrs[ 'free_shipping' ] ) && $attrs[ 'free_shipping' ] == 'yes'),
                    'exclude_sale_items' => (isset( $attrs[ 'exclude_sale_items' ] ) && $attrs[ 'exclude_sale_items' ] == 'yes'),
                    'product_categories' => array_filter( array_map( 'intval', $attrs[ 'product_categories' ] ) ),
                    'excluded_product_categories' => array_filter( array_map( 'intval', $attrs[ 'exclude_product_categories' ] ) ),
                    'minimum_amount' => wc_format_decimal( $attrs[ 'minimum_amount' ] ),
                    'maximum_amount' => wc_format_decimal( $attrs[ 'maximum_amount' ] ),
                ) );
                $coupon->save();
                do_action( 'woocommerce_coupon_options_save', $coupon_id, $coupon );
            }
            return $coupon_id;
        }

        private static function create_giftcard_record( $attrs ) {
            if ( isset( $attrs[ 'id' ] ) && $attrs[ 'id' ] <= 0 ) {
                return false;
            }
            global $wpdb;
            try {

                $sql = "INSERT INTO {$wpdb->prefix}wooznd_giftcard_items (id, coupon_id, amount, delivery_method, to_name, to_email, message, from_name, from_email, email_template_id, giftcard_template_id, send_date, expiry_date, status)"
                        . "VALUES (%d,%d,%d,%d,%s,%s,%s,%s,%s,%d,%d,%s,%s,%d)";
                $sql = $wpdb->prepare( $sql, $attrs[ 'id' ], $attrs[ 'coupon_id' ], $attrs[ 'amount' ], $attrs[ 'delivery_method' ], $attrs[ 'receiver_name' ], $attrs[ 'receiver_email' ], $attrs[ 'message' ], $attrs[ 'sender_name' ], $attrs[ 'sender_email' ], $attrs[ 'email_template_id' ], $attrs[ 'pdf_template_id' ], $attrs[ 'send_date' ], $attrs[ 'expiry_date' ], $attrs[ 'status' ] );
                $nofr = $wpdb->query( $sql );
                return ($nofr > 0);
            } catch ( Exception $ex ) {
                return false;
            }
        }

        private static function update_giftcard_record( $attrs ) {
            if ( !isset( $attrs[ 'id' ] ) ) {
                return false;
            }
            if ( isset( $attrs[ 'id' ] ) && $attrs[ 'id' ] <= 0 ) {
                return false;
            }
            global $wpdb;
            try {

                $sql = "UPDATE {$wpdb->prefix}wooznd_giftcard_items SET amount=%d, delivery_method=%d, to_name=%s, to_email=%s, message=%s, from_name=%s, from_email=%s, email_template_id=%d, giftcard_template_id=%d, send_date=%s, expiry_date=%s, status=%d"
                        . " WHERE(id=%d)";
                $sql = $wpdb->prepare( $sql, $attrs[ 'amount' ], $attrs[ 'delivery_method' ], $attrs[ 'receiver_name' ], $attrs[ 'receiver_email' ], $attrs[ 'message' ], $attrs[ 'sender_name' ], $attrs[ 'sender_email' ], $attrs[ 'email_template_id' ], $attrs[ 'pdf_template_id' ], $attrs[ 'send_date' ], $attrs[ 'expiry_date' ], $attrs[ 'status' ], $attrs[ 'id' ] );
                $nofr = $wpdb->query( $sql );
                return ($nofr > 0);
            } catch ( Exception $ex ) {
                return false;
            }
        }

    }

}

