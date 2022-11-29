<?php
$url_options = array();
if (isset($_GET['status'])) {
    $url_options['status'] = $_GET['status'];
}
if (isset($_GET['search'])) {
    $url_options['search'] = $_GET['search'];
}
if (isset($_GET['from'])) {
    $url_options['from'] = $_GET['from'];
}
if (isset($_GET['to'])) {
    $url_options['to'] = $_GET['to'];
}




$url_format = admin_url('admin.php?page=wznd-wallet-trans&pg={{page}}');

$default_url = admin_url('admin.php?page=wznd-wallet-trans');

foreach ($url_options as $key => $value) {
    $url_format.=('&' . $key . '={{' . $key . '}}');
    $default_url.=('&' . $key . '={{' . $key . '}}');
}
$status = -1;
if (!empty($_GET['status'])) {
    $status = $_GET['status'];
}
$search = !empty($_GET['search']) ? $_GET['search'] : '';
$from = !empty($_GET['from']) ? $_GET['from'] : '';
$to = !empty($_GET['to']) ? $_GET['to'] : '';

$totals = $status < 0 ? WooZndWalletTransactionDB::GetTransactionsCount(-1, $search, $from, $to) : WooZndWalletTransactionDB::GetTransactionsCount($status, $search, $from, $to);
$pagesize = 25;

$pg = isset($_GET['pg']) ? $_GET['pg'] : 1;
$paging = new WooZndPaginator($totals, $pagesize, $pg);

$rows = WooZndWalletTransactionDB::LoadTransactions($search, $status, $from, $to, $paging->offset(), $paging->limit());
