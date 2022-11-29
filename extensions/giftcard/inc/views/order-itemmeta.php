<div class="view">
    <table class="display_meta" cellspacing="0">                        
        <?php
        if ( !empty( $coupon ) ) {
            ?>

            <tr>
                <?php
                if ( WooZndUtil::GetOption( 'giftcard_codechart_type', 'br' ) == 'br' ) {
                    ?>

                    <td colspan="2">
                        <div class="wznd_barcode">
                            <img src="<?php echo WP_CONTENT_URL . '/uploads/woo-smart-pack/barcodes/br_' . $coupon . '.png'; ?>" alt="" />
                            <br />
                            <?php echo strtoupper( $coupon ); ?>
                        </div>
                    </td>
                    <?php
                } else {
                    ?>

                    <td colspan="2">
                        <div class="wznd_qrcode">
                            <img src="<?php echo WP_CONTENT_URL . '/uploads/woo-smart-pack/qrcodes/qr_' . $coupon . '.png'; ?>" alt="" />
                            <br />
                            <?php echo strtoupper( $coupon ); ?>
                        </div>
                    </td>
                    <?php
                }
                ?>                
            </tr>
            <tr>
                <th><?php echo esc_html__( 'Coupon:', 'wooznd-smartpack' ); ?></th>
                <td><?php echo strtoupper( $coupon ); ?></td>
            </tr> 
            <?php
        }

        foreach ( $all_meta_data as $data_meta_key => $value ) {
            if ( $data_meta_key == '_wznd_delivery_method' ) {
                $delivary_method = esc_html__( 'Email Address', 'wooznd-smartpack' );
                if ( $value[ 0 ] == WOOZND_GIFTCARD_DELIVERY_OFFLINE ) {
                    $delivary_method = esc_html__( 'Print & Send', 'wooznd-smartpack' );
                }
                if ( $value[ 0 ] == WOOZND_GIFTCARD_DELIVERY_SHIP ) {
                    $delivary_method = esc_html__( 'Shipping Address', 'wooznd-smartpack' );
                }
                ?>
                <tr>
                    <th><?php echo esc_html__( 'Delivery Method:', 'wooznd-smartpack' ); ?></th>
                    <td><?php echo $delivary_method; ?></td>
                </tr>
                <?php
            }
            if ( $data_meta_key == '_wznd_send_to_name' ) {
                ?>
                <tr>
                    <th style="min-width: 95px;"><?php echo esc_html__( 'Recipient name:', 'wooznd-smartpack' ); ?></th>
                    <td><p><?php echo wp_kses_post( $value[ 0 ] ); ?></p></td>
                </tr>
                <?php
            }
            if ( $data_meta_key == '_wznd_send_to_email' ) {
                ?>
                <tr>
                    <th><?php echo esc_html__( 'Recipient email:', 'wooznd-smartpack' ); ?></th>
                    <td><?php echo wp_kses_post( $value[ 0 ] ); ?></td>
                </tr>
                <?php
            }
            if ( $data_meta_key == '_wznd_send_to_message' ) {
                ?>
                <tr>
                    <th><?php echo esc_html__( 'Message:', 'wooznd-smartpack' ); ?></th>
                    <td><?php echo wp_kses_post( $value[ 0 ] ); ?></td>
                </tr>
                <?php
            }
            if ( $data_meta_key == '_wznd_send_date' ) {
                ?>
                <tr>
                    <th><?php echo esc_html__( 'Send Date:', 'wooznd-smartpack' ); ?></th>
                    <td><?php echo wp_kses_post( $value[ 0 ] ); ?></td>
                </tr>
                <?php
            }
            if ( $data_meta_key == '_wznd_sender_name' ) {
                ?>
                <tr>
                    <th><?php echo esc_html__( 'Sender name:', 'wooznd-smartpack' ); ?></th>
                    <td><?php echo wp_kses_post( $value[ 0 ] ); ?></td>
                </tr>
                <?php
            }

            if ( $data_meta_key == '_wznd_sender_email' ) {
                ?>
                <tr>
                    <th><?php echo esc_html__( 'Sender email:', 'wooznd-smartpack' ); ?></th>
                    <td><?php echo wp_kses_post( $value[ 0 ] ); ?></td>
                </tr>
                <?php
            }
        }
        if ( !empty( $coupon ) ) {
            ?>
            <tr>
                <td colspan="2"><a href="<?php echo WP_CONTENT_URL . '/uploads/woo-smart-pack/giftcards/giftcard' . $item_id . '.pdf' ?>" target="_blank"><?php echo esc_html__( 'Download Gift Card', 'wooznd-smartpack' ); ?></a></td>
            </tr>
            <?php
        }
        ?>
    </table>
</div>