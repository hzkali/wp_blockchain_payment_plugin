<?php
add_action( 'add_meta_boxes', 'wooznd_add_giftcard_resend_metabox' );

function wooznd_add_giftcard_resend_metabox() {

    add_meta_box( 'wznd_giftcard_css', esc_html__( 'Gift Card Receivers', 'wooznd-smartpack' ), 'wooznd_giftcard_resend', 'shop_order' );
}

function wooznd_giftcard_resend( $post ) {

    wp_nonce_field( basename( __FILE__ ), 'wznd_giftcard_nonce' );

    $order = wc_get_order( $post->ID );

    if ( !$order ) {

        return;
    }

    if ( count( $order->get_items() ) > 0 ) {
        $giftcards = [];
        foreach ( $order->get_items() as $item ) {
            $item_id = '';
            if ( $item[ 'type' ] == 'line_item' ) {
                foreach ( $item[ 'item_meta' ] as $key => $value ) {
                    $woo_ver = WC()->version;
                    if ( $woo_ver < "3.0.0" && $woo_ver < "2.7.0" ) {
                        if ( $key == '_wznd_item_id' ) {
                            $item_id = $value[ 0 ];
                        }
                    } else {
                        if ( $key == '_wznd_item_id' ) {

                            $item_id = $value;
                        }
                    }
                }
                if ( !empty( $item_id ) ) {
                    $gftcrd = WooZndGiftCardDB::GetGiftCard( $item_id );
                    if ( isset( $gftcrd[ 'id' ] ) ) {
                        $giftcards[] = $gftcrd;
                    }
                }
            }
        }
        $cnt = 0;
        foreach ( $giftcards as $giftcard ) {
            $cnt++;
            ?>
            <p> 
                <input type="text" value="<?php echo esc_attr( $giftcard[ 'to_name' ] ); ?>" placeholder="<?php echo esc_html__( 'Recipient name:' ); ?>" name="giftcard_resend[<?php echo $item_id; ?>][toname]" />
                <input type="email" value="<?php echo esc_attr( $giftcard[ 'to_email' ] ); ?>" placeholder="<?php echo esc_html__( 'Recipient email:' ); ?>" name="giftcard_resend[<?php echo $item_id; ?>][toemail]" />
                <input type="text" value="<?php echo strtoupper( WooZndGiftCardDB::GetCouponCodeByGiftCardId( $item_id ) ); ?>" disabled="disabled" />
            </p>        
            <?php
        }
        if ( $cnt > 0 ) {
            ?>
            <p> 
                <input type="submit" value="<?php echo esc_html__( 'Resend Gift Cards', 'wooznd-smartpack' ); ?>" name="_wznd_giftcard_resend" class="button button-primary" />
            </p>
            <?php
        } else {
            ?>
            <p>
                <?php echo esc_html__( 'No gift card found in this order.', 'wooznd-smartpack' ); ?>
            </p>
            <?php
        }
    }
}

add_action( 'save_post', 'giftcard_resend_meta_save' );

function giftcard_resend_meta_save( $post_id ) {

    // Checks save status
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'wznd_giftcard_nonce' ] ) && wp_verify_nonce( $_POST[ 'wznd_giftcard_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

    // Exits script depending on save status
    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }

    // Checks for input and sanitizes/saves if needed
    if ( isset( $_POST[ '_wznd_giftcard_resend' ] ) ) {
        $resend_gifts = isset( $_POST[ 'giftcard_resend' ] ) ? $_POST[ 'giftcard_resend' ] : [];
        WooZndUtil::UpdateOption( 'giftcard_buzy', 'yes' );
        foreach ( $resend_gifts as $key => $gift ) {
            WooZndGiftCardDB::UpdateReceiver( $key, $gift[ 'toname' ], $gift[ 'toemail' ] );
        }
        WooZndUtil::UpdateOption( 'giftcard_buzy', 'no' );
    }
}
