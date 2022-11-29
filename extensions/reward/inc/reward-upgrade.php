<?php

add_action('woocommerce_init', 'wooznd_reward_upgrade_database', 1);

function wooznd_reward_upgrade_database() {

    $rewards_version = WooZndUtil::GetOption('ws_reward_db', 99);
    //Add create Database here.
//    WooZndUtil::UpdateOption('ws_reward_db', 99);
    if ($rewards_version < 100) {

//        wooznd_reward_upgrade_database100();
    }
}

function wooznd_reward_upgrade_database100() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE {$wpdb->prefix}woo_reward_accounts ("
            . "id bigint(20) UNSIGNED NOT NULL,"
            . "balance varchar(256) NOT NULL,"
            . "ledger varchar(256) NOT NULL,"
            . "locked tinyint(4) UNSIGNED NOT NULL,"
            . "PRIMARY KEY (id)"
            . ") $charset_collate;";

    WooZndUtil::CreateTable($sql);

    $sql_t = "CREATE TABLE {$wpdb->prefix}woo_reward_transactions ("
            . "id bigint(20) UNSIGNED NOT NULL,"
            . "account_id bigint(20) UNSIGNED NOT NULL,"
            . "PRIMARY KEY (id)"
            . ") $charset_collate;";
    WooZndUtil::CreateTable($sql_t);

    WooZndUtil::UpdateOption('ws_reward_db', 100);
}
