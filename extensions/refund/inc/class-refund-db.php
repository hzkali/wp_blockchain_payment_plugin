<?php

if ( !defined( 'WOOZND_WALLET_REFUND_REQUEST_NONE' ) ) {

    define( 'WOOZND_WALLET_REFUND_REQUEST_NONE', -1 );
}

if ( !defined( 'WOOZND_WALLET_REFUND_REQUEST_PENDING' ) ) {

    define( 'WOOZND_WALLET_REFUND_REQUEST_PENDING', 0 );
}

if ( !defined( 'WOOZND_WALLET_REFUND_REQUEST_APROVED' ) ) {

    define( 'WOOZND_WALLET_REFUND_REQUEST_APROVED', 1 );
}

if ( !defined( 'WOOZND_WALLET_REFUND_REQUEST_REJECTED' ) ) {

    define( 'WOOZND_WALLET_REFUND_REQUEST_REJECTED', 2 );
}

if ( !class_exists( 'WooZndRefundDB' ) ) {

    class WooZndRefundDB {

        public static function CreateRequest( $id, $account_id, $request_amount, $status = WOOZND_WALLET_REFUND_REQUEST_PENDING, $reason = '' ) {
            if ( $id <= 0 || $account_id <= 0 || $request_amount <= 0 ) {
                return false;
            }
            global $wpdb;
            try {
                $amount = WooZndUtil::Encrypt( $request_amount );

                $sql = "INSERT INTO {$wpdb->prefix}wooznd_refund_requests (order_id, account_id, reason, request_amount, request_date, status)"
                        . " VALUES (%d, %d, %s, %s, %s, %d)";

                $sql = $wpdb->prepare( $sql, $id, $account_id, $reason, $amount, current_time( 'mysql' ), $status );
                $nofr = $wpdb->query( $sql );
                return ($nofr > 0);
            } catch ( Exception $ex ) {
                return false;
            }
        }

        public static function LoadRequests( $search = '', $status = WOOZND_WALLET_REFUND_REQUEST_NONE, $startrow = 0, $limit = 25 ) {
            global $wpdb;
            $retults = array();

            try {

                $rows = array();

                $filter = '';
                if ( $search != '' ) {
                    $q = ($filter == '') ? 'WHERE ' : ' ';
                    $q .= "(order_id = %d)";
                    $filter = $wpdb->prepare( $q, $search );
                }

                if ( $status >= WOOZND_WALLET_REFUND_REQUEST_PENDING ) {
                    $q = ($filter == '') ? 'WHERE ' : 'AND ';
                    $q .= "(status = %d)";
                    $filter .= $wpdb->prepare( $q, $status );
                }

                $sql = "SELECT * FROM {$wpdb->prefix}wooznd_refund_requests " . $filter . " ORDER BY request_date DESC LIMIT %d,%d";
                $sql = $wpdb->prepare( $sql, $startrow, $limit );

                $rows = $wpdb->get_results( $sql, ARRAY_A );
                if ( is_array( $rows ) ) {
                    foreach ( $rows as $row ) {
                        $rw = $row;
                        $rw[ 'account_number' ] = WooZndWalletAccountDB::GetAccountNumberById( $row[ 'account_id' ] );
                        $rw[ 'request_amount' ] = WooZndUtil::Decrypt( $row[ 'request_amount' ] );
                        $retults[] = $rw;
                    }
                }
            } catch ( Exception $ex ) {
                $retults = array();
            }
            return $retults;
        }

        public static function DeleteRequest( $id ) {
            if ( $id <= 0 ) {
                return false;
            }
            global $wpdb;
            try {
                $sql = "DELETE FROM {$wpdb->prefix}wooznd_refund_requests "
                        . "WHERE (order_id=%d)";
                $sql = $wpdb->prepare( $sql, $id );

                $nofr = $wpdb->query( $sql );
                return ($nofr > 0);
            } catch ( Exception $ex ) {
                return false;
            }
        }

        public static function GetRequestsCount( $search = '', $status = WOOZND_WALLET_REFUND_REQUEST_NONE ) {
            global $wpdb;

            try {

                $rows = array();

                $filter = '';
                if ( $search != '' ) {
                    $q = ($filter == '') ? 'WHERE ' : ' ';
                    $q .= "(order_id = %d)";
                    $filter = $wpdb->prepare( $q, $search );
                }

                if ( $status >= WOOZND_WALLET_REFUND_REQUEST_PENDING ) {
                    $q = ($filter == '') ? 'WHERE ' : 'AND ';
                    $q .= "(status = %d)";
                    $filter .= $wpdb->prepare( $q, $status );
                }

                $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}wooznd_refund_requests " . $filter;
                return $wpdb->get_var( $sql );
            } catch ( Exception $ex ) {
                $retults = 0;
            }
        }

        public static function GetRequestById( $id ) {
            if ( $id <= 0 ) {
                return false;
            }
            global $wpdb;
            try {

                $sql = "SELECT * FROM {$wpdb->prefix}wooznd_refund_requests "
                        . "WHERE (order_id=%d)";
                $sql = $wpdb->prepare( $sql, $id );

                $reslt = $wpdb->get_row( $sql, ARRAY_A );
                $result = $reslt;
                $result[ 'account_number' ] = WooZndWalletAccountDB::GetAccountNumberById( $reslt[ 'account_id' ] );
                $result[ 'request_amount' ] = WooZndUtil::Decrypt( $reslt[ 'request_amount' ] );
                return $result;
            } catch ( Exception $ex ) {
                return array();
            }
        }

        public static function RequestExist( $id ) {
            global $wpdb;
            try {
                $sql = "SELECT order_id FROM {$wpdb->prefix}wooznd_refund_requests "
                        . "WHERE (order_id=%d) LIMIT 1";
                $sql = $wpdb->prepare( $sql, $id );
                return ($wpdb->get_var( $sql ) > 0);
            } catch ( Exception $ex ) {
                return false;
            }
        }

        public static function ProcessRequest( $id, $amount, $reason = '', $update_amount = true ) {
            $reas = $reason;
            $req = self::GetRequestById( $id );
            if ( $reas == '' ) {
                $reas = $req[ 'reason' ];
            }
            $req_amount = $req[ 'request_amount' ];
            if ( $update_amount == true ) {
                $req_amount = $amount;
            }
            if ( self::update_request( $id, $req_amount, WOOZND_WALLET_REFUND_REQUEST_APROVED, $reas ) == true ) {
                return self::RefundWallet( $id, $amount, $reason );
            }
            return 0;
        }

        public static function CancelRequest( $id ) {
            $req = self::GetRequestById( $id );
            $reason = $req[ 'reason' ];
            $amount = $req[ 'request_amount' ];
            if ( self::update_request( $id, $amount, WOOZND_WALLET_REFUND_REQUEST_REJECTED, $reason ) == true ) {
                return true;
            }
            return false;
        }

        public static function RefundWallet( $order_id, $amount, $reason = '' ) {
            $order = new WC_Order( $order_id );
            $woo_ver = WC()->version;
            if ( $woo_ver < "3.0.0" && $woo_ver < "2.7.0" ) {
                $user_id = $order->user_id;
            } else {
                $user_id = $order->get_user_id();
            }

            $order_total = $order->get_total();
            // $order_status = $order->get_status();
            $refunded_amount = 0;

            foreach ( $order->get_refunds() as $refund ) {
                if ( $woo_ver < "3.0.0" && $woo_ver < "2.7.0" ) {
                    $refunded_amount += $refund->get_refund_amount();
                } else {
                    $refunded_amount += $refund->get_amount();
                }
            }

            if ( $refunded_amount >= $order_total ) {
                return 0;
            }

//                    if (($refunded_amount+$amount) >= $order_total) {
//                        $order->update_status('refunded');
//                    }

            $usr = wp_get_current_user();
            $trans_id = WooZndWalletTransactionDB::CreditWallet( $user_id, $amount, WOOZND_WALLET_TRANSANCTION_REFUND, $usr->user_login, $reason );
            if ( $trans_id > 0 ) {
                WooZndWalletTransactionDB::TransactionComplete( $trans_id, $usr->user_login, $reason );
                do_action( 'wooznd_order_renfund_processed', $trans_id, $order_id, $user_id );
            }
            return $trans_id;
        }

        private static function update_request( $id, $amount, $status, $reason = '' ) {
            if ( $id <= 0 ) {
                return false;
            }
            global $wpdb;
            try {
                $request_amount = WooZndUtil::Encrypt( $amount );

                $sql = "UPDATE {$wpdb->prefix}wooznd_refund_requests SET "
                        . "request_amount=%s, "
                        . "status=%d, "
                        . "reason=%s "
                        . "WHERE (order_id=%d)";
                $sql = $wpdb->prepare( $sql, $request_amount, $status, $reason, $id );

                $nofr = $wpdb->query( $sql );
                return ($nofr > 0);
            } catch ( Exception $ex ) {
                return false;
            }
        }

    }

}

