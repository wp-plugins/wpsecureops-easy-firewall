<?PHP
defined('ABSPATH') or exit;

global $WPSecureOps;
if (!isset($WPSecureOps)) {
    $WPSecureOps = array();
}

$pluginId = "wpsecureops_easy_firewall";

$WPSecureOps[$pluginId] = array(
    "title"   => "WPSecureOps Easy Firewall",
    "id"      => $pluginId,
    "version" => "1.4",
);

return $pluginId;
