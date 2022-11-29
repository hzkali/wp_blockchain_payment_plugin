<form action="" method="post">
    <?php wp_nonce_field($wooznd_nonce_action, 'wznd_wallet_nonce'); ?>
    <input type="hidden" name="action_name" value="addnew">  
    <div class="text-block">
        <p><b><?php echo esc_html__('Note:', 'wooznd-smartpack'); ?></b> <?php echo esc_html__('You can create wallet for registered users only.', 'wooznd-smartpack'); ?></p>
    </div>
    <table class="woo-wide-form">
        <tr>
            <td>
                <div class="input-box">
                    <div class="label">
                        <span><?php echo esc_html__('User ID', 'wooznd-smartpack'); ?></span>
                    </div>
                    <div class="input text-box">
                        <input name="username" type="text" placeholder="<?php echo esc_html__('User name or email address', 'wooznd-smartpack'); ?>">
                    </div>
                </div>

                <div class="input-box">
                    <div class="label">
                        <span><?php echo esc_html__('Account Number', 'wooznd-smartpack'); ?></span>
                    </div>
                    <div class="input text-box">
                        <input type="text" disabled="disabled" value="<?php echo esc_html__('Auto generate', 'wooznd-smartpack'); ?>">
                    </div>
                </div>
                <div class="input-box">
                    <div class="label">
                        <span><?php echo esc_html__('Ledger Balance', 'wooznd-smartpack'); ?></span>
                    </div>
                    <div class="input text-box">
                        <input name="ledger_balance" type="number" min="0" step="0.05" placeholder="0.00">
                    </div>
                </div>

                <div class="input-box last">
                    <div class="label">
                        <span><?php echo esc_html__('Current Balance', 'wooznd-smartpack'); ?></span>
                    </div>
                    <div class="input text-box">
                        <input name="current_balance" type="number" min="0" step="0.05" placeholder="0.00">
                    </div>
                </div>

            </td>
            <td class="wide-second">
                <div class="input-box">
                    <div class="label">
                        <span><?php echo esc_html__('Total Spent', 'wooznd-smartpack'); ?></span>
                    </div>
                    <div class="input text-box">
                        <input name="total_spent" type="number" min="0" step="0.05" placeholder="0.00">
                    </div>
                </div>
                
                <div class="input-box">
                    <div class="label">
                        <span><?php echo esc_html__('Locked/Unlocked', 'wooznd-smartpack'); ?></span>
                    </div>
                    <div class="input select-box">
                        <select name="locked">                            
                            <option value="<?php echo WOOZND_WALLET_ACCOUNT_STATUS_UNLOCKED; ?>"><?php echo esc_html__('Unlocked', 'wooznd-smartpack'); ?></option>
                            <option value="<?php echo WOOZND_WALLET_ACCOUNT_STATUS_LOCKED; ?>"><?php echo esc_html__('Locked', 'wooznd-smartpack'); ?></option>
                        </select>
                    </div>
                </div>

                <div class="input-box last">
                    <div class="label" style="vertical-align: top">
                        <span><?php echo esc_html__('Remark', 'wooznd-smartpack'); ?></span>
                    </div>
                    <div class="input text-area">
                        <textarea name="remark" placeholder="<?php echo esc_html__('Remark', 'wooznd-smartpack'); ?>" style="height: 123px;"></textarea>
                    </div>
                </div>
            </td>
        </tr>
    </table>



    <div class="actions-box woo-wide-form">
        <input class="button button-primary" value="<?php echo esc_html__('Create Wallet', 'wooznd-smartpack'); ?>" type="submit">
        <input class="button button-secondary popup-btn-close" value="<?php echo esc_html__('Cancel', 'wooznd-smartpack'); ?>" type="button">
    </div>

</form>
