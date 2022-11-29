<?php



$url_options = array();
if (isset($_GET['status'])) {
    $url_options['status'] = $_GET['status'];
}
if (isset($_GET['search'])) {
    $url_options['search'] = $_GET['search'];
}



$url_format = admin_url('admin.php?page=wznd-wallet-refunds&pg={{page}}');

$default_url = admin_url('admin.php?page=wznd-wallet-refunds');

foreach ($url_options as $key => $value) {
    $url_format.=('&' . $key . '={{' . $key . '}}');
    $default_url.=('&' . $key . '={{' . $key . '}}');
}
$status = -1;
if (!empty($_GET['status'])) {
    $status = $_GET['status'];
}
$search = !empty($_GET['search']) ? $_GET['search'] : '';

$totals = WooZndRefundDB::GetRequestsCount(($search != '') ? $search . '%' : '', $status);
$pagesize = 25;

$pg = isset($_GET['pg']) ? $_GET['pg'] : 1;
$paging = new WooZndPaginator($totals, $pagesize, $pg);


$rows = WooZndRefundDB::LoadRequests(($search != '') ? $search . '%' : '', $status, $paging->offset(), $paging->limit());

