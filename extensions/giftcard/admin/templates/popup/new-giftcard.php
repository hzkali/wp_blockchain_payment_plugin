<form action="" method="post">
    <?php wp_nonce_field($wooznd_nonce_action, 'wznd_giftcard_nonce'); ?>
    <input type="hidden" name="action_name" value="addgiftcard">  
    <div class="text-block">
        <p><?php echo esc_html__('Create and sell unlimited number of gift cards.', 'wooznd-smartpack'); ?></p>
    </div>
    <div class="wsp-tabs">
        <ul class="wsp-tabs-head">
            <li class="wsp-active"><a id="m" href="#new_general"><?php echo esc_html__('General', 'wooznd-smartpack'); ?></a></li>
            <li><a href="#new_limits"><?php echo esc_html__('Limits & Usage Restriction', 'wooznd-smartpack'); ?></a></li>
            <li><a href="#new_pdf_email"><?php echo esc_html__('PDF & Email Template', 'wooznd-smartpack'); ?></a></li> 
            <li><a href="#new_delivery"><?php echo esc_html__('Card Delivery', 'wooznd-smartpack'); ?></a></li>            
        </ul>
        <div class="wsp-tabs-body">
            <div id="new_general" class="wsp-tabs-body-content wsp-active">
                <?php include 'new-giftcard/general.php'; ?>
            </div>
            <div id="new_limits" class="wsp-tabs-body-content">
                <?php include 'new-giftcard/limits_usage_restriction.php'; ?>
            </div>
            <div id="new_pdf_email" class="wsp-tabs-body-content">
                <?php include 'new-giftcard/pdf_email.php'; ?>
            </div>
            <div id="new_delivery" class="wsp-tabs-body-content">
                <?php include 'new-giftcard/card_delivery.php'; ?>
            </div>

        </div>
    </div>

    <div class="actions-box woo-wide-form">
        <input class="button button-primary" value="<?php echo esc_html__('Create Gift Card', 'wooznd-smartpack'); ?>" type="submit">
        <input class="button button-secondary popup-btn-close" value="<?php echo esc_html__('Cancel', 'wooznd-smartpack'); ?>" type="button">
    </div>

</form>
