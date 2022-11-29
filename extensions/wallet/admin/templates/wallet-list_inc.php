<?php

$url_options = array();
if (isset($_GET['status'])) {
    $url_options['status'] = sanitize_text_field($_GET['status']);
}
if (!empty($_GET['search'])) {
    $url_options['search'] = sanitize_text_field($_GET['search']);
}
if (!empty($_GET['orderby'])) {
    $url_options['orderby'] = sanitize_text_field($_GET['orderby']);
}
if (!empty($_GET['order'])) {
    $url_options['order'] = sanitize_text_field($_GET['order']);
}


$url_format = admin_url('admin.php?page=wznd-wallet&pg={{page}}');

$default_url = admin_url('admin.php?page=wznd-wallet');

foreach ($url_options as $key => $value) {
    $url_format.=('&' . $key . '={{' . $key . '}}');
    $default_url.=('&' . $key . '={{' . $key . '}}');
}
$status = WOOZND_WALLET_ACCOUNT_STATUS_NONE;
if (isset($_GET['status'])) {
    $status = sanitize_text_field($_GET['status']);
}

$search = !empty($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
$orderby = !empty($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : '';
$order = !empty($_GET['order']) ? sanitize_text_field($_GET['order']) : '';

$totals = WooZndWalletAccountDB::GetAccountsCount($search . '%', $status);
$pagesize = 25;

$pg = isset($_GET['pg']) ? $_GET['pg'] : 1;
$paging = new WooZndPaginator($totals, $pagesize, $pg);


$rows = WooZndWalletAccountDB::LoadAccounts($search . '%', $status, $paging->offset(), $paging->limit(), $orderby, $order);
