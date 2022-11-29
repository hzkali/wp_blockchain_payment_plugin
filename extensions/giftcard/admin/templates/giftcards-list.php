<?php include 'giftcards-list_inc.php'; ?>
<div class="wrap woo_wallet">
    <h1 class="wp-heading-inline"><?php echo esc_html__('All Gift Cards', 'wooznd-smartpack'); ?></h1>

    <a href="#" class="page-title-action new-giftcard"><?php echo esc_html__('Add Gift Card', 'wooznd-smartpack'); ?></a>
<!--    <a href="#" class="page-title-action"><?php echo esc_html__('Import', 'wooznd-smartpack'); ?></a>
    <a href="#" class="page-title-action"><?php echo esc_html__('Export', 'wooznd-smartpack'); ?></a>-->
    <div class="popup-hidden new-giftcard-template" data-title="<?php echo esc_html__('Add New Gift Card', 'wooznd-smartpack'); ?>">
        <?php include 'popup/new-giftcard.php'; ?>
    </div>
    <hr class="wp-header-end">
    <h2 class="screen-reader-text"><?php echo esc_html__('Filter gift card list', 'wooznd-smartpack'); ?></h2>
    <ul class="subsubsub">
        <li><a href="<?php echo esc_attr(admin_url('admin.php?page=wznd-manage-giftcard')); ?>" class="<?php echo ($status == -1 && empty($_GET['exp'])) ? 'current' : ''; ?>"><?php echo esc_html__('All', 'wooznd-smartpack'); ?> <span class="count">(<?php echo WooZndGiftCardDB::GetGiftCardsCount('', -1); ?>)</span></a> |</li>
        <li><a href="<?php echo esc_attr(admin_url('admin.php?page=wznd-manage-giftcard&status=' . WOOZND_GIFTCARD_STATUS_PENDING)); ?>" class="<?php echo $status == WOOZND_GIFTCARD_STATUS_PENDING ? 'current' : ''; ?>"><?php echo esc_html__('Pending', 'wooznd-smartpack'); ?> <span class="count">(<?php echo WooZndGiftCardDB::GetGiftCardsCount('', WOOZND_GIFTCARD_STATUS_PENDING); ?>)</span></a> |</li>
        <li><a href="<?php echo esc_attr(admin_url('admin.php?page=wznd-manage-giftcard&status=' . WOOZND_GIFTCARD_STATUS_SENT)); ?>" class="<?php echo $status == WOOZND_GIFTCARD_STATUS_SENT ? 'current' : ''; ?>"><?php echo esc_html__('Sent', 'wooznd-smartpack'); ?> <span class="count">(<?php echo WooZndGiftCardDB::GetGiftCardsCount('', WOOZND_GIFTCARD_STATUS_SENT); ?>)</span></a></li>
        <li><a href="<?php echo esc_attr(admin_url('admin.php?page=wznd-manage-giftcard&status=' . WOOZND_GIFTCARD_STATUS_USED)); ?>" class="<?php echo $status == WOOZND_GIFTCARD_STATUS_USED ? 'current' : ''; ?>"><?php echo esc_html__('Used', 'wooznd-smartpack'); ?> <span class="count">(<?php echo WooZndGiftCardDB::GetGiftCardsCount('', WOOZND_GIFTCARD_STATUS_USED); ?>)</span></a></li>
        <li><a href="<?php echo esc_attr(admin_url('admin.php?page=wznd-manage-giftcard&status=' . WOOZND_GIFTCARD_STATUS_EXHAUSTED)); ?>" class="<?php echo $status == WOOZND_GIFTCARD_STATUS_EXHAUSTED ? 'current' : ''; ?>"><?php echo esc_html__('Exhausted', 'wooznd-smartpack'); ?> <span class="count">(<?php echo WooZndGiftCardDB::GetGiftCardsCount('', WOOZND_GIFTCARD_STATUS_EXHAUSTED); ?>)</span></a></li>
        <li><a href="<?php echo esc_attr(admin_url('admin.php?page=wznd-manage-giftcard&status=' . WOOZND_GIFTCARD_STATUS_REFUNDED)); ?>" class="<?php echo $status == WOOZND_GIFTCARD_STATUS_REFUNDED ? 'current' : ''; ?>"><?php echo esc_html__('Refunded', 'wooznd-smartpack'); ?> <span class="count">(<?php echo WooZndGiftCardDB::GetGiftCardsCount('', WOOZND_GIFTCARD_STATUS_REFUNDED); ?>)</span></a></li>
        <li>|</li>
        <li><a href="<?php echo esc_attr(admin_url('admin.php?page=wznd-manage-giftcard&exp=1')); ?>" class="<?php echo ($status == -1 && $_GET['exp'] == 1) ? 'current' : ''; ?>"><?php echo esc_html__('Expired', 'wooznd-smartpack'); ?> <span class="count">(<?php echo WooZndGiftCardDB::GetExpiredGiftCardsCount('', -1); ?>)</span></a></li>
    </ul>
    <form id="posts-filter" method="get" action="<?php echo admin_url('admin.php'); ?>">
        <input type="hidden" name="page" value="wznd-manage-giftcard" />
        <?php
        foreach ($url_options as $key => $value) {
            if (!($key == 'search' )) {
                ?>
                <input type="hidden" name="<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($value); ?>" />
                <?php
            }
        }
        ?>
        <p class="search-box">
            <input type="search" style="width:200px;" name="search" placeholder="<?php echo esc_html__('gift card coupon', 'wooznd-smartpack'); ?>" value="<?php echo esc_attr($search); ?>">
            <input type="submit" class="button" value="<?php echo esc_html__('Search', 'wooznd-smartpack'); ?>"></p>
    </form>
    <form id="posts-filter" method="get" action="<?php echo admin_url('admin.php'); ?>">
        <input type="hidden" name="page" value="wznd-manage-giftcard" />
        <?php
        foreach ($url_options as $key => $value) {
            if (!($key == 'orderby' || $key == 'order')) {
                ?>
                <input type="hidden" name="<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($value); ?>" />
                <?php
            }
        }
        ?>
        <div class="tablenav top">
            <div class="alignleft actions"> 
                <select name="orderby" >
                    <?php $ord = $orderby; ?>
                    <option value="event"><?php echo esc_html__('Sort by event date', 'wooznd-smartpack'); ?></option>                    
                    <option <?php echo $ord == 'expiry' ? 'selected="selected"' : ''; ?> value="expiry"><?php echo esc_html__('Sort by expiry date', 'wooznd-smartpack'); ?></option>
                    <option <?php echo $ord == 'coupon' ? 'selected="selected"' : ''; ?> value="coupon"><?php echo esc_html__('Sort by coupon code', 'wooznd-smartpack'); ?></option>
                    <option <?php echo $ord == 'amount' ? 'selected="selected"' : ''; ?> value="amount"><?php echo esc_html__('Sort by amount', 'wooznd-smartpack'); ?></option>
                </select>
                <select name="order">
                    <?php $ordt = $order; ?>
                    <option value="desc"><?php echo esc_html__('Descending', 'wooznd-smartpack'); ?></option>
                    <option <?php echo $ordt == 'asc' ? 'selected="selected"' : ''; ?> value="asc"><?php echo esc_html__('Ascending', 'wooznd-smartpack'); ?></option>                    
                </select>
                <input type="submit" class="button" value="<?php echo esc_html__('Sort', 'wooznd-smartpack'); ?>">		
            </div>

            <div class="pages">
                <span class="displaying-num"><?php esc_html($paging->render_result_count(esc_html__('{{from}} to {{to}} of {{total}} items', 'wooznd-smartpack'))); ?></span>
                <?php wp_kses_post($paging->render_links($url_format, 5, $url_options, $default_url, '+')); ?>
            </div>
            <br class="clear">
        </div>
    </form>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th style="width:116px;"><b><?php echo esc_html__('Coupon Code', 'wooznd-smartpack'); ?></b></th>
                <th style="width: 92px;"><b><?php echo esc_html__('Amount', 'wooznd-smartpack'); ?></b></th>
                <th style="width: 92px;"><b><?php echo esc_html__('Balance', 'wooznd-smartpack'); ?></b></th>
                <th><b><?php echo esc_html__('To', 'wooznd-smartpack'); ?></b></th>
                <th style="width: 130px;"><b><?php echo esc_html__('Event Date', 'wooznd-smartpack'); ?></b></th>  
                <th style="width: 130px;"><b><?php echo esc_html__('Expiry Date', 'wooznd-smartpack'); ?></b></th>                
                <th style="width: 77px;"><b><?php echo esc_html__('Actions', 'wooznd-smartpack'); ?></b></th>
            </tr>
        </thead>

        <tbody>
            <?php
            foreach ($rows as $row) {
                ?>
                <tr>
                    <td><b><?php echo esc_html(strtoupper($row['coupon'])); ?></b></td>                    
                    <td><?php echo wc_price($row['amount']); ?></td>
                    <td><?php echo wc_price($row['coupon_amount']); ?></td> 
                    <td><?php echo inc_util_giftcard_to_name($row); ?></td>
                    <td><?php echo isset($row['send_date']) ? WooZndUtil::MySQLTimeStampToDataTime($row['send_date'], get_option('date_format')) : esc_html__('N/A', 'wooznd-smartpack'); ?></td>
                    <td><?php echo isset($row['expiry_date']) ? WooZndUtil::MySQLTimeStampToDataTime($row['expiry_date'], get_option('date_format')) : esc_html__('N/A', 'wooznd-smartpack'); ?></td>
                    <td>
                        <a class="button edit-giftcard"><?php echo esc_html__('View/Edit', 'wooznd-smartpack'); ?></a>
                        <div class="popup-hidden edit-giftcard-template" data-title="<?php echo esc_html__('View/Edit Gift Card', 'wooznd-smartpack'); ?>">
                            <?php include 'popup/edit-giftcard.php'; ?>
                        </div>
                    </td>

                </tr>    
                <?php
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <th><b><?php echo esc_html__('Coupon Code', 'wooznd-smartpack'); ?></b></th>
                <th><b><?php echo esc_html__('Amount', 'wooznd-smartpack'); ?></b></th>
                <th><b><?php echo esc_html__('Balance', 'wooznd-smartpack'); ?></b></th>
                <th><b><?php echo esc_html__('To', 'wooznd-smartpack'); ?></b></th>
                <th><b><?php echo esc_html__('Event Date', 'wooznd-smartpack'); ?></b></th>  
                <th><b><?php echo esc_html__('Expiry Date', 'wooznd-smartpack'); ?></b></th>                
                <th><b><?php echo esc_html__('Actions', 'wooznd-smartpack'); ?></b></th>
            </tr>
        </tfoot>
    </table>
    <div class="tablenav bottom">
        <div class="pages">
            <span class="displaying-num"><?php $paging->render_result_count(esc_html__('{{from}} to {{to}} of {{total}} items', 'wooznd-smartpack')); ?></span>
            <?php $paging->render_links($url_format, 5, $url_options, $default_url, '+'); ?>
        </div>
        <br class="clear">
    </div>
</div>