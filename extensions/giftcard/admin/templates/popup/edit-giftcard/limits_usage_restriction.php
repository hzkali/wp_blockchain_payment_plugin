<table class="woo-wide-form">
    <tr>
        <td>
            <div class="input-box">
                <div class="label">
                    <span><?php echo esc_html__('Minimum spend', 'wooznd-smartpack'); ?></span>
                </div>
                <div class="input text-box">
                    <input name="minimum_amount" type="number" value="<?php echo $row['minimum_amount']; ?>" min="0" step="0.05" placeholder="<?php echo esc_html__('No Minimum', 'wooznd-smartpack') ?>">
                </div>
            </div>
            <div class="input-box">
                <div class="label">
                    <span><?php echo esc_html__('Maximum spend', 'wooznd-smartpack'); ?></span>
                </div>
                <div class="input text-box">
                    <input name="maximum_amount" type="number" value="<?php echo $row['maximum_amount']; ?>" min="0" step="0.05" placeholder="<?php echo esc_html__('No Maximum', 'wooznd-smartpack') ?>">
                </div>
            </div>
            
            <div class="input-box last">
                <div class="label">
                    <span>Exclude sale items</span>
                </div>
                <div class="input check-box">
                    <input id="exclude_sale_items" name="exclude_sale_items" value="yes"<?php echo ($row['exclude_sale_items'] == 'yes') ? ' checked="checked"' : ''; ?> type="checkbox" />
                    <label for="exclude_sale_items">Check this box if gift cards should not apply to items on sale. Per-item gift card will only work if the item is not on sale. Per-cart gift cards will only work if there are no sale items in the cart.</label>
                </div>
            </div>
        </td>
        <td class="wide-second">
            <div class="input-box">
                <div class="label">
                    <span>Individual use only</span>
                </div>
                <div class="input check-box">
                    <input id="individual_use" name="individual_use" value="yes"<?php echo ($row['individual_use'] == 'yes') ? ' checked="checked"' : ''; ?> type="checkbox" />
                    <label for="individual_use">Check this box if a gift card cannot be used in conjunction with other gift cards or coupons.</label>
                </div>
            </div>
            <div class="input-box">
                <div class="label">
                    <span><?php echo esc_html__('Usage limit per user', 'wooznd-smartpack'); ?></span>
                </div>
                <div class="input text-box">
                    <input name="usage_limit_per_user" type="number" value="<?php echo (!empty($row['usage_limit_per_user']))?$row['usage_limit_per_user']:''; ?>" min="0" step="1" placeholder="<?php echo esc_html__('Unlimited Usage', 'wooznd-smartpack') ?>">
                </div>
            </div>

            <div class="input-box last">
                <div class="label">
                    <span><?php echo esc_html__('Usage limit per gift card', 'wooznd-smartpack'); ?></span>
                </div>
                <div class="input text-box">
                    <input name="usage_limit" type="number" value="<?php echo (!empty($row['usage_limit']))?$row['usage_limit']:''; ?>" min="0" step="1" placeholder="<?php echo esc_html__('Unlimited Usage', 'wooznd-smartpack') ?>">
                </div>
            </div>
        </td>
    </tr>
</table>
