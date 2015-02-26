<?PHP
defined('ABSPATH') or exit;

global $WPSecureOps;
if (!isset($WPSecureOps)) {
    $WPSecureOps = array();
}

$pluginId = "wpsecureops_easy_firewall";

$WPSecureOps[$pluginId] = [
    "title"   => "WPSecureOps Easy Firewall",
    "id"      => $pluginId,
    "version" => "1.3",
];

return $pluginId;
