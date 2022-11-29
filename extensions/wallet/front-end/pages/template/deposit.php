<form action="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')) . 'wwl-my-wallet'; ?>" method="post">
    <div class="wooznd_wallet_deposit">
        <h3><?php echo $deposit_title; ?></h3>
        <input type="text" name="wznd_wallet_deposit" placeholder="<?php echo $placeholder; ?>" />        
        <button><?php echo $button_text; ?></button>
    </div>
</form>