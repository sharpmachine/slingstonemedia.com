function wafp_toggle_new_form() {
    jQuery('.wafp-new-link').toggle();
    jQuery('.wafp-display-new-form').toggle();
}

function wafp_delete_link(link_id, msg) {
    if (confirm(msg)) {
        jQuery.ajax({
            type: "POST",
            url: "index.php",
            data: "plugin=wafp&controller=links&action=delete&lid=" + link_id,
            success: function (html) {
                jQuery('#wafp-link-' + link_id).fadeOut();
            }
        });
    }
}

function wafp_view_admin_affiliate_page(action, period, wafpage) {
    jQuery('.wafp-stats-loader').show();
    jQuery.ajax({
        type: "POST",
        url: "index.php",
        data: "plugin=wafp&controller=reports&action=" + action + "&period=" + period + "&wafpage=" + wafpage,
        success: function (html) {
            jQuery("#tooltip").remove(); // clear out the tooltip
            jQuery('#wafp-admin-affiliate-panel').replaceWith(html);
            jQuery('.wafp-stats-loader').hide();
        }
    });
}

function wafp_view_dashboard_affiliate_page(url, action, period, wafpage) {
    jQuery('.wafp-stats-loader').show();
    jQuery.ajax({
        type: "POST",
        url: url,
        data: "plugin=wafp&controller=dashboard&action=" + action + "&period=" + period + "&wafpage=" + wafpage,
        success: function (html) {
            jQuery("#tooltip").remove(); // clear out the tooltip
            jQuery('#wafp-dash-affiliate-panel').replaceWith(html);
            jQuery('.wafp-stats-loader').hide();
        }
    });
}

function wafp_integration_toggle() {
  jQuery('.wafp-integration-option').hide();
}

jQuery(document).ready(function(){
  jQuery('.wafp-show-integration-option').click( function() {
    var integration = jQuery('.wafp-integration-dropdown').val();
    jQuery('.wafp-' + integration + '-option').slideToggle();
  });
});