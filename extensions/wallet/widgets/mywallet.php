<?php

class WooZndMyWallet_Widget extends WP_Widget {

    function __construct() {
        parent::__construct(
                'wooznd_mywallet', esc_html__('My Wallet', 'wooznd-smartpack'), array(
            'description' => esc_html__('Displays users wallet informations', 'wooznd-smartpack')
                )
        );
    }

    function form($instance) {

        $title = (isset($instance['title'])) ? $instance['title'] : '';
        $ledger_text = (isset($instance['ledger_text'])) ? $instance['ledger_text'] : '';
        $current_text = (isset($instance['current_text'])) ? $instance['current_text'] : '';
        $total_spent_text = (isset($instance['total_spent_text'])) ? $instance['total_spent_text'] : '';
        ?>

        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo esc_html__('Title', 'wooznd-smartpack'); ?></label>
            <input class="widefat" type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($title); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('ledger_text'); ?>"><?php echo esc_html__('Ledger Text', 'wooznd-smartpack'); ?></label>
            <input class="widefat" type="text" id="<?php echo $this->get_field_id('ledger_text'); ?>" name="<?php echo $this->get_field_name('ledger_text'); ?>" value="<?php echo esc_attr($ledger_text); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('current_text'); ?>"><?php echo esc_html__('Current Text', 'wooznd-smartpack'); ?></label>
            <input class="widefat" type="text" id="<?php echo $this->get_field_id('current_text'); ?>" name="<?php echo $this->get_field_name('current_text'); ?>" value="<?php echo esc_attr($current_text); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('total_spent_text'); ?>"><?php echo esc_html__('Total Spent Text', 'wooznd-smartpack'); ?></label>
            <input class="widefat" type="text" id="<?php echo $this->get_field_id('total_spent_text'); ?>" name="<?php echo $this->get_field_name('total_spent_text'); ?>" value="<?php echo esc_attr($total_spent_text); ?>">
        </p>

        <?php
    }

    function update($new_instance, $old_instance) {

        $title = (isset($new_instance['title'])) ? $new_instance['title'] : '';
        $ledger_text = (isset($new_instance['ledger_text'])) ? $new_instance['ledger_text'] : '';
        $current_text = (isset($new_instance['current_text'])) ? $new_instance['current_text'] : '';
        $total_spent_text = (isset($new_instance['total_spent_text'])) ? $new_instance['total_spent_text'] : '';

        $instance = $old_instance;
        $instance['title'] = strip_tags($title);
        $instance['ledger_text'] = strip_tags($ledger_text);
        $instance['current_text'] = strip_tags($current_text);
        $instance['total_spent_text'] = strip_tags($total_spent_text);
        return $instance;
    }

    function widget($args, $instance) {
        $title = isset($instance['title']) ? $instance['title'] : '';
        $ledger_text = (isset($instance['ledger_text'])) ? $instance['ledger_text'] : '';
        $current_text = (isset($instance['current_text'])) ? $instance['current_text'] : '';
        $total_spent_text = (isset($instance['total_spent_text'])) ? $instance['total_spent_text'] : '';



        extract($args);
        echo $before_widget;
        echo $before_title . (!empty($title) ? $title : 'My Wallet') . $after_title;
        echo do_shortcode('[wznd_mywallet ledger_text="' . $ledger_text . '" current_text="' . (!empty($current_text) ? $current_text : 'Balance:') . '" total_spent_text="' . $total_spent_text . '"]');
        echo $after_widget;
    }

}
