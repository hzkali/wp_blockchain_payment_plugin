<?php
include_once('giftcard_check.php');

function wooznd_register_giftcard_widgets() {
    register_widget('WooZndGiftCardChecker_Widget');
}
add_action('widgets_init', 'wooznd_register_giftcard_widgets');


