<?php
add_filter( 'product_type_options', 'wooznd_gift_card_product_option' );

function wooznd_gift_card_product_option( $product_type_options ) {

    $product_type_options[ 'wznd_enable_giftcard' ] = array(
        'id' => '_wznd_enable_giftcard',
        'wrapper_class' => 'show_if_simple show_if_variable',
        'label' => esc_html__( 'Gift Card', 'wooznd-smartpack' ),
        'description' => esc_html__( 'Turn this product into gift product.', 'wooznd-smartpack' ),
        'default' => 'no'
    );
    return $product_type_options;
}

add_filter( 'woocommerce_product_data_tabs', 'wooznd_giftcard_product_tabs' );

function wooznd_giftcard_product_tabs( $tabs ) {

    $tabs[ 'wznd_giftcard' ] = array(
        'label' => esc_html__( 'Gift Card', 'wooznd-smartpack' ),
        'target' => 'wznd_giftcard_options',
        'class' => array( 'show_if_simple', 'show_if_variable' ),
    );
    return $tabs;
}

add_action( 'woocommerce_product_data_panels', 'wooznd_giftcard_options_product_tab_content', 99 );

function wooznd_giftcard_options_product_tab_content() {
    global $post;
    ?><div id='wznd_giftcard_options' class='panel woocommerce_options_panel'>
        <div class='options_group'><?php
            wp_nonce_field( basename( __FILE__ ), 'wznd_giftcard_nonce' );
            woocommerce_wp_select(
                    array(
                        'id' => '_wznd_giftcard_discount_type',
                        'label' => esc_html__( 'Discount Type', 'wooznd-smartpack' ),
                        'class' => 'wc-enhanced-select',
                        'style' => 'width:60%',
                        'options' => array(
                            '' => esc_html__( 'Default', 'wooznd-smartpack' ),
                            'fixed_cart' => esc_html__( 'Cart Discount', 'wooznd-smartpack' ),
                            'fixed_product' => esc_html__( 'Product Discount', 'wooznd-smartpack' ),
                        ),
                        'desc_tip' => true,
                        'description' => esc_html__( 'Controls gift card discount type.', 'wooznd-smartpack' )
                    )
            );
            ?>
        </div><div class='options_group'><?php
            wp_nonce_field( basename( __FILE__ ), 'wznd_giftcard_nonce' );
            woocommerce_wp_select(
                    array(
                        'id' => '_wznd_giftcard_price_type',
                        'label' => esc_html__( 'Pricing Type', 'wooznd-smartpack' ),
                        'class' => 'wc-enhanced-select',
                        'style' => 'width:60%',
                        'options' => array(
                            'default' => esc_html__( 'Sales or Regular Price', 'wooznd-smartpack' ),
                            'user' => esc_html__( 'User Price', 'wooznd-smartpack' ),
                            'range' => esc_html__( 'Price Range', 'wooznd-smartpack' ),
                            'select' => esc_html__( 'Price Selection', 'wooznd-smartpack' )
                        ),
                        'desc_tip' => true,
                        'description' => esc_html__( 'Controls gift card pricing type.', 'wooznd-smartpack' )
                    )
            );
            ?>
        </div>
        <div class='options_group show_if_price_range'>
            <?php
            woocommerce_wp_text_input( array(
                'id' => '_wznd_giftcard_from_price',
                'label' => esc_html__( 'Price Minimun', 'wooznd-smartpack' ),
                'style' => 'width:60%',
                'desc_tip' => true,
                'description' => esc_html__( 'Controls minimun price range for price range.', 'wooznd-smartpack' ),
                'type' => 'number',
                'data_type' => 'price',
                'placeholder' => '0.00',
                'custom_attributes' => array(
                    'min' => '0',
                    'step' => '0.01',
                )
            ) );
            woocommerce_wp_text_input( array(
                'id' => '_wznd_giftcard_to_price',
                'label' => esc_html__( 'Price Maximun', 'wooznd-smartpack' ),
                'style' => 'width:60%',
                'desc_tip' => true,
                'description' => esc_html__( 'Controls maximun price range for price range.', 'wooznd-smartpack' ),
                'type' => 'number',
                'data_type' => 'price',
                'placeholder' => '0.00',
                'custom_attributes' => array(
                    'min' => '0',
                    'step' => '0.01',
                )
            ) );
            ?>
        </div>
        <div class='options_group show_if_price_select'>
            <?php
            woocommerce_wp_textarea_input(
                    array(
                        'id' => '_wznd_giftcard_select_price',
                        'label' => esc_html__( 'Price Select Options', 'wooznd-smartpack' ),
                        'style' => 'width:60%',
                        'placeholder' => '5|10|20',
                        'desc_tip' => true,
                        'description' => esc_html__( 'Controls price selection options.', 'wooznd-smartpack' )
                    )
            );
            ?>
        </div>
        <div class="options_group">
            <?php
            woocommerce_wp_text_input( array(
                'id' => '_wznd_giftcard_expiry_days',
                'label' => esc_html__( 'Gift card validity (Days)', 'wooznd-smartpack' ),
                'style' => 'width:60%',
                'default' => '7',
                'desc_tip' => true,
                'description' => esc_html__( 'Gift card validity period in days.', 'wooznd-smartpack' ),
                'type' => 'number',
                'placeholder' => '07',
                'custom_attributes' => array(
                    'min' => '1',
                    'step' => '1',
                )
            ) );
            ?>
        </div>
        <div class='options_group'>
            <?php
            $woo_ver = WC()->version;
            if ( $woo_ver < "3.0.0" && $woo_ver < "2.7.0" ) {
                woocommerce_wp_text_input( array(
                    'id' => '_wznd_giftcard_product_ids',
                    'label' => esc_html__( 'Products', 'wooznd-smartpack' ),
                    'style' => 'width:60%',
                    'desc_tip' => true,
                    'description' => esc_html__( 'Products which need to be in the cart to use this gift card or, for "Product Discounts", which products are discounted.', 'wooznd-smartpack' ),
                    'class' => 'wc-product-search',
                    'custom_attributes' => array(
                        'data-multiple' => 'true',
                        'data-placeholder' => esc_html__( 'Search for a product&hellip;', 'wooznd-smartpack' ),
                        'data-action' => 'woocommerce_json_search_products_and_variations',
                        'data-selected' => WooZndUtil::GetFormattedProductName( get_post_meta( $post->ID, '_wznd_giftcard_product_ids', true ) ),
                    )
                ) );

                woocommerce_wp_text_input( array(
                    'id' => '_wznd_giftcard_exclude_product_ids',
                    'label' => esc_html__( 'Exclude Products', 'wooznd-smartpack' ),
                    'style' => 'width:60%',
                    'desc_tip' => true,
                    'description' => esc_html__( 'Products which must not be in the cart to use this gift card or, for "Product Discounts", which products are not discounted.', 'wooznd-smartpack' ),
                    'class' => 'wc-product-search',
                    'custom_attributes' => array(
                        'data-multiple' => 'true',
                        'data-placeholder' => esc_html__( 'Search for a product&hellip;', 'wooznd-smartpack' ),
                        'data-action' => 'woocommerce_json_search_products_and_variations',
                        'data-selected' => WooZndUtil::GetFormattedProductName( get_post_meta( $post->ID, '_wznd_giftcard_exclude_product_ids', true ) ),
                    )
                ) );
            } else {
                ?>
                <p class="form-field"><label><?php echo esc_html__( 'Products', 'wooznd-smartpack' ); ?></label>
                    <select class="wc-product-search" multiple="multiple" style="width: 60%;" name="_wznd_giftcard_product_ids[]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'wooznd-smartpack' ); ?>" data-action="woocommerce_json_search_products_and_variations">
                        <?php
                        $product_ids = get_post_meta( $post->ID, '_wznd_giftcard_product_ids', true );

                        foreach ( $product_ids as $product_id ) {
                            $product = wc_get_product( $product_id );
                            if ( is_object( $product ) ) {
                                echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
                            }
                        }
                        ?>
                    </select> <?php echo wc_help_tip( esc_html__( 'Products which need to be in the cart to use this gift card or, for "Product Discounts", which products are discounted.', 'wooznd-smartpack' ) ); ?></p>
                <?php ?>
                <p class="form-field"><label><?php echo esc_html__( 'Exclude Products', 'wooznd-smartpack' ); ?></label>
                    <select class="wc-product-search" multiple="multiple" style="width: 60%;" name="_wznd_giftcard_exclude_product_ids[]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'wooznd-smartpack' ); ?>" data-action="woocommerce_json_search_products_and_variations">
                        <?php
                        $product_ids = get_post_meta( $post->ID, '_wznd_giftcard_exclude_product_ids', true );

                        foreach ( $product_ids as $product_id ) {
                            $product = wc_get_product( $product_id );
                            if ( is_object( $product ) ) {
                                echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
                            }
                        }
                        ?>
                    </select> <?php echo wc_help_tip( esc_html__( 'Products which must not be in the cart to use this gift card or, for "Product Discounts", which products are not discounted.', 'wooznd-smartpack' ) ); ?></p>
                <?php
            }
            ?>
        </div>
        <div class='options_group'>
            <p class="form-field"><label for="_wznd_giftcard_product_categoties"><?php echo esc_html__( 'Product categories', 'wooznd-smartpack' ); ?></label>
                <select id="product_categories" name="_wznd_giftcard_product_categoties[]" style="width: 60%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'Any category', 'wooznd-smartpack' ); ?>">
                    <?php
                    $category_ids = ( array ) get_post_meta( $post->ID, '_wznd_giftcard_product_categoties', true );
                    $categories = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );

                    if ( $categories ) {
                        foreach ( $categories as $cat ) {
                            echo '<option value="' . esc_attr( $cat->term_id ) . '"' . selected( in_array( $cat->term_id, $category_ids ), true, false ) . '>' . esc_html( $cat->name ) . '</option>';
                        }
                    }
                    ?>
                </select> <?php echo wc_help_tip( esc_html__( 'A product must be in this category for the gift card to remain valid or, for "Product Discounts", products in these categories will be discounted.', 'wooznd-smartpack' ) ); ?>
            </p>
            <p class="form-field"><label for="_wznd_giftcard_exclude_categoties"><?php echo esc_html__( 'Exclude categories', 'wooznd-smartpack' ); ?></label>
                <select id="exclude_product_categories" name="_wznd_giftcard_exclude_categoties[]" style="width: 60%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'No categories', 'wooznd-smartpack' ); ?>">
                    <?php
                    $category_ids = ( array ) get_post_meta( $post->ID, '_wznd_giftcard_exclude_categoties', true );
                    $categories = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );

                    if ( $categories ) {
                        foreach ( $categories as $cat ) {
                            echo '<option value="' . esc_attr( $cat->term_id ) . '"' . selected( in_array( $cat->term_id, $category_ids ), true, false ) . '>' . esc_html( $cat->name ) . '</option>';
                        }
                    }
                    ?>
                </select> <?php echo wc_help_tip( esc_html__( 'Product must not be in this category for the gift card to remain valid or, for "Product Discounts", products in these categories will not be discounted.', 'wooznd-smartpack' ) ); ?>
            </p>
        </div>
        <div class='options_group'>
            <?php
            woocommerce_wp_checkbox(
                    array(
                        'id' => '_wznd_giftcard_allow_send_date',
                        'label' => esc_html__( 'Show Schedule Date', 'wooznd-smartpack' ),
                        'description' => esc_html__( 'Allow Buyers to specify when to send their gift cards.', 'wooznd-smartpack' )
            ) );
            woocommerce_wp_text_input( array(
                'id' => '_wznd_giftcard_coupon_pattern',
                'label' => esc_html__( 'Gift Card Code Pattern', 'wooznd-smartpack' ),
                'style' => 'width:60%',
                'data_type' => 'text',
                'desc_tip' => true,
                'description' => esc_html__( 'Code pattern to use when creating new gift card coupon, (e.g [A10], [Aa10], [a10], [C10], [Cc10], [c10], [N10])', 'wooznd-smartpack' ),
                'placeholder' => 'Use Default'
            ) );
            ?>
        </div>
        <div class='options_group'>
            <?php
            woocommerce_wp_select(
                    array(
                        'id' => '_wznd_giftcard_email_template',
                        'label' => esc_html__( 'Gift Card Email Template', 'wooznd-smartpack' ),
                        'class' => 'wc-enhanced-select',
                        'style' => 'width:60%',
                        'options' => WooZndUtil::GetPostTypeOption( 'wznd_giftcard', 'publish', array( '' => esc_html__( 'Default', 'wooznd-smartpack' ) ) ),
                        'desc_tip' => true,
                        'description' => esc_html__( 'Gift card template to use when sending mail.', 'wooznd-smartpack' )
                    )
            );

            woocommerce_wp_select(
                    array(
                        'id' => '_wznd_giftcard_template',
                        'label' => esc_html__( 'Gift Card PDF Template', 'wooznd-smartpack' ),
                        'class' => 'wc-enhanced-select',
                        'style' => 'width:60%',
                        'options' => WooZndUtil::GetPostTypeOption( 'wznd_giftcard' ),
                        'desc_tip' => true,
                        'description' => esc_html__( 'Gift card template to use when sending mail.', 'wooznd-smartpack' )
                    )
            );
            ?>
        </div>
        <div class='options_group'><?php
            woocommerce_wp_select(
                    array(
                        'id' => '_wznd_giftcard_delivery',
                        'label' => esc_html__( 'Delivery Method', 'wooznd-smartpack' ),
                        'class' => 'wc-enhanced-select',
                        'style' => 'width:60%',
                        'options' => [
                            '' => esc_html__( 'Select', 'wooznd-smartpack' ),
                            WOOZND_GIFTCARD_DELIVERY_OFFLINE => esc_html__( 'Print & Send', 'wooznd-smartpack' ),
                            WOOZND_GIFTCARD_DELIVERY_SHIP => esc_html__( 'Shipping', 'wooznd-smartpack' ),
                            WOOZND_GIFTCARD_DELIVERY_EMAIL => esc_html__( 'Email Address', 'wooznd-smartpack' )
                        ],
                        'desc_tip' => true,
                        'description' => esc_html__( 'Delivery method to use after creating a gift card.', 'wooznd-smartpack' )
                    )
            );
            woocommerce_wp_checkbox( array(
                'id' => '_wznd_giftcard_show_sender_name',
                'label' => esc_html__( 'Buyer name field', 'wooznd-smartpack' ),
                'desc_tip' => true,
                'description' => esc_html__( 'Allows buyers to specify their name', 'wooznd-smartpack' ),
            ) );
            woocommerce_wp_checkbox( array(
                'id' => '_wznd_giftcard_show_sender_email',
                'label' => esc_html__( 'Buyer email field', 'wooznd-smartpack' ),
                'desc_tip' => true,
                'description' => esc_html__( 'Allows buyers to specify their email', 'wooznd-smartpack' ),
            ) );

            woocommerce_wp_checkbox( array(
                'id' => '_wznd_giftcard_show_receiver_name',
                'label' => esc_html__( 'Receiver name field', 'wooznd-smartpack' ),
                'desc_tip' => true,
                'default' => 'yes',
                'description' => esc_html__( "Allows buyers to specify receiver's email", "woocommerce" ),
            ) );

            woocommerce_wp_checkbox( array(
                'id' => '_wznd_giftcard_show_receiver_email',
                'label' => esc_html__( 'Receiver email field', 'wooznd-smartpack' ),
                'desc_tip' => true,
                'description' => esc_html__( "Allows buyers to specify receiver's email", "woocommerce" ),
            ) );
            woocommerce_wp_checkbox( array(
                'id' => '_wznd_giftcard_show_message',
                'label' => esc_html__( 'Message field', 'wooznd-smartpack' ),
                'desc_tip' => true,
                'description' => esc_html__( "Allows buyers to add gift card message", "woocommerce" ),
            ) );
            ?></div>
    </div><?php
}

add_action( 'woocommerce_process_product_meta', 'wooznd_save_giftcard_option_fields' );

function wooznd_save_giftcard_option_fields( $post_id ) {

    $is_valid_nonce = ( isset( $_POST[ 'wznd_giftcard_nonce' ] ) && wp_verify_nonce( $_POST[ 'wznd_giftcard_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
    if ( !$is_valid_nonce ) {
        return;
    }
    $enable_gift_card = isset( $_POST[ '_wznd_enable_giftcard' ] ) ? 'yes' : 'no';
    update_post_meta( $post_id, '_wznd_enable_giftcard', $enable_gift_card );




    if ( isset( $_POST[ '_wznd_giftcard_discount_type' ] ) ) {
        update_post_meta( $post_id, '_wznd_giftcard_discount_type', $_POST[ '_wznd_giftcard_discount_type' ] );
    } else {
        delete_post_meta( $post_id, '_wznd_giftcard_discount_type' );
    }

    if ( isset( $_POST[ '_wznd_giftcard_price_type' ] ) ) {
        update_post_meta( $post_id, '_wznd_giftcard_price_type', $_POST[ '_wznd_giftcard_price_type' ] );
    } else {
        delete_post_meta( $post_id, '_wznd_giftcard_price_type' );
    }

    $wznd_giftcard_from_price = isset( $_POST[ '_wznd_giftcard_from_price' ] ) ? abs( $_POST[ '_wznd_giftcard_from_price' ] ) : 0;
    update_post_meta( $post_id, '_wznd_giftcard_from_price', $wznd_giftcard_from_price );

    $wznd_giftcard_to_price = isset( $_POST[ '_wznd_giftcard_to_price' ] ) ? abs( $_POST[ '_wznd_giftcard_to_price' ] ) : 9999999999;
    update_post_meta( $post_id, '_wznd_giftcard_to_price', $wznd_giftcard_to_price );

    $wznd_giftcard_select_price = isset( $_POST[ '_wznd_giftcard_select_price' ] ) ? $_POST[ '_wznd_giftcard_select_price' ] : 0;
    update_post_meta( $post_id, '_wznd_giftcard_select_price', $wznd_giftcard_select_price );

    $wznd_giftcard_expiry_days = isset( $_POST[ '_wznd_giftcard_expiry_days' ] ) ? absint( $_POST[ '_wznd_giftcard_expiry_days' ] ) : 7;
    $wznd_giftcard_expiry_days = ($wznd_giftcard_expiry_days == 0 || $wznd_giftcard_expiry_days == '') ? 7 : $wznd_giftcard_expiry_days;
    update_post_meta( $post_id, '_wznd_giftcard_expiry_days', $wznd_giftcard_expiry_days );

    if ( isset( $_POST[ '_wznd_giftcard_product_ids' ] ) ) {

        update_post_meta( $post_id, '_wznd_giftcard_product_ids', $_POST[ '_wznd_giftcard_product_ids' ] );
    } else {
        delete_post_meta( $post_id, '_wznd_giftcard_product_ids' );
    }


    if ( isset( $_POST[ '_wznd_giftcard_exclude_product_ids' ] ) ) {
        update_post_meta( $post_id, '_wznd_giftcard_exclude_product_ids', $_POST[ '_wznd_giftcard_exclude_product_ids' ] );
    } else {
        delete_post_meta( $post_id, '_wznd_giftcard_exclude_product_ids' );
    }

    if ( isset( $_POST[ '_wznd_giftcard_product_categoties' ] ) ) {
        update_post_meta( $post_id, '_wznd_giftcard_product_categoties', $_POST[ '_wznd_giftcard_product_categoties' ] );
    } else {
        delete_post_meta( $post_id, '_wznd_giftcard_product_categoties' );
    }

    if ( isset( $_POST[ '_wznd_giftcard_exclude_categoties' ] ) ) {
        update_post_meta( $post_id, '_wznd_giftcard_exclude_categoties', $_POST[ '_wznd_giftcard_exclude_categoties' ] );
    } else {
        delete_post_meta( $post_id, '_wznd_giftcard_exclude_categoties' );
    }

    $show_gift_card_date = isset( $_POST[ '_wznd_giftcard_allow_send_date' ] ) ? 'yes' : 'no';
    update_post_meta( $post_id, '_wznd_giftcard_allow_send_date', $show_gift_card_date );

    if ( isset( $_POST[ '_wznd_giftcard_template' ] ) ) {
        update_post_meta( $post_id, '_wznd_giftcard_template', $_POST[ '_wznd_giftcard_template' ] );
    } else {
        delete_post_meta( $post_id, '_wznd_giftcard_template' );
    }

    if ( isset( $_POST[ '_wznd_giftcard_email_template' ] ) ) {
        update_post_meta( $post_id, '_wznd_giftcard_email_template', $_POST[ '_wznd_giftcard_email_template' ] );
    } else {
        delete_post_meta( $post_id, '_wznd_giftcard_email_template' );
    }

    if ( isset( $_POST[ '_wznd_giftcard_coupon_pattern' ] ) ) {
        update_post_meta( $post_id, '_wznd_giftcard_coupon_pattern', $_POST[ '_wznd_giftcard_coupon_pattern' ] );
    } else {
        delete_post_meta( $post_id, '_wznd_giftcard_coupon_pattern' );
    }


    if ( isset( $_POST[ '_wznd_giftcard_delivery' ] ) ) {
        update_post_meta( $post_id, '_wznd_giftcard_delivery', $_POST[ '_wznd_giftcard_delivery' ] );
    } else {
        delete_post_meta( $post_id, '_wznd_giftcard_delivery' );
    }

    $giftcard_show_sender_name = isset( $_POST[ '_wznd_giftcard_show_sender_name' ] ) ? 'yes' : 'no';
    update_post_meta( $post_id, '_wznd_giftcard_show_sender_name', $giftcard_show_sender_name );

    $giftcard_show_sender_email = isset( $_POST[ '_wznd_giftcard_show_sender_email' ] ) ? 'yes' : 'no';
    update_post_meta( $post_id, '_wznd_giftcard_show_sender_email', $giftcard_show_sender_email );

    $giftcard_show_receiver_name = isset( $_POST[ '_wznd_giftcard_show_receiver_name' ] ) ? 'yes' : 'no';
    update_post_meta( $post_id, '_wznd_giftcard_show_receiver_name', $giftcard_show_receiver_name );

    $giftcard_show_receiver_email = isset( $_POST[ '_wznd_giftcard_show_receiver_email' ] ) ? 'yes' : 'no';
    update_post_meta( $post_id, '_wznd_giftcard_show_receiver_email', $giftcard_show_receiver_email );

    $giftcard_show_message = isset( $_POST[ '_wznd_giftcard_show_message' ] ) ? 'yes' : 'no';
    update_post_meta( $post_id, '_wznd_giftcard_show_message', $giftcard_show_message );
}
