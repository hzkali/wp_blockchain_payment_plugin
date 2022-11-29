<?php
add_action('init', 'wooznd_register_giftcard_template_post_type');

function wooznd_register_giftcard_template_post_type() {

    $labels = array(
        "name" => esc_html__('Gift Card Email & PDF Templates', 'wooznd-smartpack'),
        "singular_name" => esc_html__('Gift Card Template', 'wooznd-smartpack'),
        "menu_name" => esc_html__('Gift Card Templates', 'wooznd-smartpack'),
        "all_items" => esc_html__('Gift Card Templates', 'wooznd-smartpack'),
        "add_new" => esc_html__('Add New', 'wooznd-smartpack'),
        "add_new_item" => esc_html__('Add New Template', 'wooznd-smartpack'),
        "edit_item" => esc_html__('Edit Template', 'wooznd-smartpack'),
        "new_item" => esc_html__('Add New Template', 'wooznd-smartpack'),
        "view_item" => esc_html__('View Template', 'wooznd-smartpack'),
        "search_items" => esc_html__('Search Template', 'wooznd-smartpack'),
    );

    $args = array(
        "label" => esc_html__('Gift Card Email & PDF Templates', 'wooznd-smartpack'),
        "labels" => $labels,
        "description" => "",
        "public" => false,
        "show_ui" => true,
        "show_in_rest" => false,
        "rest_base" => "",
        "has_archive" => false,
        "show_in_menu" => 'wznd-manage-giftcard',
        "exclude_from_search" => true,
        "capability_type" => "post",
        "map_meta_cap" => true,
        "hierarchical" => false,
        "rewrite" => array("slug" => "wznd_giftcard", "with_front" => false),
        "query_var" => "wznd_giftcard",
        "menu_position" => 100, "menu_icon" => "dashicons-schedule",
        "supports" => array("title", "editor", "custom-fields"),
    );
    register_post_type("wznd_giftcard", $args);
}

add_action('edit_form_after_title', 'wooznd_display_giftcard_template_instructions');

function wooznd_display_giftcard_template_instructions() {

    $scr = get_current_screen();
    if ($scr->post_type !== 'wznd_giftcard') {
        return;
    }
    ?>
    <br />
    <div id="wznd_giftcard_inst" class="postbox ">
        <button type="button" class="handlediv button-link" aria-expanded="true"><span class="screen-reader-text">Toggle panel: Custom CSS</span><span class="toggle-indicator" aria-hidden="true"></span></button>
        <h2 class="hndle ui-sortable-handle"><span>Instructions</span></h2>
        <div class="inside">
            <table>
                <tr>
                    <td style="width:180px;"><b>[wznd_coupon]</b></td>
                    <td><?php echo esc_html__('Displays gift card coupon code', 'wooznd-smartpack'); ?></td>
                </tr>
                <tr>
                    <td><b>[wznd_message]</b></td>
                    <td><?php echo esc_html__('Displays gift card message', 'wooznd-smartpack'); ?></td>
                </tr>
                <tr>
                    <td><b>[wznd_amount]</b></td>
                    <td><?php echo esc_html__('Displays gift card amount', 'wooznd-smartpack'); ?></td>
                </tr>
                <tr>
                    <td><b>[wznd_expirydate]</b></td>
                    <td><?php echo esc_html__('Displays gift card expiry date', 'wooznd-smartpack'); ?></td>
                </tr>
                <tr>
                    <td><b>[wznd_toname]</b></td>
                    <td><?php echo esc_html__('Displays gift card to recipient name', 'wooznd-smartpack'); ?></td>
                </tr>
                <tr>
                    <td><b>[wznd_toemail]</b></td>
                    <td><?php echo esc_html__('Displays gift card to recipient email', 'wooznd-smartpack'); ?></td>
                </tr>
                <tr>
                    <td><b>[wznd_fromname]</b></td>
                    <td><?php echo esc_html__('Displays gift card to sender name', 'wooznd-smartpack'); ?></td>
                </tr>
                <tr>
                    <td><b>[wznd_fromemail]</b></td>
                    <td><?php echo esc_html__('Displays gift card to sender email', 'wooznd-smartpack'); ?></td>
                </tr>
                <tr>
                    <td><b>[wznd_qrcode]</b></td>
                    <td><?php echo esc_html__('Displays gift card qrcode', 'wooznd-smartpack'); ?></td>
                </tr>
                <tr>
                    <td><b>[wznd_barcode]</b></td>
                    <td><?php echo esc_html__('Displays gift card barcode', 'wooznd-smartpack'); ?></td>
                </tr>
            </table>
        </div>
    </div>
    <?php
}

add_action('add_meta_boxes', 'wooznd_add_giftcard_template_metabox');

function wooznd_add_giftcard_template_metabox() {

    add_meta_box('wznd_giftcard_css', esc_html__('Custom CSS', 'wooznd-smartpack'), 'wooznd_giftcard_template_custom_css', 'wznd_giftcard');
}

function wooznd_giftcard_template_custom_css($post) {

    wp_nonce_field(basename(__FILE__), 'wznd_giftcard_nonce');
    $wznd_custom_css = get_post_meta($post->ID, '_wznd_giftcard_custom_css', true);
    ?>
    <p> 
        <textarea name="_wznd_giftcard_custom_css" style="width:100%; min-height: 200px"><?php echo isset($wznd_custom_css) ? $wznd_custom_css : ''; ?></textarea>
    </p>
    <?php
}

add_action('save_post', 'giftcard_template_meta_save');

function giftcard_template_meta_save($post_id) {

    // Checks save status
    $is_autosave = wp_is_post_autosave($post_id);
    $is_revision = wp_is_post_revision($post_id);
    $is_valid_nonce = ( isset($_POST['wznd_giftcard_nonce']) && wp_verify_nonce($_POST['wznd_giftcard_nonce'], basename(__FILE__)) ) ? 'true' : 'false';

    // Exits script depending on save status
    if ($is_autosave || $is_revision || !$is_valid_nonce) {
        return;
    }

    // Checks for input and sanitizes/saves if needed
    if (isset($_POST['_wznd_giftcard_custom_css'])) {
        update_post_meta($post_id, '_wznd_giftcard_custom_css', sanitize_textarea_field($_POST['_wznd_giftcard_custom_css']));
    }
}
