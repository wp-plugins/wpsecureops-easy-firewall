<?PHP
global $WPSecureOps;
if (!isset($WPSecureOps)) {
    $WPSecureOps = [];
}

$pluginId = "wpsecureops_easy_firewall";

$WPSecureOps[$pluginId] = [
    "title"       => "WPSecureOps Easy Firewall",
    "id"          => $pluginId,
    "version"     => /* version **/ "1.0" /* end of version */,
    "plugin_url"  => /* plugin url **/ "http://wpsecureops.com/" /* end of plugin url */,
    "github_url"  => /* github url **/ "http://wpsecureops.com/" /* end of github url */,
    "fb_url"      => /* fb url **/ "http://wpsecureops.com/" /* end of fb url */,
    "twitter_url" => /* twitter url **/ "http://wpsecureops.com/" /* end of twitter url */,
];

return $pluginId;
