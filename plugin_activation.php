<?PHP

defined('ABSPATH') or exit;

$plugin_file = realpath(dirname(__FILE__) . "/wpsecureops-easy-firewall.php");

function wpsecureops_easy_firewall_plugin_activation()
{
    set_transient("wpsecureops-easy-firewall-activated", 1, 60);
}

register_activation_hook($plugin_file, 'wpsecureops_easy_firewall_plugin_activation');

function wpsecureops_easy_firewall_plugin_act_notice()
{
    if (get_transient($k = "wpsecureops-easy-firewall-activated")) {
        delete_transient($k);

        $html = '<div class="updated">';
        $html .= '<p>';
        $html .= __('To configure WPSecureOps Easy Firewall please go to <a href="options-general.php?page=wpsecureops-easy-firewall%2Fsettings.php">this page</a>.', 'wpsecureops-easy-firewall');
        $html .= '</p>';
        $html .= '</div>';

        echo $html;
    }
}
add_action('admin_notices', 'wpsecureops_easy_firewall_plugin_act_notice');

function wpsecureops_easy_firewall_plugin_deactivation()
{
    if (wpsecureops_easy_firewall_get_passwordProtected() === "1") {
        wpsecureops_easy_firewall_set_option_passwordProtected("0");
        wpsecureops_easy_firewall__flush_rules();
    }
}

register_deactivation_hook($plugin_file, 'wpsecureops_easy_firewall_plugin_deactivation');
