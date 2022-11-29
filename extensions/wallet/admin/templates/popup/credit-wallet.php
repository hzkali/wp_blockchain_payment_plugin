<form action="" method="post">
    <?php wp_nonce_field($wooznd_nonce_action, 'wznd_wallet_nonce'); ?>
    <input type="hidden" name="action_name" value="credit" />
    <input type="hidden" name="user_id" value="<?php echo esc_attr($row['id']); ?>">
    <table class="woo-wide-form">
        <tr>
            <td>
                <div class="input-box">
                    <div class="label">
                        <span><?php echo esc_html__('Account Number', 'wooznd-smartpack'); ?></span>
                    </div>
                    <div class="input text-box">
                        <input disabled="disabled" type="text" value="<?php echo esc_attr($row['account_number']); ?>">
                    </div>
                </div>

                <div class="input-box">
                    <div class="label">
                        <span><?php echo esc_html__('Beneficiary', 'wooznd-smartpack'); ?></span>
                    </div>
                    <div class="input text-box">
                        <input disabled="disabled" type="text" value="<?php echo esc_attr($row['first_name'] . ' ' . $row['last_name']); ?>">
                    </div>
                </div>

                <div class="input-box">
                    <div class="label">
                        <span><?php echo esc_html__('Amount', 'wooznd-smartpack'); ?></span>
                    </div>
                    <div class="input text-box">
                        <input name="amount" type="number" min="0" step="0.05" placeholder="0.00">
                    </div>
                </div>

                <div class="input-box last">
                    <div class="label">
                        <span><?php echo esc_html__('Status', 'wooznd-smartpack'); ?></span>
                    </div>
                    <div class="input select-box">
                        <select name="status">
                            <option value="<?php echo WOOZND_WALLET_TRANSANCTION_STATUS_PENDING; ?>"><?php echo esc_html__('Pending', 'wooznd-smartpack'); ?></option>
                            <option value="<?php echo WOOZND_WALLET_TRANSANCTION_STATUS_ONHOLD; ?>"><?php echo esc_html__('On Hold', 'wooznd-smartpack'); ?></option>
                            <option value="<?php echo WOOZND_WALLET_TRANSANCTION_STATUS_PROCESSING; ?>"><?php echo esc_html__('Processing', 'wooznd-smartpack'); ?></option>
                            <option value="<?php echo WOOZND_WALLET_TRANSANCTION_STATUS_COMPLETED; ?>"><?php echo esc_html__('Completed', 'wooznd-smartpack'); ?></option>
                        </select>
                    </div>
                </div>
            </td>
            <td class="wide-second">
                <div class="input-box">
                    <div class="label">
                        <span><?php echo esc_html__('Type', 'wooznd-smartpack'); ?></span>
                    </div>
                    <div class="input select-box">
                        <select name="transtype">                
                            <option value="<?php echo WOOZND_WALLET_TRANSANCTION_CREDIT; ?>"><?php echo esc_html__('Credit', 'wooznd-smartpack'); ?></option>
                            <option value="<?php echo WOOZND_WALLET_TRANSANCTION_DEPOSIT; ?>"><?php echo esc_html__('Deposit', 'wooznd-smartpack'); ?></option>                
                            <option value="<?php echo WOOZND_WALLET_TRANSANCTION_REFUND; ?>"><?php echo esc_html__('Refund', 'wooznd-smartpack'); ?></option>
                            <option value="<?php echo WOOZND_WALLET_TRANSANCTION_TRANSFER; ?>"><?php echo esc_html__('Transfer', 'wooznd-smartpack'); ?></option>                
                        </select>
                    </div>
                </div>
                <div class="input-box last">
                    <div class="label" style="vertical-align: top">
                        <span><?php echo esc_html__('Remark', 'wooznd-smartpack'); ?></span>
                    </div>
                    <div class="input text-area">
                        <textarea name="remark" placeholder="<?php echo esc_html__('Remark', 'wooznd-smartpack'); ?>" style="height: 200px;"></textarea>
                    </div>
                </div>
            </td>
        </tr>
    </table>
    <div class="actions-box woo-wide-form">
        <input class="button button-primary" value="<?php echo esc_html__('Add Funds', 'wooznd-smartpack'); ?>" type="submit">
        <input class="button button-secondary popup-btn-close" value="<?php echo esc_html__('Cancel', 'wooznd-smartpack'); ?>" type="button">
    </div>

</form>
