<?php
include_once('mywallet.php');
include_once('deposit.php');

function wooznd_register_wallet_widgets() {
    register_widget('WooZndMyWallet_Widget');
    register_widget('WooZndDeposit_Widget');
}
add_action('widgets_init', 'wooznd_register_wallet_widgets');


