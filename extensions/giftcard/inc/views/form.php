
<?php
if ( $price_type == 'range' ) {
    $price_from = get_post_meta( $product->get_id(), '_wznd_giftcard_from_price', true );
    $price_to = get_post_meta( $product->get_id(), '_wznd_giftcard_to_price', true );
    ?>
    <div class="wznd_gift_card_input wznd_gift_card_price">
        <?php echo esc_html__( 'Enter Gift Price:', 'wooznd-smartpack' ); ?> 
        <input type="number" name="gift_price" placeholder="<?php echo esc_html__( 'Price', 'wooznd-smartpack' ); ?>" value="<?php echo $price_from; ?>" min="<?php echo $price_from; ?>" max="<?php echo $price_to; ?>" step="1" />
    </div>
    <?php
} else if ( $price_type == 'select' ) {
    ?>
    <div class="wznd_gift_card_input wznd_gift_card_price">
        <?php echo esc_html__( 'Enter Gift Price:', 'wooznd-smartpack' ); ?> 
        <select name="gift_price">
            <?php
            $prices = str_getcsv( get_post_meta( $product->get_id(), '_wznd_giftcard_select_price', true ), '|' );
            if ( is_array( $prices ) ) {
                foreach ( $prices as $price ) {
                    ?>
                    <option value="<?php echo $price; ?>"><?php echo wc_price( $price ); ?></option>
                    <?php
                }
            }
            ?>
        </select>
    </div>
    <?php
} else if ( $price_type == 'user' ) {
    ?>
    <div class="wznd_gift_card_input wznd_gift_card_price">
        <p><?php echo esc_html__( 'Enter Gift Price:', 'wooznd-smartpack' ); ?></p> <input type="text" name="gift_price" placeholder="<?php echo esc_html__( 'Price', 'wooznd-smartpack' ); ?>" value="<?php echo esc_attr( isset( $_POST[ 'gift_price' ] ) ? $_POST[ 'gift_price' ] : $product->get_price() ); ?>" />
    </div>
    <?php
} else {
    ?>
    <input type="hidden" name="gift_price" value="<?php echo esc_attr( isset( $_POST[ 'gift_price' ] ) ? $_POST[ 'gift_price' ] : $product->get_price() ); ?>" />
    <?php
}

if ( empty( $delivary_mathod ) && $delivary_mathod == '' ) {
    ?>
    <div class="wznd_gift_card_input">
        <p><?php echo esc_html__( 'Delivery Method:', 'wooznd-smartpack' ); ?></p>
    </div>
    <div class="wznd_gift_card_input wznd_gift_card_delivery">
        <select name="delivary_method">
            <option<?php echo (isset( $_POST[ 'delivary_method' ] ) && $_POST[ 'delivary_method' ] == WOOZND_GIFTCARD_DELIVERY_OFFLINE) ? ' selected="selected"' : ''; ?> value="<?php echo WOOZND_GIFTCARD_DELIVERY_OFFLINE; ?>"><?php echo esc_html__( 'Print & Send', 'wooznd-smartpack' ); ?></option>
            <option<?php echo (isset( $_POST[ 'delivary_method' ] ) && $_POST[ 'delivary_method' ] == WOOZND_GIFTCARD_DELIVERY_SHIP) ? ' selected="selected"' : ''; ?> value="<?php echo WOOZND_GIFTCARD_DELIVERY_SHIP; ?>"><?php echo esc_html__( 'Shipping Address', 'wooznd-smartpack' ); ?></option>
            <option<?php echo ((isset( $_POST[ 'delivary_method' ] ) && $_POST[ 'delivary_method' ] == WOOZND_GIFTCARD_DELIVERY_EMAIL) || (!isset( $_POST[ 'delivary_method' ] ) || sanitize_text_field( $_POST[ 'delivary_method' ] ) == '')) ? ' selected="selected"' : ''; ?> value="<?php echo WOOZND_GIFTCARD_DELIVERY_EMAIL; ?>"><?php echo esc_html__( 'Email Address', 'wooznd-smartpack' ); ?></option>
        </select>
    </div>
    <?php
} else {
    ?>
    <input type="hidden" name="delivary_method" value="<?php echo esc_attr( isset( $_POST[ 'delivary_method' ] ) ? $_POST[ 'delivary_method' ] : WOOZND_GIFTCARD_DELIVERY_EMAIL ); ?>" />
    <?php
}

if ( (empty( $delivary_mathod ) && $delivary_mathod == '') || $delivary_mathod == WOOZND_GIFTCARD_DELIVERY_EMAIL ) {
    ?>
    <div class="wznd_gift_card_input<?php echo $hide_email_fields; ?>">
        <p>Send gift card to:</p>
    </div>
    <?php
    if ( empty( $show_receiver_name ) || $show_receiver_name == 'yes' ) {
        ?>
        <div class="wznd_gift_card_input<?php echo $hide_email_fields; ?>">
            <input type="text" name="send_to_name" placeholder="<?php echo esc_html__( 'Recipient name', 'wooznd-smartpack' ); ?>" value="<?php echo esc_attr( isset( $_POST[ 'send_to_name' ] ) ? $_POST[ 'send_to_name' ] : '' ); ?>" />
        </div>
        <?php
    }
    if ( empty( $show_receiver_email ) || $show_receiver_email == 'yes' ) {
        ?>
        <div class="wznd_gift_card_input<?php echo $hide_email_fields; ?>">            
            <input type="text" name="send_to_email" placeholder="<?php echo esc_html__( 'Recipient email', 'wooznd-smartpack' ); ?>" value="<?php echo esc_attr( isset( $_POST[ 'send_to_email' ] ) ? $_POST[ 'send_to_email' ] : '' ); ?>" />
        </div>
        <?php
    }

    if ( $show_sender_name == 'yes' ) {
        ?>
        <div class="wznd_gift_card_input<?php echo $hide_email_fields; ?>">            
            <input type="text" name="sender_name" placeholder="<?php echo esc_html__( 'Your name (optional)', 'wooznd-smartpack' ); ?>" value="<?php echo esc_attr( isset( $_POST[ 'send_to_email' ] ) ? $_POST[ 'send_to_email' ] : '' ); ?>" />
        </div>
        <?php
    }
    if ( $show_sender_email == 'yes' ) {
        ?>
        <div class="wznd_gift_card_input<?php echo $hide_email_fields; ?>">            
            <input type="text" name="sender_email" placeholder="<?php echo esc_html__( 'Your email (optional)', 'wooznd-smartpack' ); ?>" value="<?php echo esc_attr( isset( $_POST[ 'send_to_email' ] ) ? $_POST[ 'send_to_email' ] : '' ); ?>" />
        </div>
        <?php
    }
    if ( empty( $show_message ) || $show_message == 'yes' ) {
        ?>
        <div class="wznd_gift_card_input">
            <textarea  name="send_to_message" placeholder="<?php echo esc_html__( 'Gift card message', 'wooznd-smartpack' ); ?>"><?php echo esc_html( isset( $_POST[ 'send_to_message' ] ) ? $_POST[ 'send_to_message' ] : '' ); ?></textarea>
        </div>
        <?php
    }
    ?>
    <?php
}
$send_date = get_post_meta( $product->get_id(), '_wznd_giftcard_allow_send_date', true );
if ( $send_date == 'yes' ) {
    ?>
    <div class="wznd_gift_card_input">
        <input type="text" class="datepicker" name="send_date" placeholder="<?php echo esc_html__( 'Gift card Date', 'wooznd-smartpack' ); ?>" value="<?php echo esc_attr( isset( $_POST[ 'send_date' ] ) ? $_POST[ 'send_date' ] : current_time( 'Y-m-d' ) ); ?>" />
    </div>
    <?php
} else {
    ?>
    <input type="hidden" name="send_date" value="<?php echo esc_attr( isset( $_POST[ 'send_date' ] ) ? $_POST[ 'send_date' ] : current_time( 'Y-m-d' ) ); ?>" />
   <?php
}