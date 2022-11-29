<?php
add_filter('woocommerce_product_data_tabs', 'wooznd_reward_product_tabs');

function wooznd_reward_product_tabs($tabs) {
    if (WooZndUtil::GetOption('enable_purchase_reward', 'yes') == 'no') {
        return $tabs;
    }
    $tabs['wznd_reward'] = array(
        'label' => esc_html__('Purchase Reward', 'wooznd-smartpack'),
        'target' => 'wznd_reward_options',
        'class' => array('show_if_simple', 'show_if_variable'),
    );
    return $tabs;
}

add_action('woocommerce_product_data_panels', 'wooznd_reward_options_product_tab_content', 99);

function wooznd_reward_options_product_tab_content() {
    global $post;
    if (WooZndUtil::GetOption('enable_purchase_reward', 'yes') == 'no') {
        return;
    }
    ?><div id='wznd_reward_options' class='panel woocommerce_options_panel'><div class="options_group show_if_simple show_if_variable"><?php
            wp_nonce_field(basename(__FILE__), 'wznd_reward_nonce');
            global $woocommerce, $post;

            woocommerce_wp_checkbox(array(
                'id' => '_wznd_enable_reward',
                'label' => esc_html__('Enable Purchase Reward', 'wooznd-smartpack'),
                'desc_tip' => true,
                'description' => esc_html__('Allow customers wallet to be rewarded with credit.', 'wooznd-smartpack'),
            ));

            woocommerce_wp_select(
                    array(
                        'id' => '_wznd_reward_credit_type',
                        'label' => esc_html__('Amount Type', 'wooznd-smartpack'),
                        'class' => 'wc-enhanced-select',
                        'style' => 'width:80%',
                        'options' => array(
                            'fixed' => esc_html__('Fixed Value', 'wooznd-smartpack'),
                            'fixed-unit' => esc_html__('Fixed Value (per unit)', 'wooznd-smartpack'),
                            'percent' => esc_html__('Percentage %', 'wooznd-smartpack'),
                            'percent-unit' => esc_html__('Percentage % ( per unit)', 'wooznd-smartpack')
                        ),
                        'desc_tip' => true,
                        'description' => esc_html__('Controls the reward amount type.', 'wooznd-smartpack')
                    )
            );

            woocommerce_wp_text_input(
                    array(
                        'id' => '_wznd_reward_credit_amount',
                        'label' => esc_html__('Reward Amount', 'wooznd-smartpack'),
                        'type' => 'number',
                        'placeholder' => esc_html__('Enter amount', 'wooznd-smartpack'),
                        'desc_tip' => 'true',
                        'default' => '0',
                        'custom_attributes' => array(
                            'min' => '0',
                            'step' => '0.05'
                        ),
                        'description' => esc_html__('Purchase reward amount for this product.', 'wooznd-smartpack')
                    )
            );

            woocommerce_wp_text_input(
                    array(
                        'id' => '_wznd_reward_credit_remark',
                        'label' => esc_html__('Reward Remark', 'wooznd-smartpack'),
                        'placeholder' => esc_html__('Purchase Reward', 'wooznd-smartpack'),
                        'desc_tip' => 'true',
                        'description' => esc_html__('Reward Transaction remark.', 'wooznd-smartpack')
                    )
            );

            woocommerce_wp_text_input(
                    array(
                        'id' => '_wznd_reward_credit_info',
                        'label' => esc_html__('Purchase Reward Info', 'wooznd-smartpack'),
                        'placeholder' => esc_html__('Enter Info', 'wooznd-smartpack'),
                        'desc_tip' => 'true',
                        'description' => esc_html__('Information about the purchase reward', 'wooznd-smartpack')
                    )
            );
            ?>
        </div>
    </div><?php
}

add_action('woocommerce_process_product_meta', 'wooznd_save_reward_option_fields');

function wooznd_save_reward_option_fields($post_id) {
    if (WooZndUtil::GetOption('enable_purchase_reward', true) == false) {
        return;
    }

    $is_valid_nonce = ( isset($_POST['wznd_reward_nonce']) && wp_verify_nonce($_POST['wznd_reward_nonce'], basename(__FILE__)) ) ? true : false;
    if (!$is_valid_nonce) {
        return;
    }

    // Enable Customer Reward
    $credit_enable = isset($_POST['_wznd_enable_reward']) ? 'yes' : 'no';
    update_post_meta($post_id, '_wznd_enable_reward', $credit_enable);

    // Reward Amount Type
    $amount_type_field = sanitize_text_field($_POST['_wznd_reward_credit_type']);
    update_post_meta($post_id, '_wznd_reward_credit_type', $amount_type_field);

    // Reward Amount
    $amount_field = wc_format_decimal($_POST['_wznd_reward_credit_amount']);
    if (!empty($amount_field)) {
        update_post_meta($post_id, '_wznd_reward_credit_amount', $amount_field);
    } else {
        delete_post_meta($post_id, '_wznd_reward_credit_amount');
    }
    // Reward Remark
    $remark_field = sanitize_text_field($_POST['_wznd_reward_credit_remark']);
    if (!empty($remark_field)) {
        update_post_meta($post_id, '_wznd_reward_credit_remark', $remark_field);
    } else {
        delete_post_meta($post_id, '_wznd_reward_credit_remark');
    }

    // Reward Info
    $remark_info_field = sanitize_text_field($_POST['_wznd_reward_credit_info']);
    if (!empty($remark_info_field)) {
        update_post_meta($post_id, '_wznd_reward_credit_info', $remark_info_field);
    } else {
        delete_post_meta($post_id, '_wznd_reward_credit_info');
    }
}
