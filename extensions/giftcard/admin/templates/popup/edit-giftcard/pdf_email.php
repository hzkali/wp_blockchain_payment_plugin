<?php $templ = WooZndUtil::GetPostTypeOption('wznd_giftcard'); ?>
<div class="input-box">
    <div class="label">
        <span><?php echo esc_html__('PDF Template', 'wooznd-smartpack'); ?></span>
    </div>
    <div class="input select-box">
        <select name="pdf_template_id" class="wc-enhanced-select">
            <?php
            foreach ($templ as $key => $value) {
                ?>
                <option value="<?php echo $key; ?>"<?php echo ($key==$row['giftcard_template_id'])?' selected="selected"':''; ?>><?php echo $value; ?></option>
                <?php
            }
            ?>
        </select>
    </div>
</div>   
<div class="input-box">
    <div class="label">
        <span><?php echo esc_html__('Email Template', 'wooznd-smartpack'); ?></span>
    </div>
    <div class="input select-box">
        <select name="email_template_id"> 
            <option value=""><?php echo esc_html__('Default', 'wooznd-smartpack'); ?></option>
            <?php
            foreach ($templ as $key => $value) {
                ?>
                <option value="<?php echo $key; ?>"<?php echo ($key==$row['email_template_id'])?' selected="selected"':''; ?>><?php echo $value; ?></option>
                <?php
            }
            ?>
        </select>
    </div>
</div>   
<div class="input-box last">
    <div class="label" style="vertical-align: top">
        <span><?php echo esc_html__('Message', 'wooznd-smartpack'); ?></span>
    </div>
    <div class="input text-area">
        <textarea name="message" placeholder="<?php echo esc_html__('Message', 'wooznd-smartpack'); ?>" style="height: 123px;"><?php echo esc_textarea($row['message']); ?></textarea>
    </div>
</div>
