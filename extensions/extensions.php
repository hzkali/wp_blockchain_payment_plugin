<?php

//Wallet Pro
if (!class_exists('WooZendWallet')) {

    include_once ('wallet/wallet.php');
}
//Refund Pro
if (!class_exists('WooZndRefund')) {

    include_once ('refund/refund.php');
}

//Reward Pro
if (!class_exists('WooZndReward')) {
    
    include_once ('reward/reward.php');
}

//Gift cards Pro
if (!class_exists('WooZndGiftCard')) {
   
    include_once ('giftcard/giftcard.php');
}


if (!function_exists('wooznd_plugin_activate')) {

    function wooznd_plugin_activate() {
        
        if (function_exists('wooznd_wallet_activate')) {
            wooznd_wallet_activate();
        }
        
        if (function_exists('wooznd_refund_activate')) {
            
            wooznd_refund_activate();
        }
        
        if (function_exists('wooznd_reward_activate')) {
            wooznd_reward_activate();
        }
        
        if (function_exists('wooznd_giftcard_activate')) {
            wooznd_giftcard_activate();
        }
    }

}