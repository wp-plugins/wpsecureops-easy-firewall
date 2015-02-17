<?PHP
defined('ABSPATH') or exit;

global $WPSecureOps;
if (!isset($WPSecureOps)) {
    $WPSecureOps = [];
}

$pluginId = "wpsecureops_easy_firewall";

$WPSecureOps[$pluginId] = [
    "title"   => "WPSecureOps Easy Firewall",
    "id"      => $pluginId,
    "version" => "1.2",
];

return $pluginId;
