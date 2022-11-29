<?php require 'transactions_inc.php'; ?>
<h3><?php echo $transaction_title; ?></h3>
<table class="woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
    <thead>
        <tr>
            <th><span class="nobr"><?php echo esc_html__('Receipt #', 'wooznd-smartpack'); ?></span></th>
            <th><span class="nobr"><?php echo esc_html__('Remark', 'wooznd-smartpack'); ?></span></th>
            <th><span class="nobr"><?php echo esc_html__('Status', 'wooznd-smartpack'); ?></span></th>
            <th><span class="nobr"><?php echo esc_html__('Credit', 'wooznd-smartpack'); ?></span></th>
            <th><span class="nobr"><?php echo esc_html__('Debit', 'wooznd-smartpack'); ?></span></th>
            <th style="text-align: right;"><?php echo esc_html__('Date', 'wooznd-smartpack'); ?></th>
        </tr>
    </thead>

    <tbody>
        <?php
        foreach ($rows as $row) {
            $trans_remark = isset($row['remark']) ? $row['remark'] : '';
            if (isset($row['order_id'])) {
                $order = wc_get_order($row['order_id']);
                $trans_remark = preg_replace('/{{order_id}}/', $row['order_id'], $trans_remark);
                $trans_remark = preg_replace('/{{order_url}}/', $order->get_view_order_url(), $trans_remark);
                $trans_remark .= ' <a href="' . $order->get_view_order_url() . '" target="_blank">' . esc_html__('View Order', 'wooznd-smartpack') . '</a>';
            }
            ?>
            <tr class="order">
                <td>                
                    <?php echo esc_html($row['receipt'] ? $row['receipt'] : ''); ?>
                </td>
                <td>
                    <?php echo wp_kses_post($trans_remark); ?>
                </td>
                <td>
                    <?php echo WalletUtil::TransactionStatusString($row['status'], false); ?>
                </td>
                <td>
                    <?php echo ($row['credit'] > 0) ? wc_price($row['credit']) : '-'; ?>
                </td>
                <td>
                    <?php echo ($row['debit'] > 0) ? wc_price($row['debit']) : '-'; ?>
                </td>
                <td style="text-align: right;">
                    <time datetime="<?php echo WooZndUtil::MySQLTimeStampToDataTime($row['issue_date'], 'Y-m-d'); ?>" title="<?php echo WooZndUtil::MySQLTimeStampToDataTime($row['issue_date'], 'U'); ?>"><?php echo WooZndUtil::MySQLTimeStampToDataTime($row['issue_date'], get_option('date_format') . ' ' . get_option('time_format')); ?></time>
                </td>
            </tr>
            <?php
        }
        ?>
    </tbody>
    <tfoot>
        <tr>
            <th><span class="nobr"><?php echo esc_html__('Receipt #', 'wooznd-smartpack'); ?></span></th>
            <th><span class="nobr"><?php echo esc_html__('Remark', 'wooznd-smartpack'); ?></span></th>
            <th><span class="nobr"><?php echo esc_html__('Status', 'wooznd-smartpack'); ?></span></th>
            <th><span class="nobr"><?php echo esc_html__('Credit', 'wooznd-smartpack'); ?></span></th>
            <th><span class="nobr"><?php echo esc_html__('Debit', 'wooznd-smartpack'); ?></span></th>
            <th style="text-align: right;"><?php echo esc_html__('Date', 'wooznd-smartpack'); ?></th>
        </tr>
    </tfoot>
</table>

<div class="woocommerce-Pagination">
    <?php $paging->render_woo_links($url_format, [], $default_url, '+', esc_html__('Previous', 'wooznd-smartpack'), esc_html__('Next', 'wooznd-smartpack')); ?>
</div>

