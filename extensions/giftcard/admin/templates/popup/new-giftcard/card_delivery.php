<table class="woo-wide-form">
    <tr>
        <td>

            <div class="input-box">
                <div class="label">
                    <span><?php echo esc_html__('Delivary Method', 'wooznd-smartpack'); ?></span>
                </div>
                <div class="input select-box">
                    <select name="delivery_method" class="wc-enhanced-select">                            
                        <option value="<?php echo WOOZND_GIFTCARD_DELIVERY_OFFLINE; ?>"><?php echo esc_html__('Print & Send', 'wooznd-smartpack'); ?></option>
                        <option value="<?php echo WOOZND_GIFTCARD_DELIVERY_EMAIL; ?>"><?php echo esc_html__('Email Address', 'wooznd-smartpack'); ?></option>
                    </select>
                </div>
            </div>
            
            <div class="input-box">
                <div class="label">
                    <span><?php echo esc_html__('Sender name', 'wooznd-smartpack'); ?></span>
                </div>
                <div class="input text-box">
                    <input type="text" name="sender_name" placeholder="<?php echo esc_html__('Full name', 'wooznd-smartpack'); ?>">
                </div>
            </div>
            
            <div class="input-box last">
                <div class="label">
                    <span><?php echo esc_html__('Sender email', 'wooznd-smartpack'); ?></span>
                </div>
                <div class="input text-box">
                    <input type="email" name="sender_email" placeholder="<?php echo esc_html__('Email address', 'wooznd-smartpack'); ?>">
                </div>
            </div>

        </td>
        <td class="wide-second">
            <div class="input-box">
                <div class="label">
                    <span><?php echo esc_html__('Receiver name', 'wooznd-smartpack'); ?></span>
                </div>
                <div class="input text-box">
                    <input type="text" name="receiver_name" placeholder="<?php echo esc_html__('Full name', 'wooznd-smartpack'); ?>">
                </div>
            </div>
            
            <div class="input-box last">
                <div class="label">
                    <span><?php echo esc_html__('Receiver email', 'wooznd-smartpack'); ?></span>
                </div>
                <div class="input text-box">
                    <input type="email" name="receiver_email" placeholder="<?php echo esc_html__('Email address', 'wooznd-smartpack'); ?>">
                </div>
            </div>
        </td>
    </tr>
</table>
