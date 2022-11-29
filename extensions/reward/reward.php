<?php

require_once dirname(__FILE__) . '/inc/reward_settings.php';
if (WooZndUtil::GetOption('enable_purchase_reward', 'yes') == 'yes') {
    include_once('inc/reward-activate.php');
    require_once dirname(__FILE__) . '/inc/class-reward.php';
    require_once dirname(__FILE__) . '/inc/reward-product-meta.php';
    add_action('woocommerce_init', 'WooZndReward::Init');
}




