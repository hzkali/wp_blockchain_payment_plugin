<?php

global $wp_query;
$is_endpoint = isset($wp_query->query_vars['wznd-my-wallet']);

$p_url = '';
if ($is_endpoint && !is_admin() && is_main_query() && in_the_loop() && is_account_page()) {
    //$p_url = get_permalink(get_the_ID()).'';
    $p_url = wc_get_endpoint_url('wznd-my-wallet');
} else {
    $p_url = get_permalink(get_the_ID());
}

$url_format = $p_url . '?pg={{page}}';
$default_url = $p_url;

$account_number = WooZndWalletAccountDB::GetAccountNumberById(get_current_user_id());

$totals = WooZndWalletTransactionDB::GetTransactionsCount(-1, $account_number, '', '');
$pagesize = isset($page_size) ? $page_size : 5;

$pg = isset($_GET['pg']) ? $_GET['pg'] : 1;
$paging = new WooZndPaginator($totals, $pagesize, $pg);



$rows = WooZndWalletTransactionDB::LoadTransactions($account_number, -1, '', '', $paging->offset(), $paging->limit());


