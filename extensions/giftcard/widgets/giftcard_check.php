<?php

class WooZndGiftCardChecker_Widget extends WP_Widget {

    function __construct() {
        parent::__construct(
                'wooznd_giftcard', esc_html__('Gift Card Checker', 'wooznd-smartpack'), array(
            'description' => esc_html__('Allows users to check their gift card balance', 'wooznd-smartpack')
                )
        );
    }

    function form($instance) {

        $title = (isset($instance['title'])) ? $instance['title'] : '';
        $button_text = (isset($instance['button_text'])) ? $instance['button_text'] : '';
        $placeholder = (isset($instance['placeholder'])) ? $instance['placeholder'] : '';


        $amount_label = (isset($instance['amount_label'])) ? $instance['amount_label'] : '';
        $balance_label = (isset($instance['balance_label'])) ? $instance['balance_label'] : '';
        $sent_to_label = (isset($instance['sent_to_label'])) ? $instance['sent_to_label'] : '';
        $expiry_date_label = (isset($instance['expiry_date_label'])) ? $instance['expiry_date_label'] : '';
        ?>

        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo esc_html__('Title', 'wooznd-smartpack'); ?></label>
            <input class="widefat" type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($title); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('button_text'); ?>"><?php echo esc_html__('Button Text', 'wooznd-smartpack'); ?></label>
            <input class="widefat" type="text" id="<?php echo $this->get_field_id('button_text'); ?>" name="<?php echo $this->get_field_name('button_text'); ?>" value="<?php echo esc_attr($button_text); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('placeholder'); ?>"><?php echo esc_html__('Placeholder', 'wooznd-smartpack'); ?></label>
            <input class="widefat" type="text" id="<?php echo $this->get_field_id('placeholder'); ?>" name="<?php echo $this->get_field_name('placeholder'); ?>" value="<?php echo esc_attr($placeholder); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('amount_label'); ?>"><?php echo esc_html__('Amount Label', 'wooznd-smartpack'); ?></label>
            <input class="widefat" type="text" id="<?php echo $this->get_field_id('amount_label'); ?>" name="<?php echo $this->get_field_name('amount_label'); ?>" value="<?php echo esc_attr($amount_label); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('balance_label'); ?>"><?php echo esc_html__('Balance Label', 'wooznd-smartpack'); ?></label>
            <input class="widefat" type="text" id="<?php echo $this->get_field_id('balance_label'); ?>" name="<?php echo $this->get_field_name('balance_label'); ?>" value="<?php echo esc_attr($balance_label); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('sent_to_label'); ?>"><?php echo esc_html__('Sent To Label', 'wooznd-smartpack'); ?></label>
            <input class="widefat" type="text" id="<?php echo $this->get_field_id('sent_to_label'); ?>" name="<?php echo $this->get_field_name('sent_to_label'); ?>" value="<?php echo esc_attr($sent_to_label); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('expiry_date_label'); ?>"><?php echo esc_html__('Expiry Date Label', 'wooznd-smartpack'); ?></label>
            <input class="widefat" type="text" id="<?php echo $this->get_field_id('expiry_date_label'); ?>" name="<?php echo $this->get_field_name('expiry_date_label'); ?>" value="<?php echo esc_attr($expiry_date_label); ?>">
        </p>

        <?php
    }

    function update($new_instance, $old_instance) {

        $title = (isset($new_instance['title'])) ? $new_instance['title'] : '';
        $button_text = (isset($new_instance['button_text'])) ? $new_instance['button_text'] : '';
        $placeholder = (isset($new_instance['placeholder'])) ? $new_instance['placeholder'] : '';

        $amount_label = (isset($new_instance['amount_label'])) ? $new_instance['amount_label'] : 'dddddd';
        $balance_label = (isset($new_instance['balance_label'])) ? $new_instance['balance_label'] : '';
        $sent_to_label = (isset($new_instance['sent_to_label'])) ? $new_instance['sent_to_label'] : '';
        $expiry_date_label = (isset($new_instance['expiry_date_label'])) ? $new_instance['expiry_date_label'] : '';



        $instance = $old_instance;
        $instance['title'] = strip_tags($title);
        $instance['button_text'] = strip_tags($button_text);
        $instance['placeholder'] = strip_tags($placeholder);

        $instance['amount_label'] = strip_tags($amount_label);
        $instance['balance_label'] = strip_tags($balance_label);
        $instance['sent_to_label'] = strip_tags($sent_to_label);
        $instance['expiry_date_label'] = strip_tags($expiry_date_label);
        return $instance;
    }

    function widget($args, $instance) {
        $title = isset($instance['title']) ? $instance['title'] : 'Gift Card Checker';
        $button_text = (isset($instance['button_text'])) ? $instance['button_text'] : 'Check';
        $placeholder = (isset($instance['placeholder'])) ? $instance['placeholder'] : 'Enter code';

        $amount_label = (isset($instance['amount_label'])) ? $instance['amount_label'] : 'Amount:';
        $balance_label = (isset($instance['balance_label'])) ? $instance['balance_label'] : 'Remaining balance:';
        $sent_to_label = (isset($instance['sent_to_label'])) ? $instance['sent_to_label'] : 'Sent to:';
        $expiry_date_label = (isset($instance['expiry_date_label'])) ? $instance['expiry_date_label'] : 'Expiry date:';


        extract($args);
        echo $before_widget;
        echo $before_title . (!empty($title) ? $title : 'Gift Card Checker') . $after_title;
        echo do_shortcode('[wznd_giftcard_check inline="true" placeholder="' . $placeholder . '" button_text="' . $button_text . '" amount_label="' . $amount_label . '" balance_label="' . $balance_label . '" sent_to_label="' . $sent_to_label . '" expiry_date_label="' . $expiry_date_label . '"]');
        echo $after_widget;
    }

}
