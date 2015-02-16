<?php

/*
Plugin Name: WPSecureOps Easy Firewall
Plugin URI: http://wpsecureops.com/
Description: Simple to use and free security firewall which does not require any coding skills! Simply enable/disable features in the settings page.
Version: 1.1
Author: WPSecureOps
Author URI: http://wpsecureops.com/
License: GPLv2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
*/

require_once "plugin_info.php";
require_once "settings.php";

function wpsecureops_easy_firewall___get_cipher()
{
    require_once 'vendor/phpseclib/AES.php';

    static $cipher = false;
    if (!$cipher) {
        $cipher = new NoConflict_Crypt_AES(CRYPT_AES_MODE_ECB);
        $cipher->setKey(NONCE_SALT . AUTH_SALT);
    }

    return $cipher;
}
function wpsecureops_easy_firewall_obj_encrypt($obj)
{
    return base64_encode(wpsecureops_easy_firewall___get_cipher()->encrypt(serialize($obj)));
}
function wpsecureops_easy_firewall_obj_decrypt($s)
{
    return unserialize(wpsecureops_easy_firewall___get_cipher()->decrypt(base64_decode($s, true)));
}

function wpsecureops_easy_firewall__repeat_with_prefix($prefix, $ar, $suffix)
{
    $s = "";
    foreach ($ar as $v) {
        if (!empty($v)) {
            $s .= $prefix . $v . $suffix;
        }
    }

    return $s;
}
add_action('mod_rewrite_rules', 'wpsecureops_easy_firewall__gen_wp_rules');
function wpsecureops_easy_firewall__gen_wp_rules($old_rules)
{
    $opts = wpsecureops_easy_firewall_get_all_options();

    $rules = "";
    /* rewrite rules **/

    if (isset($opts["xmlrpcphp"]) && $opts["xmlrpcphp"] === "1") {
        $rules .= '
<files xmlrpc.php>
	order allow,deny
	deny from all
</files>
';
    }

    if (isset($opts["trackbackdisable"]) && $opts["trackbackdisable"] === "1") {
        $rules .= '
<files wp-trackback.php>
	order allow,deny
	deny from all
</files>
';
    }

    if (isset($opts["wpcommentspost"]) && $opts["wpcommentspost"] === "1") {
        $rules .= '
<files wp-comments-post.php>
	order allow,deny
	deny from all
</files>
';
    }

    if (isset($opts["denyhtfiles"]) && $opts["denyhtfiles"] === "1") {
        $rules .= '
<files ~ "^.*\.([Hh][Tt][Aa])">
	order allow,deny
	deny from all
	satisfy all
</files>
';
    }

    if (isset($opts["blockincludeonly"]) && $opts["blockincludeonly"] === "1") {
        $rules .= '
<IfModule mod_rewrite.c>
	RewriteEngine On
	RewriteBase /
	RewriteRule ^wp-admin/includes/ - [F,L]
	RewriteRule !^wp-includes/ - [S=3]
	RewriteRule ^wp-includes/[^/]+\.php$ - [F,L]
	RewriteRule ^wp-includes/js/tinymce/langs/.+\.php - [F,L]
	RewriteRule ^wp-includes/theme-compat/ - [F,L]
</IfModule>
';
    }

    if (isset($opts["disabledirlisting"]) && $opts["disabledirlisting"] === "1") {
        $rules .= '
# disable directory browsing
Options All -Indexes
';
    }

    if (isset($opts["disablehotlinking"]) && $opts["disablehotlinking"] === "1") {
        $rules .= '
RewriteEngine on
RewriteCond %{HTTP_REFERER} !^$
RewriteCond %{HTTP_REFERER} !^http(s)?://(www\.)?' . str_replace('www.', '', $_SERVER['HTTP_HOST']) . ' [NC]
RewriteRule \.(jpg|jpeg|png|gif)$ - [NC,F,L]
';
    }

    if (isset($opts["banips"]) && count($opts["banips"]) > 0 && !empty($opts["banips"][0])) {
        $rules .= '
order allow,deny
' . wpsecureops_easy_firewall__repeat_with_prefix("deny from ", $opts["banips"], "\n") . '
allow from all
';
    }

    if (isset($opts["blockbots"]) && count($opts["blockbots"]) > 0 && !empty($opts["blockbots"][0])) {
        $rules .= '
<IfModule mod_rewrite.c>
	RewriteEngine On
	RewriteCond %{HTTP_USER_AGENT} ^.*(' . implode("|", $opts["blockbots"]) . ').*$ [NC]
	RewriteRule .* - [F,L]
</IfModule>
';
    }
    /* end of rewrite rules **/
    return $rules . "\n" . $old_rules;
}

function wpsecureops_easy_firewall__ftp_forms($s = false)
{
    static $forms = [];

    if (isset($s) && $s) {
        $forms[] = $s;
    } else {
        return implode("\n", $forms);
    }
}

function wpsecureops_easy_firewall__file_put_contents($fullpath, $contents)
{
    static $form_shown = false;

    $wpsecureops_easy_firewall_options = wpsecureops_easy_firewall_get_all_options();

    $upload_dir = basename($fullpath);

    if (!file_exists($fullpath) || $contents !== file_get_contents($fullpath)) {
        if (!is_writable($fullpath)) {
            $url   = "options.php";
            $nonce = wp_create_nonce("wpsecureops-easy-firewall-settings-group-options");
            $_POST = array_merge($_POST,
            [
                "_wpnonce"         => $nonce,
                "option_page"      => "wpsecureops-easy-firewall-settings-group",
                "action"           => "update",
                "submit"           => "Save changes",
                "_wp_http_referer" => admin_url("/options-general.php?page=wpsecureops-easy-firewall/settings.php"),
            ]);
            foreach ($wpsecureops_easy_firewall_options as $k => $v) {
                if (!is_array($v)) {
                    $_POST[ "wpsecureops_easy_firewall_" . $k . "" ] = $v;
                } else {
                    $_POST[ "wpsecureops_easy_firewall_" . $k . "" ] = implode("\n", $v);
                }
            }

            $creds = get_transient($trans = "wpsecureops-easy-firewall-creds");
            if ($creds) {
                $creds = wpsecureops_easy_firewall_obj_decrypt($creds);
            }

            ob_start();
            if (!$creds && false === (request_filesystem_credentials($url, '', false, $upload_dir, array_keys($_POST)))) {
                $c = ob_get_contents();
                ob_end_clean();

                if (!$form_shown) {
                    add_settings_error(
                        'general',
                        'error', sprintf(
                            __("
									Error: " . $upload_dir . " is not writable, Password protected areas is disabled!<br/>
									To be able to enable it (without needing to make " . $upload_dir . " writable), fill in your FTP details:
								", 'wpsecureops_easy_firewall'
                            )
                        ),
                        'error'
                    );

                    wpsecureops_easy_firewall__ftp_forms($c);

                    $form_shown = true;
                }

                return;
            } else {
                if (!$fs = WP_Filesystem($creds)) {
                    ob_start();
                    // our credentials were no good, ask the user for them again
                    request_filesystem_credentials($url, '', true, $upload_dir, array_keys($_POST));

                    $c = ob_get_contents();
                    ob_end_clean();

                    wpsecureops_easy_firewall__ftp_forms($c);
                    $form_shown = true;

                    return;
                } else {
                    global $wp_filesystem;
                    $wp_filesystem->put_contents($fullpath, $contents);
                }
            }
        } else {
            file_put_contents($fullpath, $contents);
        }
    }
}
function wpsecureops_easy_firewall__crypt_apr1_md5($plainpasswd)
{
    $salt = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz0123456789"), 0, 8);
    $len  = strlen($plainpasswd);
    $text = $plainpasswd . '$apr1$' . $salt;
    $bin  = pack("H32", md5($plainpasswd . $salt . $plainpasswd));
    $tmp  = "";
    for ($i = $len; $i > 0; $i -= 16) {
        $text .= substr($bin, 0, min(16, $i));
    }
    for ($i = $len; $i > 0; $i >>= 1) {
        $text .= ($i & 1) ? chr(0) : $plainpasswd{0};
    }
    $bin = pack("H32", md5($text));
    for ($i = 0; $i < 1000; $i++) {
        $new = ($i & 1) ? $plainpasswd : $bin;

        if ($i % 3) {
            $new .= $salt;
        }

        if ($i % 7) {
            $new .= $plainpasswd;
        }
        $new .= ($i & 1) ? $bin : $plainpasswd;
        $bin = pack("H32", md5($new));
    }
    for ($i = 0; $i < 5; $i++) {
        $k = $i + 6;
        $j = $i + 12;

        if ($j === 16) {
            $j = 5;
        }
        $tmp             = $bin[$i] . $bin[$k] . $bin[$j] . $tmp;
    }
    $tmp = chr(0) . chr(0) . $bin[11] . $tmp;
    $tmp = strtr(strrev(substr(base64_encode($tmp), 2)), "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/", "./0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz");

    return "$" . "apr1" . "$" . $salt . "$" . $tmp;
}
function wpsecureops_easy_firewall__flush_rules()
{
    $opts = wpsecureops_easy_firewall_get_all_options();

    if (strpos($opts['protected_password'], "!") === false) {

        // password!
        $opts['protected_password'] = "!" . wpsecureops_easy_firewall__crypt_apr1_md5($opts['protected_password']);
        wpsecureops_easy_firewall_set_option_protected_password($opts['protected_password']);
    }

    if (!empty($opts['protected_user']) && !empty($opts['protected_password']) && $opts["passwordProtected"] === "1") {
        $upload_dir       = WP_PLUGIN_DIR . "/" . basename(__DIR__) . "/";
        $htpasswd         = $upload_dir . "/.htpasswd";
        $htpasswdContents = $opts['protected_user'] . ":" . str_replace("!", "", $opts['protected_password']);
        wpsecureops_easy_firewall__file_put_contents($htpasswd, $htpasswdContents);
    }
    /* rewrite file rules **/

    if (isset($opts["passwordProtected"]) && $opts["passwordProtected"] === "1" && !empty($opts['protected_user']) && !empty($opts['protected_password'])) {
        if (file_exists(__DIR__ . "/" . '.htpasswd')) {
            $rules = "";
            $rules .= '
AuthType Basic
# this text is displayed in the login dialog
AuthName "WPSecureOps Easy Firewall Restricted Area"
# The absolute path of the Apache htpasswd file. You should edit this
AuthUserFile ' . __DIR__ . '/.htpasswd
# Allows any user in the .htpasswd file to access the directory
require valid-user
Order allow,deny
Allow from all
<Files admin-ajax.php>
    Order allow,deny
    Allow from all
    Satisfy any
</Files>
';
            wpsecureops_easy_firewall__file_put_contents('' . ABSPATH . '/wp-admin/.htaccess', $rules);
        } else {
            wpsecureops_easy_firewall__file_put_contents('' . ABSPATH . '/wp-admin/.htaccess', "# wpsecureops easy firewall disabled");
        }
    } else {
        wpsecureops_easy_firewall__file_put_contents('' . ABSPATH . '/wp-admin/.htaccess', "# wpsecureops easy firewall disabled");
    }
    /* end of rewrite file rules **/
    flush_rewrite_rules(true);
}

function wpsecureops_easy_firewall_opt_updated($option_name, $old_value, $new_value)
{
    if (strpos($option_name, "wpsecureops_easy_firewall") !== -1 && isset($_POST['hostname'])) {
        set_transient('wpsecureops-easy-firewall-creds', wpsecureops_easy_firewall_obj_encrypt($_POST), 120);
    }

    return;
}
add_action("updated_option", 'wpsecureops_easy_firewall_opt_updated', 99, 3);

add_action("wpsecureops-easy-firewall/settings-updated", "wpsecureops_easy_firewall__flush_rules");

function wpsecureops_easy_firewall__form_before()
{
    $out = wpsecureops_easy_firewall__ftp_forms();
    if (!empty($out)) {
        echo $out;

        return false;
    }
}
