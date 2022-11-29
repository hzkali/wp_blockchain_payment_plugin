<?php
$wallet = WooZndWalletAccountDB::GetAccount(get_current_user_id());
$ledger_balance = $wallet['ledger_balance'];
$current_balance = $wallet['current_balance'];
$total_spent = $wallet['total_spent'];
?>
<div class="wooznd_wallet_brief">
    <h3><?php echo $wallet_title; ?></h3>
    <p>
        <?php
       if (!empty($ledger_label)) {
            ?>
            <strong><?php echo esc_html($ledger_label); ?></strong> <?php echo wc_price($ledger_balance); ?>&nbsp;
            <?php
        }
        if (!empty($current_label)) {
            ?>
            <strong><?php echo esc_html($current_label); ?></strong> <?php echo wc_price($current_balance); ?>&nbsp;
            <?php
        }
        if (!empty($total_spent_label)) {
            ?>
            <strong><?php echo esc_html($total_spent_label); ?></strong> <?php echo wc_price($total_spent); ?>
            <?php
        }
        ?>
    </p>
</div>