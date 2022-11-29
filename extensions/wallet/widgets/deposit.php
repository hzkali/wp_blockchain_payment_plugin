<?php

class WooZndDeposit_Widget extends WP_Widget {

    function __construct() {
        parent::__construct(
                'wooznd_deposit', esc_html__('Wallet Deposit', 'wooznd-smartpack'), array(
            'description' => esc_html__('Allows users to deposit funds into their wallet', 'wooznd-smartpack')
                )
        );
    }

    function form($instance) {

        $title = (isset($instance['title'])) ? $instance['title'] : '';
        $button_text = (isset($instance['button_text'])) ? $instance['button_text'] : '';
        $placeholder = (isset($instance['placeholder'])) ? $instance['placeholder'] : '';
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

        <?php
    }

    function update($new_instance, $old_instance) {

        $title = (isset($new_instance['title'])) ? $new_instance['title'] : '';
        $button_text = (isset($new_instance['button_text'])) ? $new_instance['button_text'] : '';
        $placeholder = (isset($new_instance['placeholder'])) ? $new_instance['placeholder'] : '';

        $instance = $old_instance;
        $instance['title'] = strip_tags($title);
        $instance['button_text'] = strip_tags($button_text);
        $instance['placeholder'] = strip_tags($placeholder);
        return $instance;
    }

    function widget($args, $instance) {
        $title = isset($instance['title']) ? $instance['title'] : 'Wallet Deposit';
        $button_text = (isset($instance['button_text'])) ? $instance['button_text'] : 'Deposit';
        $placeholder = (isset($instance['placeholder'])) ? $instance['placeholder'] : 'Enter Amount';



        extract($args);
        echo $before_widget;
        echo $before_title . (!empty($title) ? $title : 'My Wallet') . $after_title;
        echo do_shortcode('[wznd_deposit placeholder="' . $placeholder . '" button_text="' . $button_text . '"]');
        echo $after_widget;
    }

}
