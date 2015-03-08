<?PHP
defined('ABSPATH') or exit;

function wpsecureops_easy_firewall_normalize_line_endings($s)
{
    // Convert all line-endings to UNIX format
    $s = str_replace(array("\r\n", "\r"), "\n", $s);

    return $s;
}

// create custom plugin settings menu
add_action('admin_menu', 'wpsecureops_easy_firewall_create_menu');

function wpsecureops_easy_firewall_create_menu()
{

    //create new top-level menu
        add_submenu_page('options-general.php', 'WPSecureOps Easy Firewall', 'WPSecureOps Easy Firewall', 'administrator', __FILE__, 'wpsecureops_easy_firewall_settings_page');

    //call register settings function
    add_action('admin_init', 'wpsecureops_easy_firewall_register_settings');
    add_action('admin_init', 'wpsecureops_easy_firewall_is_save_triggered');
}

function wpsecureops_easy_firewall_register_settings()
{
    register_setting('wpsecureops-easy-firewall-settings-group', 'wpsecureops_easy_firewall_protected_user');
    register_setting('wpsecureops-easy-firewall-settings-group', 'wpsecureops_easy_firewall_protected_password');
    register_setting('wpsecureops-easy-firewall-settings-group', 'wpsecureops_easy_firewall_passwordProtected');
    register_setting('wpsecureops-easy-firewall-settings-group', 'wpsecureops_easy_firewall_xmlrpcphp');
    register_setting('wpsecureops-easy-firewall-settings-group', 'wpsecureops_easy_firewall_trackbackdisable');
    register_setting('wpsecureops-easy-firewall-settings-group', 'wpsecureops_easy_firewall_wpcommentspost');
    register_setting('wpsecureops-easy-firewall-settings-group', 'wpsecureops_easy_firewall_denyhtfiles');
    register_setting('wpsecureops-easy-firewall-settings-group', 'wpsecureops_easy_firewall_blockincludeonly');
    register_setting('wpsecureops-easy-firewall-settings-group', 'wpsecureops_easy_firewall_disabledirlisting');
    register_setting('wpsecureops-easy-firewall-settings-group', 'wpsecureops_easy_firewall_disablehotlinking');
    register_setting('wpsecureops-easy-firewall-settings-group', 'wpsecureops_easy_firewall_banips');
    register_setting('wpsecureops-easy-firewall-settings-group', 'wpsecureops_easy_firewall_blockbots');
}

function wpsecureops_easy_firewall_is_save_triggered()
{
    //	if(isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true' && isset($_GET['page']) && $_GET['page'] == "wpsecureops-easy-firewall/".basename(__FILE__)) {
//		do_action("wpsecureops-easy-firewall/settings-updated");
//	}
    if (get_transient("wpsecureops-easy-firewall-settings-updated")) {
        do_action("wpsecureops-easy-firewall/settings-updated");
    }
}

add_filter("wpsecureops-easy-firewall/form-before", "wpsecureops_easy_firewall__form_before");

function wpsecureops_easy_firewall__whitelist_detect_save()
{
    if (
        isset($_REQUEST['option_page']) &&
        $_REQUEST['option_page'] === "wpsecureops-easy-firewall-settings-group" &&
        isset($_REQUEST['action']) &&
        $_REQUEST['action'] === "update"
    ) {
        set_transient("wpsecureops-easy-firewall-settings-updated", 1, 360);
        do_action("wpsecureops-easy-firewall/settings-updated");
    }
}

add_filter('whitelist_options', 'wpsecureops_easy_firewall__whitelist_detect_save');

function wpsecureops_easy_firewall_get_protected_user()
{
    $v = get_option('wpsecureops_easy_firewall_protected_user');
    if (!$v) {
        $v = 'wpsecureops';
    }

    return $v;
}
function wpsecureops_easy_firewall_get_protected_password()
{
    $v = get_option('wpsecureops_easy_firewall_protected_password');
    if (!$v) {
        $v = null;
    }

    return $v;
}
function wpsecureops_easy_firewall_get_passwordProtected()
{
    $v = get_option('wpsecureops_easy_firewall_passwordProtected');
    if (!$v) {
        $v = false;
    }

    return $v;
}
function wpsecureops_easy_firewall_get_xmlrpcphp()
{
    $v = get_option('wpsecureops_easy_firewall_xmlrpcphp');
    if (!$v) {
        $v = false;
    }

    return $v;
}
function wpsecureops_easy_firewall_get_trackbackdisable()
{
    $v = get_option('wpsecureops_easy_firewall_trackbackdisable');
    if (!$v) {
        $v = false;
    }

    return $v;
}
function wpsecureops_easy_firewall_get_wpcommentspost()
{
    $v = get_option('wpsecureops_easy_firewall_wpcommentspost');
    if (!$v) {
        $v = false;
    }

    return $v;
}
function wpsecureops_easy_firewall_get_denyhtfiles()
{
    $v = get_option('wpsecureops_easy_firewall_denyhtfiles');
    if (!$v) {
        $v = true;
    }

    return $v;
}
function wpsecureops_easy_firewall_get_blockincludeonly()
{
    $v = get_option('wpsecureops_easy_firewall_blockincludeonly');
    if (!$v) {
        $v = true;
    }

    return $v;
}
function wpsecureops_easy_firewall_get_disabledirlisting()
{
    $v = get_option('wpsecureops_easy_firewall_disabledirlisting');
    if (!$v) {
        $v = true;
    }

    return $v;
}
function wpsecureops_easy_firewall_get_disablehotlinking()
{
    $v = get_option('wpsecureops_easy_firewall_disablehotlinking');
    if (!$v) {
        $v = false;
    }

    return $v;
}
function wpsecureops_easy_firewall_get_banips()
{
    $v = get_option('wpsecureops_easy_firewall_banips');
    if (!$v) {
        $v = false;
    }

    return explode("\n", wpsecureops_easy_firewall_normalize_line_endings($v));
}
function wpsecureops_easy_firewall_get_blockbots()
{
    $v = get_option('wpsecureops_easy_firewall_blockbots');
    if (!$v) {
        $v = false;
    }

    return explode("\n", wpsecureops_easy_firewall_normalize_line_endings($v));
}

function wpsecureops_easy_firewall_set_option_protected_user($v)
{
    update_option('wpsecureops_easy_firewall_protected_user', $v);
}
function wpsecureops_easy_firewall_set_option_protected_password($v)
{
    update_option('wpsecureops_easy_firewall_protected_password', $v);
}
function wpsecureops_easy_firewall_set_option_passwordProtected($v)
{
    update_option('wpsecureops_easy_firewall_passwordProtected', $v);
}
function wpsecureops_easy_firewall_set_option_xmlrpcphp($v)
{
    update_option('wpsecureops_easy_firewall_xmlrpcphp', $v);
}
function wpsecureops_easy_firewall_set_option_trackbackdisable($v)
{
    update_option('wpsecureops_easy_firewall_trackbackdisable', $v);
}
function wpsecureops_easy_firewall_set_option_wpcommentspost($v)
{
    update_option('wpsecureops_easy_firewall_wpcommentspost', $v);
}
function wpsecureops_easy_firewall_set_option_denyhtfiles($v)
{
    update_option('wpsecureops_easy_firewall_denyhtfiles', $v);
}
function wpsecureops_easy_firewall_set_option_blockincludeonly($v)
{
    update_option('wpsecureops_easy_firewall_blockincludeonly', $v);
}
function wpsecureops_easy_firewall_set_option_disabledirlisting($v)
{
    update_option('wpsecureops_easy_firewall_disabledirlisting', $v);
}
function wpsecureops_easy_firewall_set_option_disablehotlinking($v)
{
    update_option('wpsecureops_easy_firewall_disablehotlinking', $v);
}
function wpsecureops_easy_firewall_set_option_banips($v)
{
    update_option('wpsecureops_easy_firewall_banips', wpsecureops_easy_firewall_normalize_line_endings($v));
}
function wpsecureops_easy_firewall_set_option_blockbots($v)
{
    update_option('wpsecureops_easy_firewall_blockbots', wpsecureops_easy_firewall_normalize_line_endings($v));
}

function wpsecureops_easy_firewall_get_all_options()
{
    $r                           = array();
    $r['protected_user']     = wpsecureops_easy_firewall_get_protected_user();
    $r['protected_password'] = wpsecureops_easy_firewall_get_protected_password();
    $r['passwordProtected']  = wpsecureops_easy_firewall_get_passwordProtected();
    $r['xmlrpcphp']          = wpsecureops_easy_firewall_get_xmlrpcphp();
    $r['trackbackdisable']   = wpsecureops_easy_firewall_get_trackbackdisable();
    $r['wpcommentspost']     = wpsecureops_easy_firewall_get_wpcommentspost();
    $r['denyhtfiles']        = wpsecureops_easy_firewall_get_denyhtfiles();
    $r['blockincludeonly']   = wpsecureops_easy_firewall_get_blockincludeonly();
    $r['disabledirlisting']  = wpsecureops_easy_firewall_get_disabledirlisting();
    $r['disablehotlinking']  = wpsecureops_easy_firewall_get_disablehotlinking();
    $r['banips']             = wpsecureops_easy_firewall_get_banips();
    $r['blockbots']          = wpsecureops_easy_firewall_get_blockbots();

    return $r;
}

function wpsecureops_easy_firewall_settings_page()
{
    ?>
	<div class="wrap">
		<h2>WPSecureOps Easy Firewall Settings</h2>

		<?php if (apply_filters("wpsecureops-easy-firewall/form-before", null) !== false) {
    ?>
		<form method="post" action="options.php">
			<?php settings_fields('wpsecureops-easy-firewall-settings-group');
    ?>
			<?php do_settings_sections('wpsecureops-easy-firewall-settings-group');
    ?>
			<table class="form-table">
								<tr valign="top">
					<th scope="row"><?php echo __('Username', 'wpsecureops-easy-firewall') ?></th>
											<td>
							<input type="text" name="wpsecureops_easy_firewall_protected_user" value="<?php echo esc_attr(wpsecureops_easy_firewall_get_protected_user());
    ?>" />
							<div class="description">ALWAYS use different username for protected areas then the ones in WordPress!</div>
						</td>
									</tr>
								<tr valign="top">
					<th scope="row"><?php echo __('Password', 'wpsecureops-easy-firewall') ?></th>
											<td>
							<input type="password" name="wpsecureops_easy_firewall_protected_password" value="<?php echo esc_attr(wpsecureops_easy_firewall_get_protected_password());
    ?>" />
							<div class="description">ALWAYS use different password for protected areas then the ones in WordPress!</div>
						</td>
									</tr>
								<tr valign="top">
					<th scope="row"><?php echo __('Password protection for /wp-admin/', 'wpsecureops-easy-firewall') ?></th>
											<td>
							<input type="checkbox" name="wpsecureops_easy_firewall_passwordProtected" value="1" <?php echo wpsecureops_easy_firewall_get_passwordProtected() === "1" ? " checked=1" : "";
    ?>" />

							<div class="description">Will require additional credentials for /wp-admin/</div>
						</td>
									</tr>
								<tr valign="top">
					<th scope="row"><?php echo __('Disable XML-RPC', 'wpsecureops-easy-firewall') ?></th>
											<td>
							<input type="checkbox" name="wpsecureops_easy_firewall_xmlrpcphp" value="1" <?php echo wpsecureops_easy_firewall_get_xmlrpcphp() === "1" ? " checked=1" : "";
    ?>" />

							<div class="description">Disallow ANY access to XML-RPC. This feature may block some pingbacks and remote clients which require the xml-rpc to be enabled.</div>
						</td>
									</tr>
								<tr valign="top">
					<th scope="row"><?php echo __('Disable wp-trackback.php', 'wpsecureops-easy-firewall') ?></th>
											<td>
							<input type="checkbox" name="wpsecureops_easy_firewall_trackbackdisable" value="1" <?php echo wpsecureops_easy_firewall_get_trackbackdisable() === "1" ? " checked=1" : "";
    ?>" />

							<div class="description">Disallow ANY access to wp-trackback.php. This feature will disable ANY trackbacks from 3rd party sites. Mostly used on WP sites which does not have comments enabled.</div>
						</td>
									</tr>
								<tr valign="top">
					<th scope="row"><?php echo __('Disable ANY commenting in the site (wp-comments-post.php)', 'wpsecureops-easy-firewall') ?></th>
											<td>
							<input type="checkbox" name="wpsecureops_easy_firewall_wpcommentspost" value="1" <?php echo wpsecureops_easy_firewall_get_wpcommentspost() === "1" ? " checked=1" : "";
    ?>" />

							<div class="description">Disallow ANY access to wp-comments-post.php. This feature will disable ANY comments from users and robots. Mostly used on WP sites which does not have comments shown in the UI.</div>
						</td>
									</tr>
								<tr valign="top">
					<th scope="row"><?php echo __('Deny access to .htacces and .htpasswd files', 'wpsecureops-easy-firewall') ?></th>
											<td>
							<input type="checkbox" name="wpsecureops_easy_firewall_denyhtfiles" value="1" <?php echo wpsecureops_easy_firewall_get_denyhtfiles() === "1" ? " checked=1" : "";
    ?>" />

							<div class="description"></div>
						</td>
									</tr>
								<tr valign="top">
					<th scope="row"><?php echo __('Block include only files', 'wpsecureops-easy-firewall') ?></th>
											<td>
							<input type="checkbox" name="wpsecureops_easy_firewall_blockincludeonly" value="1" <?php echo wpsecureops_easy_firewall_get_blockincludeonly() === "1" ? " checked=1" : "";
    ?>" />

							<div class="description">Block access to any of WordPress internal files, which are not meant to be accessible via URLs.</div>
						</td>
									</tr>
								<tr valign="top">
					<th scope="row"><?php echo __('Disable directory listing', 'wpsecureops-easy-firewall') ?></th>
											<td>
							<input type="checkbox" name="wpsecureops_easy_firewall_disabledirlisting" value="1" <?php echo wpsecureops_easy_firewall_get_disabledirlisting() === "1" ? " checked=1" : "";
    ?>" />

							<div class="description">In case you don&quot;t know or need the directory listing, leave this as checked.</div>
						</td>
									</tr>
								<tr valign="top">
					<th scope="row"><?php echo __('Disable hotlinking', 'wpsecureops-easy-firewall') ?></th>
											<td>
							<input type="checkbox" name="wpsecureops_easy_firewall_disablehotlinking" value="1" <?php echo wpsecureops_easy_firewall_get_disablehotlinking() === "1" ? " checked=1" : "";
    ?>" />

							<div class="description">Stop anyone from including/loading images from your site</div>
						</td>
									</tr>
								<tr valign="top">
					<th scope="row"><?php echo __('Ban users from accessing the site', 'wpsecureops-easy-firewall') ?></th>
										<td>
						<textarea name="wpsecureops_easy_firewall_banips" class="wpso_multi_text"><?php echo implode("\n", wpsecureops_easy_firewall_get_banips());
    ?></textarea>
						<div class="description">Enter IPs (or subnets 123.123.123.) which should be banned from visiting the site.</div>
					</td>
									</tr>
								<tr valign="top">
					<th scope="row"><?php echo __('Ban bots from accessing the site', 'wpsecureops-easy-firewall') ?></th>
										<td>
						<textarea name="wpsecureops_easy_firewall_blockbots" class="wpso_multi_text"><?php echo implode("\n", wpsecureops_easy_firewall_get_blockbots());
    ?></textarea>
						<div class="description">Enter parts of the bot&#039;s user agents</div>
					</td>
									</tr>
							</table>

			<?php submit_button();
    ?>

		</form>
		<?php 
}
    ?>
		<?php apply_filters("wpsecureops-easy-firewall/form-after", null);
    ?>
	</div>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			$('.wpso_multi_text').each(function() {
				var $textarea = $(this);
				var $container = $("<div/>");

				var changed = function() {
					var v = "";
					$('input', $container).each(function() {
						if($(this).val()) {
							v += $(this).val() + "\n";
						}
					});

					$textarea.val(v.replace(/\n$/mi, ""));
				};

				var addRow = function(v) {
					var $wrapper = $("<div/>");

					var $input = $('<input type="text" />');
					if(v) {
						$input.val(v);
					}

					var $removeButton = $('<a href="javascript:;">Remove</a>');
					$removeButton.bind('click', function() {
						$wrapper.remove();
						changed();
					});

					$input.bind('blur change', changed);


					$wrapper.append($input);
					$wrapper.append($removeButton);

					$container.append($wrapper);
				};

				$($(this).val().split("\n")).each(function(k, v) {
					addRow(v);
				});

				$textarea.hide();
				$textarea.data('wpso_multi_text_container', $container);

				var $addButton = $('<a href="javascript:;">Add more</a>');
				$addButton.bind('click', function() {
					addRow();
				});

				$container.insertAfter($textarea);
				$addButton.insertAfter($container);
			})
		});
	</script>
<?php

}
