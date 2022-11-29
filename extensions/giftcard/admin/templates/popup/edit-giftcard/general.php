<table class="woo-wide-form">
    <tr>
        <td>
            <div class="input-box">
                <div class="label">
                    <span><?php echo esc_html__('Discount Type', 'wooznd-smartpack'); ?></span>
                </div>
                <div class="input select-box">
                    <select name="discount_type">                            
                        <option value="fixed_cart"><?php echo esc_html__('Cart Discount', 'wooznd-smartpack'); ?></option>
                        <option value="fixed_product"<?php echo ($row['discount_type'] == 'fixed_product') ? ' selected="selected"' : ''; ?>><?php echo esc_html__('Product Discount', 'wooznd-smartpack'); ?></option>
                    </select>
                </div>
            </div>            
            <div class="input-box">
                <div class="label">
                    <span><?php echo esc_html__('Coupon Code', 'wooznd-smartpack'); ?></span>
                </div>
                <div class="input text-box">
                    <input name="coupon_code" value="<?php echo $row['coupon']; ?>" type="text" placeholder="<?php echo esc_html__('Coupon code', 'wooznd-smartpack'); ?>">
                </div>
            </div>          
            <div class="input-box">
                <div class="label">
                    <span><?php echo esc_html__('Gift Card Amout', 'wooznd-smartpack'); ?></span>
                </div>
                <div class="input text-box">
                    <input name="amount" type="number" value="<?php echo number_format($row['amount'], 2); ?>" min="0" step="0.05" value="20.00" placeholder="0.00">
                </div>
            </div>
            <div class="input-box last">
                <div class="label">
                    <span><?php echo esc_html__('Gift Card Balance', 'wooznd-smartpack'); ?></span>
                </div>
                <div class="input text-box">
                    <input name="coupon_amount" type="number" value="<?php echo number_format($row['coupon_amount'], 2); ?>" min="0" step="0.05" value="20.00" placeholder="0.00">
                </div>
            </div>

        </td>
        <td class="wide-second">
            <div class="input-box">
                <div class="label">
                    <span>Apply gift cards before tax</span>
                </div>
                <div class="input check-box">
                    <input id="apply_before_tax" name="apply_before_tax" value="yes"<?php echo ($row['apply_before_tax'] == 'yes') ? ' checked="checked"' : ''; ?> type="checkbox" />
                    <label for="apply_before_tax">Apply gift cards before tax</label>
                </div>
            </div>
            <div class="input-box">
                <div class="label">
                    <span>Allow free shipping</span>
                </div>
                <div class="input check-box">
                    <input id="free_shipping" name="free_shipping" value="yes"<?php echo ($row['free_shipping'] == 'yes') ? ' checked="checked"' : ''; ?> type="checkbox" />
                    <label for="free_shipping">Check this box if the gift card grants free shipping. A free shipping method must be enabled in your shipping zone and be set to require "a valid free shipping coupon" (see the "Free Shipping Requires" setting).</label>
                </div>
            </div>
            <div class="input-box">
                <div class="label">
                    <span><?php echo esc_html__('Event Date', 'wooznd-smartpack'); ?></span>
                </div>
                <div class="input text-box">
                    <input name="send_date" type="text" value="<?php echo WooZndUtil::MySQLTimeStampToDataTime($row['send_date'], 'Y-m-d'); ?>" class="pop_datepicker" value="<?php ?>" placeholder="Event Date">
                </div>
            </div>

            <div class="input-box last">
                <div class="label">
                    <span><?php echo esc_html__('Expiry Date', 'wooznd-smartpack'); ?></span>
                </div>
                <div class="input text-box">
                    <input name="expiry_date" type="text" value="<?php echo WooZndUtil::MySQLTimeStampToDataTime($row['expiry_date'], 'Y-m-d'); ?>" class="pop_datepicker" value="" placeholder="Expiry Date">
                </div>
            </div>
        </td>
    </tr>
</table>
