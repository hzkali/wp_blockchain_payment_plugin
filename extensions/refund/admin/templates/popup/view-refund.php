<form action="" method="post">
    <?php wp_nonce_field($wooznd_nonce_action, 'wznd_refund_nonce'); ?>
    <input type="hidden" name="action_name" value="update-refund">
    <input type="hidden" name="refund_id" value="<?php echo esc_attr($row['order_id']); ?>" />

    <div class="text-block">
        <p><b><?php echo esc_html__('Requested On:', 'wooznd-smartpack'); ?></b> <?php echo isset($row['request_date']) ? WooZndUtil::MySQLTimeStampToDataTime($row['request_date'], get_option('date_format')) : esc_html__('N/A', 'wooznd-smartpack'); ?>.</p>
    </div>
    <table class="woo-wide-form">
        <tr>
            <td>
                <div class="input-box">
                    <div class="label">
                        <span><?php echo esc_html__('Order Number', 'wooznd-smartpack'); ?></span>
                    </div>
                    <div class="input text-box">
                        <input type="text" disabled="disabled" value="<?php echo esc_attr('#' . $order->get_order_number()); ?>">
                    </div>
                </div>

                <div class="input-box">
                    <div class="label">
                        <span><?php echo esc_html__('Account Number', 'wooznd-smartpack'); ?></span>
                    </div>
                    <div class="input text-box">
                        <input type="text" disabled="disabled" value="<?php echo esc_attr($row['account_number']); ?>">
                    </div>
                </div>

                <div class="input-box">
                    <div class="label">
                        <span><?php echo esc_html__('Order Amount', 'wooznd-smartpack'); ?></span>
                    </div>
                    <div class="input text-box">
                        <input type="text" disabled="disabled" value="<?php echo esc_attr($order->get_total()); ?>">
                    </div>
                </div>
                <div class="input-box last">
                    <div class="label">
                        <span><?php echo esc_html__('Request Amount', 'wooznd-smartpack'); ?></span>
                    </div>
                    <div class="input text-box">
                        <input type="text" disabled="disabled" value="<?php echo esc_attr($row['request_amount']); ?>">
                    </div>
                </div>


            </td>
            <td class="wide-second">
                <?php
                if ($row['status'] == WOOZND_WALLET_REFUND_REQUEST_APROVED || $row['status'] == WOOZND_WALLET_REFUND_REQUEST_REJECTED) {
                    ?>
                    <div class="input-box">
                        <div class="label">
                            <span><?php echo esc_html__('Amount Refunded', 'wooznd-smartpack'); ?></span>
                        </div>
                        <div class="input text-box">
                            <input type="text" disabled="disabled" value="<?php echo esc_attr($order->get_total_refunded()); ?>">
                        </div>
                    </div>
                    <?php
                } else {

                    
                    ?>
                    <div class="input-box">
                        <div class="label">
                            <span><?php echo esc_html__('Refundable Amount', 'wooznd-smartpack'); ?></span>
                        </div>
                        <div class="input text-box">
                            <input type="text" name="request_amount" value="<?php echo esc_attr($row['request_amount']-$order->get_total_refunded()); ?>">
                        </div>
                    </div>
    <?php
}
?>
                <?php
                if ($row['status'] == WOOZND_WALLET_REFUND_REQUEST_APROVED || $row['status'] == WOOZND_WALLET_REFUND_REQUEST_REJECTED) {
                    ?>
                    <div class="input-box">
                        <div class="label">
                            <span><?php echo esc_html__('Status', 'wooznd-smartpack'); ?></span>
                        </div>
                        <div class="input text-box">
                            <input type="text" disabled="disabled" value="<?php echo esc_attr(WooZndRefund::RefundStatusString($row['status'], false)); ?>">
                        </div>
                    </div>
    <?php
} else {
    ?>
                    <div class="input-box">
                        <div class="label">
                            <span><?php echo esc_html__('Status', 'wooznd-smartpack'); ?></span>
                        </div>                    
                        <div class="input select-box">
                            <select name="status">                            
                                <option value="<?php echo WOOZND_WALLET_REFUND_REQUEST_PENDING; ?>"><?php echo esc_html__('Pending', 'wooznd-smartpack'); ?></option>
                                <option<?php echo $row['status'] == WOOZND_WALLET_REFUND_REQUEST_APROVED ? ' selected="selected"' : ''; ?> value="<?php echo WOOZND_WALLET_REFUND_REQUEST_APROVED; ?>"><?php echo esc_html__('Aproved', 'wooznd-smartpack'); ?></option>
                                <option<?php echo $row['status'] == WOOZND_WALLET_REFUND_REQUEST_REJECTED ? ' selected="selected"' : ''; ?> value="<?php echo WOOZND_WALLET_REFUND_REQUEST_REJECTED; ?>"><?php echo esc_html__('Rejected', 'wooznd-smartpack'); ?></option>                                
                            </select>
                        </div>
                    </div>    
    <?php
}
?>


                <div class="input-box last">
                    <div class="label" style="vertical-align: top">
                        <span><?php echo esc_html__('Reason', 'wooznd-smartpack'); ?></span>
                    </div>
                    <div class="input text-area">
                        <textarea name="reason"<?php echo ($row['status'] == WOOZND_WALLET_REFUND_REQUEST_APROVED || $row['status'] == WOOZND_WALLET_REFUND_REQUEST_REJECTED) ? ' disabled="disabled"' : ''; ?> placeholder="<?php echo esc_html__('Reason', 'wooznd-smartpack'); ?>" style="height: 117px;"><?php echo esc_textarea($row['reason']); ?></textarea>
                    </div>
                </div>
            </td>
        </tr>
    </table>
    <div class="actions-box woo-wide-form">
<?php
if ($row['status'] == WOOZND_WALLET_REFUND_REQUEST_APROVED || $row['status'] == WOOZND_WALLET_REFUND_REQUEST_REJECTED) {
    ?>
            <input class="button button-secondary popup-btn-close" value="<?php echo esc_html__('Close', 'wooznd-smartpack'); ?>" type="button">                 
            <?php
        } else {
            ?>
            <input class="button button-primary" value="<?php echo esc_html__('Update', 'wooznd-smartpack'); ?>" type="submit">
            <input class="button button-secondary popup-btn-close" value="<?php echo esc_html__('Cancel', 'wooznd-smartpack'); ?>" type="button">                 
            <?php
        }
        ?>    
        <div style="float: right;">
            <input class="button delete-theme" name="delete" value="<?php echo esc_html__('Delete', 'wooznd-smartpack'); ?>" type="submit">
        </div>
    </div>

</form>
