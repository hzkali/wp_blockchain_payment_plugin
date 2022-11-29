<?php

if (!function_exists('wznd_create_giftcard_pdf')) {

    function wznd_create_giftcard_pdf($item_id, $template_id) {
        $pst = get_post($template_id);
        if (!isset($pst->ID)) {
            return;
        }
        $giftcard_path = WP_CONTENT_DIR . '/uploads/woo-smart-pack/giftcards/giftcard' . $item_id . '.pdf';
        $css_styles = get_post_meta($template_id, '_wznd_giftcard_custom_css', true);

        $html = (isset($css_styles) ? '<style>' . $css_styles . '</style>' : '_wznd_giftcard_custom_css') . do_shortcode($pst->post_content);

        include_once("mpdf.php");

        $mpdf = new mPDF('c', 'A4', '', '', 0, 0, 0, 0, 0, 0);

        $mpdf->WriteHTML($html);

        $mpdf->Output($giftcard_path, 'F');
        
        return $giftcard_path;
    }

}