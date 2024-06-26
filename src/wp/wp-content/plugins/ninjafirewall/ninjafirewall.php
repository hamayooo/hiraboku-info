<?php
/*
Plugin Name: NinjaFirewall (WP Edition)
Plugin URI: https://nintechnet.com/
Description: A true Web Application Firewall to protect and secure WordPress.
Version: 3.6.8
Author: The Ninja Technologies Network
Author URI: https://nintechnet.com/
License: GPLv3 or later
Network: true
Text Domain: ninjafirewall
Domain Path: /languages
*/

/*
 +---------------------------------------------------------------------+
 | NinjaFirewall (WP Edition)                                          |
 |                                                                     |
 | (c) NinTechNet - https://nintechnet.com/                            |
 +---------------------------------------------------------------------+
*/
define( 'NFW_ENGINE_VERSION', '3.6.8' );
/*
 +---------------------------------------------------------------------+
 | This program is free software: you can redistribute it and/or       |
 | modify it under the terms of the GNU General Public License as      |
 | published by the Free Software Foundation, either version 3 of      |
 | the License, or (at your option) any later version.                 |
 |                                                                     |
 | This program is distributed in the hope that it will be useful,     |
 | but WITHOUT ANY WARRANTY; without even the implied warranty of      |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the       |
 | GNU General Public License for more details.                        |
 +---------------------------------------------------------------------+
*/

if (! defined( 'ABSPATH' ) ) { die( 'Forbidden' ); }

/* ------------------------------------------------------------------ */

// Load (force) our translation files.
$nf_locale = array( 'fr_FR' );
$this_locale = get_locale();
if ( in_array( $this_locale, $nf_locale ) ) {
	if ( file_exists( __DIR__ . "/languages/ninjafirewall-{$this_locale}.mo" ) ) {
		unload_textdomain( 'ninjafirewall' );
		load_textdomain( 'ninjafirewall', __DIR__ . "/languages/ninjafirewall-{$this_locale}.mo" );
	}
}
/* ------------------------------------------------------------------ */

$null = __('A true Web Application Firewall to protect and secure WordPress.', 'ninjafirewall');
define('NFW_NULL_BYTE', 2);
define('NFW_SCAN_BOTS', 531);
define('NFW_ASCII_CTRL', 500);
define('NFW_DOC_ROOT', 510);
define('NFW_WRAPPERS', 520);
define('NFW_OBJECTS', 525);
define('NFW_LOOPBACK', 540);
$err_fw = array(
	1	=> __('Cannot find WordPress configuration file', 'ninjafirewall'),
	2	=>	__('Cannot read WordPress configuration file', 'ninjafirewall'),
	3	=>	__('Cannot retrieve WordPress database credentials', 'ninjafirewall'),
	4	=>	__('Cannot connect to WordPress database', 'ninjafirewall'),
	5	=>	__('Cannot retrieve user options from database (#2)', 'ninjafirewall'),
	6	=>	__('Cannot retrieve user options from database (#3)', 'ninjafirewall'),
	7	=>	__('Cannot retrieve user rules from database (#2)', 'ninjafirewall'),
	8	=>	__('Cannot retrieve user rules from database (#3)', 'ninjafirewall'),
	9	=>	__('The firewall has been disabled from the <a href="admin.php?page=nfsubopt">administration console</a>', 'ninjafirewall'),
	10	=> __('Unable to communicate with the firewall. Please check your settings', 'ninjafirewall'),
	11	=>	__('Cannot retrieve user options from database (#1)', 'ninjafirewall'),
	12	=>	__('Cannot retrieve user rules from database (#1)', 'ninjafirewall'),
	13 => sprintf( __("The firewall cannot access its log and cache folders. If you changed the name of WordPress %s or %s folders, you must define NinjaFirewall's built-in %s constant (see %s for more info)", 'ninjafirewall'), '<code>/wp-content/</code>', '<code>/plugins/</code>', '<code>NFW_LOG_DIR</code>', "<a href='https://nintechnet.com/ninjafirewall/wp-edition/help/?htninja' target='_blank'>Path to NinjaFirewall's log and cache directory</a>"),
);

if (! defined('NFW_LOG_DIR') ) {
	define('NFW_LOG_DIR', WP_CONTENT_DIR);
}
if (! empty($_SERVER['DOCUMENT_ROOT']) && $_SERVER['DOCUMENT_ROOT'] != '/' ) {
	$_SERVER['DOCUMENT_ROOT'] = rtrim( $_SERVER['DOCUMENT_ROOT'] , '/' );
}
/* ------------------------------------------------------------------ */

require plugin_dir_path(__FILE__) . 'lib/nfw_misc.php';

if (! defined( 'NFW_REMOTE_ADDR') ) {
	nfw_select_ip();
}

add_action( 'nfwgccron', 'nfw_garbage_collector' );

/* ------------------------------------------------------------------ */			//s1:h0

function nfw_activate() {

	// Warn if the user does not have the 'unfiltered_html' capability:
	if (! current_user_can( 'unfiltered_html' ) ) {
		exit( __('You do not have "unfiltered_html" capability. Please enable it in order to run NinjaFirewall (or make sure you do not have "DISALLOW_UNFILTERED_HTML" in your wp-config.php script).', 'ninjafirewall'));
	}

	nf_not_allowed( 'block', __LINE__ );

	global $wp_version;
	if ( version_compare( $wp_version, '3.3', '<' ) ) {
		exit( sprintf( __('NinjaFirewall requires WordPress 3.3 or greater but your current version is %s.', 'ninjafirewall'), $wp_version) );
	}

	if ( version_compare( PHP_VERSION, '5.3.0', '<' ) ) {
		exit( sprintf( __('NinjaFirewall requires PHP 5.3 or greater but your current version is %s.', 'ninjafirewall'), PHP_VERSION) );
	}

	if (! function_exists('mysqli_connect') ) {
		exit( sprintf( __('NinjaFirewall requires the PHP %s extension.', 'ninjafirewall'), '<code>mysqli</code>') );
	}

	if ( ini_get( 'safe_mode' ) ) {
		exit( __('You have SAFE_MODE enabled. Please disable it, it is deprecated as of PHP 5.3.0 (see http://php.net/safe-mode).', 'ninjafirewall'));
	}

	if ( ( is_multisite() ) && (! current_user_can( 'manage_network' ) ) ) {
		exit( __('You are not allowed to activate NinjaFirewall.', 'ninjafirewall') );
	}

	if ( PATH_SEPARATOR == ';' ) {
		exit( __('NinjaFirewall is not compatible with Microsoft Windows.', 'ninjafirewall') );
	}

	if ( $nfw_options = nfw_get_option( 'nfw_options' ) ) {
		$nfw_options['enabled'] = 1;
		nfw_update_option( 'nfw_options', $nfw_options);

		if (! empty($nfw_options['sched_scan']) ) {
			if ($nfw_options['sched_scan'] == 1) {
				$schedtype = 'hourly';
			} elseif ($nfw_options['sched_scan'] == 2) {
				$schedtype = 'twicedaily';
			} else {
				$schedtype = 'daily';
			}
			if ( wp_next_scheduled('nfscanevent') ) {
				wp_clear_scheduled_hook('nfscanevent');
			}
			wp_schedule_event( time() + 3600, $schedtype, 'nfscanevent');
		}
		if (! empty($nfw_options['enable_updates']) ) {
			if ($nfw_options['sched_updates'] == 1) {
				$schedtype = 'hourly';
			} elseif ($nfw_options['sched_updates'] == 2) {
				$schedtype = 'twicedaily';
			} else {
				$schedtype = 'daily';
			}
			if ( wp_next_scheduled('nfsecupdates') ) {
				wp_clear_scheduled_hook('nfsecupdates');
			}
			wp_schedule_event( time() + 15, $schedtype, 'nfsecupdates');
		}
		if (! empty($nfw_options['a_52']) ) {
			if ( wp_next_scheduled('nfdailyreport') ) {
				wp_clear_scheduled_hook('nfdailyreport');
			}
			nfw_get_blogtimezone();
			wp_schedule_event( strtotime( date('Y-m-d 00:00:05', strtotime("+1 day")) ), 'daily', 'nfdailyreport');
		}
		// Re-enable the garbage collector:
		wp_schedule_event( time() + 1800, 'hourly', 'nfwgccron' );

		if ( file_exists( NFW_LOG_DIR . '/nfwlog/cache/bf_conf_off.php' ) ) {
			rename(NFW_LOG_DIR . '/nfwlog/cache/bf_conf_off.php', NFW_LOG_DIR . '/nfwlog/cache/bf_conf.php');
		}
	}
}

register_activation_hook( __FILE__, 'nfw_activate' );

/* ------------------------------------------------------------------ */

function nfw_deactivate() {

	nf_not_allowed( 'block', __LINE__ );

	$nfw_options = nfw_get_option( 'nfw_options' );
	$nfw_options['enabled'] = 0;

	if ( wp_next_scheduled('nfwgccron') ) {
		wp_clear_scheduled_hook('nfwgccron');
	}
	if ( wp_next_scheduled('nfscanevent') ) {
		wp_clear_scheduled_hook('nfscanevent');
	}
	if ( wp_next_scheduled('nfsecupdates') ) {
		wp_clear_scheduled_hook('nfsecupdates');
	}
	if ( wp_next_scheduled('nfdailyreport') ) {
		wp_clear_scheduled_hook('nfdailyreport');
	}
	if ( file_exists( NFW_LOG_DIR . '/nfwlog/cache/bf_conf.php' ) ) {
		rename(NFW_LOG_DIR . '/nfwlog/cache/bf_conf.php', NFW_LOG_DIR . '/nfwlog/cache/bf_conf_off.php');
	}

	nfw_update_option( 'nfw_options', $nfw_options);

}

register_deactivation_hook( __FILE__, 'nfw_deactivate' );

/* ------------------------------------------------------------------ */

function nfw_upgrade() {

	// We must make sure the current PHP session is updated
	// even if for whitelisted non-admin users:
	nfw_session_start();

	if ( nf_not_allowed(0, __LINE__) ) { return; }

	$is_update = 0;

	$nfw_options = nfw_get_option( 'nfw_options' );
	$nfw_rules = nfw_get_option( 'nfw_rules' );

	// Only used for multisite installs running NF < 3.2.2:
	if ( empty($nfw_options['engine_version']) ) {
		$nfw_options = get_option( 'nfw_options' );
		$nfw_rules = get_option( 'nfw_rules' );
	}

	if ( isset($_POST['nf_export']) ) {
		if ( empty($_POST['nfwnonce']) || ! wp_verify_nonce($_POST['nfwnonce'], 'options_save') ) {
			wp_nonce_ays('options_save');
		}
		$nfwbfd_log = NFW_LOG_DIR . '/nfwlog/cache/bf_conf.php';
		if ( file_exists($nfwbfd_log) ) {
			$bd_data = serialize( file_get_contents($nfwbfd_log) );
		} else {
			$bd_data = '';
		}
		$data = serialize($nfw_options) . "\n:-:\n" . serialize($nfw_rules) . "\n:-:\n" . $bd_data;
		header('Content-Type: text/plain');
		header('Content-Length: '. strlen( $data ) );
		header('Content-Disposition: attachment; filename="nfwp.' . NFW_ENGINE_VERSION . '.dat"');
		echo $data;
		exit;
	}

	if ( isset($_POST['dlmods']) ) {
		if ( empty($_POST['nfwnonce']) || ! wp_verify_nonce($_POST['nfwnonce'], 'filecheck_save') ) {
			wp_nonce_ays('filecheck_save');
		}
		if (file_exists(NFW_LOG_DIR . '/nfwlog/cache/nfilecheck_diff.php') ) {
			$download_file = NFW_LOG_DIR . '/nfwlog/cache/nfilecheck_diff.php';
		} elseif (file_exists(NFW_LOG_DIR . '/nfwlog/cache/nfilecheck_diff.php.php') ) {
			$download_file = NFW_LOG_DIR . '/nfwlog/cache/nfilecheck_diff.php.php';
		} else {
			wp_nonce_ays('filecheck_save');
		}
		$stat = stat($download_file);
		$data = '== NinjaFirewall File Check (diff)'. "\n";
		$data.= '== ' . site_url() . "\n";
		$data.= '== ' . date_i18n('M d, Y @ H:i:s O', $stat['ctime']) . "\n\n";
		$data.= '[+] = ' . __('New file', 'ninjafirewall') .
					'      [!] = ' . __('Modified file', 'ninjafirewall') .
					'      [-] = ' . __('Deleted file', 'ninjafirewall') .
					"\n\n";
		$fh = fopen($download_file, 'r');
		while (! feof($fh) ) {
			$res = explode('::', fgets($fh) );
			if ( empty($res[1]) ) { continue; }
			if ($res[1] == 'N') {
				$data .= '[+] ' . $res[0] . "\n";
			} elseif ($res[1] == 'D') {
				$data .= '[-] ' . $res[0] . "\n";
			} elseif ($res[1] == 'M') {
				$data .= '[!] ' . $res[0] . "\n";
			}
		}
		fclose($fh);
		$data .= "\n== EOF\n";

		header('Content-Type: text/plain');
		header('Content-Length: '. strlen( $data ) );
		header('Content-Disposition: attachment; filename="'. $_SERVER['SERVER_NAME'] .'_diff.txt"');
		echo $data;
		exit;
	}

	if ( isset($_POST['dlsnap']) ) {
		if ( empty($_POST['nfwnonce']) || ! wp_verify_nonce($_POST['nfwnonce'], 'filecheck_save') ) {
			wp_nonce_ays('filecheck_save');
		}
		if (file_exists(NFW_LOG_DIR . '/nfwlog/cache/nfilecheck_snapshot.php') ) {
			$stat = stat(NFW_LOG_DIR . '/nfwlog/cache/nfilecheck_snapshot.php');
			$data = '== NinjaFirewall File Check (snapshot)'. "\n";
			$data.= '== ' . site_url() . "\n";
			$data.= '== ' . date_i18n('M d, Y @ H:i:s O', $stat['ctime']) . "\n\n";
			$fh = fopen(NFW_LOG_DIR . '/nfwlog/cache/nfilecheck_snapshot.php', 'r');
			while (! feof($fh) ) {
				$res = explode('::', fgets($fh) );
				if (! empty($res[0][0]) && $res[0][0] == '/') {
					$data .= $res[0] . "\n";
				}
			}
			fclose($fh);
			$data .= "\n== EOF\n";
			header('Content-Type: text/plain');
			header('Content-Length: '. strlen( $data ) );
			header('Content-Disposition: attachment; filename="'. $_SERVER['SERVER_NAME'] .'_snapshot.txt"');
			echo $data;
			exit;
		} else {
			wp_nonce_ays('filecheck_save');
		}
	}

	if (! empty($nfw_options['engine_version']) && version_compare($nfw_options['engine_version'], NFW_ENGINE_VERSION, '<') ) {
		// v1.0.4 update -------------------------------------------------
		if ( empty( $nfw_options['alert_email']) ) {
			$nfw_options['a_0']  = 1; $nfw_options['a_11'] = 1;
			$nfw_options['a_12'] = 1; $nfw_options['a_13'] = 0;
			$nfw_options['a_14'] = 0; $nfw_options['a_15'] = 1;
			$nfw_options['a_16'] = 0; $nfw_options['a_21'] = 1;
			$nfw_options['a_22'] = 1; $nfw_options['a_23'] = 0;
			$nfw_options['a_24'] = 0; $nfw_options['a_31'] = 1;
			$nfw_options['alert_email'] = get_option('admin_email');
		}
		// v1.1.0 update -------------------------------------------------
		if (! isset( $nfw_options['post_b64'] ) ) {
			$nfw_options['alert_sa_only']  = 2;
			$nfw_options['nt_show_status'] = 1;
			$nfw_options['post_b64']       = 1;
		}
		// v1.1.2 update -------------------------------------------------
		if (! isset( $nfw_options['no_xmlrpc'] ) ) {
			$nfw_options['no_xmlrpc'] = 0;
		}
		// v1.1.3 update -------------------------------------------------
		if (! isset( $nfw_options['enum_archives'] ) ) {
			$nfw_options['enum_archives'] = 0;
			$nfw_options['enum_login'] = 1;
		}
		// v1.1.6 update -------------------------------------------------
		if (! isset( $nfw_options['request_sanitise'] ) ) {
			$nfw_options['request_sanitise'] = 0;
		}
		// v1.1.9 update -------------------------------------------------
		if ( empty( $nfw_options['logo']) ) {
			$nfw_options['logo'] = plugins_url() . '/ninjafirewall/images/ninjafirewall_75.png';
		}
		// v1.2.1 update -------------------------------------------------
		if ( empty( $nfw_options['fg_mtime']) ) {
			$nfw_options['fg_enable'] = 0;
			$nfw_options['fg_mtime'] = 10;
		}
		// v1.2.3 update -------------------------------------------------
		if ( version_compare( $nfw_options['engine_version'], '1.2.3', '<' ) ) {
			$nfw_options['blocked_msg'] = base64_encode($nfw_options['blocked_msg']);
		}
		// v1.2.4 update -------------------------------------------------
		if ( isset($nfw_options['$auth_msg']) ) {
			unset($nfw_options['$auth_msg']);
		}
		// v1.2.7 update -------------------------------------------------
		if ( version_compare( $nfw_options['engine_version'], '1.2.7', '<' ) ) {
			if ( is_writable(NFW_LOG_DIR) ) {
				if (! file_exists(NFW_LOG_DIR . '/nfwlog') ) {
					mkdir( NFW_LOG_DIR . '/nfwlog', 0755);
				}
				if (! file_exists(NFW_LOG_DIR . '/nfwlog/cache') ) {
					mkdir( NFW_LOG_DIR . '/nfwlog/cache', 0755);
				}
				touch( NFW_LOG_DIR . '/nfwlog/index.html' );
				touch( NFW_LOG_DIR . '/nfwlog/cache/index.html' );
				@file_put_contents(NFW_LOG_DIR . '/nfwlog/.htaccess', "Order Deny,Allow\nDeny from all", LOCK_EX);
				@file_put_contents(NFW_LOG_DIR . '/nfwlog/cache/.htaccess', "Order Deny,Allow\nDeny from all", LOCK_EX);

				$nfwbfd_log = NFW_LOG_DIR . '/nfwlog/cache/bf_conf.php';
				if ((! empty($nfw_options['bf_request'])) && (! empty($nfw_options['bf_bantime'])) &&
					 (! empty($nfw_options['bf_attempt'])) && (! empty($nfw_options['bf_maxtime'])) &&
					 (! empty($nfw_options['auth_name'])) && (! empty($nfw_options['auth_pass'])) &&
					 (! empty($nfw_options['bf_rand'])) ) {
					if ( empty($nfw_options['bf_enable'])) {
						$nfw_options['bf_enable'] = 1;
					}
					if ( empty($nfw_options['auth_msg']) ) {
						$nfw_options['auth_msg'] = 'Access restricted';
					}
					if (! isset($nfw_options['bf_xmlrpc']) ) {
						$nfw_options['bf_xmlrpc'] = 0;
					}
					if (! isset($nfw_options['bf_authlog']) ) {
						$nfw_options['bf_authlog'] = 0;
					}
					$data = '<?php $bf_enable=' . $nfw_options['bf_enable'] .
					';$bf_request=\'' . $nfw_options['bf_request'] . '\'' .
					';$bf_bantime=' . $nfw_options['bf_bantime'] .
					';$bf_attempt=' . $nfw_options['bf_attempt'] .
					';$bf_maxtime=' . $nfw_options['bf_maxtime'] .
					';$bf_xmlrpc=' . $nfw_options['bf_xmlrpc'] .
					';$auth_name=\'' . $nfw_options['auth_name'] . '\'' .
					';$auth_pass=\'' . $nfw_options['auth_pass'] . '\';' .
					'$auth_msg=\'' . $nfw_options['auth_msg'] . '\'' .
					';$bf_rand=\'' . $nfw_options['bf_rand'] . '\';'.
					'$bf_authlog='. $nfw_options['bf_authlog'] . '; ?>';
					$fh = fopen( $nfwbfd_log, 'w' );
					fwrite( $fh, $data );
					fclose( $fh );
				}
			}
			unset($nfw_options['bf_enable']);
			unset($nfw_options['bf_request']);
			unset($nfw_options['bf_bantime']);
			unset($nfw_options['bf_attempt']);
			unset($nfw_options['bf_maxtime']);
			unset($nfw_options['bf_xmlrpc']);
			unset($nfw_options['auth_name']);
			unset($nfw_options['auth_pass']);
			unset($nfw_options['auth_msg']);
			unset($nfw_options['bf_rand']);
			unset($nfw_options['bf_authlog']);
		}
		// v1.3.1 update -------------------------------------------------
		if ( version_compare( $nfw_options['engine_version'], '1.3.1', '<' ) ) {
			if ( function_exists('header_register_callback') && function_exists('headers_list') && function_exists('header_remove') ) {
				$nfw_options['response_headers'] = '000100000';
			}
		}
		// v1.3.3 update -------------------------------------------------
		if ( version_compare( $nfw_options['engine_version'], '1.3.3', '<' ) ) {
			$nfw_options['a_41'] = 1;
			$nfw_options['sched_scan'] = 0;
			$nfw_options['report_scan'] = 0;
		}
		// v1.3.4 update -------------------------------------------------
		if ( version_compare( $nfw_options['engine_version'], '1.3.4', '<' ) ) {
			$nfw_options['a_51'] = 1;
		}
		// v1.3.5 update -------------------------------------------------
		if ( version_compare( $nfw_options['engine_version'], '1.3.5', '<' ) ) {
			$nfw_options['fg_exclude'] = '';
		}
		// v1.3.6 update -------------------------------------------------
		if ( version_compare( $nfw_options['engine_version'], '1.3.6', '<' ) ) {
			$path = NFW_LOG_DIR . '/nfwlog/cache/';
			$glob = glob($path . "nfdbhash*php");
			if ( is_array($glob)) {
				foreach($glob as $file) {
					unlink($file);
				}
			}
		}
		// v1.7 update -------------------------------------------------
		if ( version_compare( $nfw_options['engine_version'], '1.7', '<' ) ) {
			$nfw_options['a_52'] = 1;
			if ( ! wp_next_scheduled('nfdailyreport') ) {
				nfw_get_blogtimezone();
				wp_schedule_event( strtotime( date('Y-m-d 00:00:05', strtotime("+1 day")) ), 'daily', 'nfdailyreport');
			}
			$nfw_options['no_xmlrpc_multi'] = 0;
		}
		// v3.1.2 update (file guard) ----------------------------------
		if ( version_compare( $nfw_options['engine_version'], '3.1.2', '<' ) ) {
			if (! empty( $nfw_options['fg_exclude'] ) ) {
				$nfw_options['fg_exclude'] = preg_quote( $nfw_options['fg_exclude'], '`');
			}
		}
		// v3.2.2 update -----------------------------------------------
		if ( version_compare( $nfw_options['engine_version'], '3.2.2', '<' ) ) {
			if ( is_multisite() ) {
				update_site_option('nfw_options', $nfw_options);
				update_site_option('nfw_rules', $nfw_rules_new);
			}
		}
		// v3.3 update ---------------------------------------------------
		if ( version_compare( $nfw_options['engine_version'], '3.3', '<' ) ) {
			if ( function_exists('header_register_callback') && function_exists('headers_list') && function_exists('header_remove') ) {
				if (! empty( $nfw_options['response_headers'] ) && strlen( $nfw_options['response_headers'] ) == 6 ) {
					$nfw_options['response_headers'] .= '00';
				}
			}
		}
		// v3.4 update ---------------------------------------------------
		if ( version_compare( $nfw_options['engine_version'], '3.4', '<' ) ) {
			$nfw_options['a_53'] = 1;
		}
		// v3.5.1 update -------------------------------------------------
		if ( version_compare( $nfw_options['engine_version'], '3.5.1', '<' ) ) {
			// Create garbage collector's cron job:
			if ( wp_next_scheduled( 'nfwgccron' ) ) {
				wp_clear_scheduled_hook( 'nfwgccron' );
			}
			wp_schedule_event( time() + 60, 'hourly', 'nfwgccron' );
		}
		// v3.6.2 update -------------------------------------------------
		if ( version_compare( $nfw_options['engine_version'], '3.6.2', '<' ) ) {
			$nfw_options['rate_notice'] = time() + 86400 * 15;
		}
		// -------------------------------------------------------------

		$nfw_options['engine_version'] = NFW_ENGINE_VERSION;
		$is_update = 1;

		define('NFUPDATESDO', 2);
		@nf_sub_updates();

		if (! defined('NFW_NEWRULES_VERSION') ) {
			define('NFW_NEWRULES_VERSION', '20160101.1');
		}

		if ( $nfw_rules_new = @unserialize(NFW_RULES) ) {
			foreach ( $nfw_rules_new as $new_key => $new_value ) {
				foreach ( $new_value as $key => $value ) {
					if ( ( isset( $nfw_rules[$new_key]['ena'] ) ) && ( $key == 'ena' ) ) {
						$nfw_rules_new[$new_key]['ena'] = $nfw_rules[$new_key]['ena'];
					}
					if ( ( isset( $nfw_rules[$new_key]['on'] ) ) && ( $key == 'ena' ) ) {
						$nfw_rules_new[$new_key]['ena'] = $nfw_rules[$new_key]['on'];
					}
				}
			}
			if ( isset( $nfw_rules[NFW_DOC_ROOT]['what'] ) ) {
				$nfw_rules_new[NFW_DOC_ROOT]['cha'][1]['wha'] = str_replace( '/', '/[./]*', $nfw_rules[NFW_DOC_ROOT]['what'] );
				$nfw_rules_new[NFW_DOC_ROOT]['ena']	= $nfw_rules[NFW_DOC_ROOT]['on'];
			} else {
				$nfw_rules_new[NFW_DOC_ROOT]['cha'][1]['wha'] = $nfw_rules[NFW_DOC_ROOT]['cha'][1]['wha'];
				$nfw_rules_new[NFW_DOC_ROOT]['ena']	= $nfw_rules[NFW_DOC_ROOT]['ena'];
			}

			if ( version_compare( $nfw_options['rules_version'], '20140925', '<' ) ) {
			if ( isset($nfw_rules_new[151]) ) {
					unset($nfw_rules_new[151]);
				}
				if ( isset($nfw_rules_new[152]) ) {
					unset($nfw_rules_new[152]);
				}
			}
			// ---------------------------------------------------------------

			nfw_update_option( 'nfw_rules', $nfw_rules_new);
			$nfw_options['rules_version'] = NFW_NEWRULES_VERSION;

		} else {

			if ( ( is_multisite() ) && ( $nfw_options['alert_sa_only'] == 2 ) ) {
				$recipient = get_option('admin_email');
			} else {
				$recipient = $nfw_options['alert_email'];
			}

			$subject = '[NinjaFirewall] ' . __('ERROR: Failed to update rules', 'ninjafirewall');
			if ( is_multisite() ) {
				$url = __('-Blog :', 'ninjafirewall') .' '. network_home_url('/') . "\n\n";
			} else {
				$url = __('-Blog :', 'ninjafirewall') .' '. home_url('/') . "\n\n";
			}
			$message = __('NinjaFirewall failed to update its rules. This is a critical error, your current rules may be corrupted or disabled. In order to solve the problem, please follow these instructions:', 'ninjafirewall') . "\n\n";
			$message.= __('1. Log in to your WordPress admin dashboard.', 'ninjafirewall') . "\n";
			$message.= __('2. Go to "NinjaFirewall > Updates".', 'ninjafirewall') . "\n";
			$message.= __('3. Click on "Check for updates now!".', 'ninjafirewall') .
							"\n\n".
							__('-Date :', 'ninjafirewall') .' '. ucfirst(date_i18n('F j, Y @ H:i:s')) . ' (UTC '. date('O') . ")\n" .
							$url .
						'NinjaFirewall (WP Edition) - https://nintechnet.com/' . "\n" .
					__('Support forum', 'ninjafirewall') . ': http://wordpress.org/support/plugin/ninjafirewall' . "\n";
			wp_mail( $recipient, $subject, $message );
		}
	}


	if ( $is_update ) {
		$tmp_data = '';
		// up to v1.2.7  -------------------------------------------------
		if ( version_compare( $nfw_options['engine_version'], '1.2.8', '<' ) ) {
			if ( isset($nfw_options['nfw_tmp']) ) {
				unset( $nfw_options['nfw_tmp'] );
				$log_file = NFW_LOG_DIR . '/nfwlog/firewall_' . date( 'Y-m' ) . '.php';
				if ( $tmp_data = @gzinflate( base64_decode( nfw_get_option('nfw_tmp') ) ) ) {
					@file_put_contents( $log_file, $tmp_data, LOCK_EX);
				}
				nfw_delete_option( 'nfw_tmp' );
			}
			if ( $tmp_data ) {
				$stat_file = NFW_LOG_DIR . '/nfwlog/stats_' . date( 'Y-m' ) . '.php';
				$nfw_stat = array('0', '0', '0', '0', '0', '0', '0', '0', '0', '0');
				$stats_lines = explode( PHP_EOL, $tmp_data );
				foreach ( $stats_lines as $line ) {
					if (preg_match( '/^\[.+?\]\s+\[.+?\]\s+(?:\[.+?\]\s+){3}\[([0-9])\]/', $line, $match) ) {
						++$nfw_stat[$match[1]];
					}
				}
				@file_put_contents( $stat_file, $nfw_stat[0] . ':' . $nfw_stat[1] . ':' .
					$nfw_stat[2] . ':' . $nfw_stat[3] . ':' . $nfw_stat[4] . ':' .
					$nfw_stat[5] . ':' . $nfw_stat[6] . ':' . $nfw_stat[7] . ':' .
					$nfw_stat[8] . ':' . $nfw_stat[9], LOCK_EX );
			}
		}

		nfw_update_option( 'nfw_options', $nfw_options);
	}

	if ( defined( 'NFW_ALERT' ) ) {
		nfw_check_emailalert();
	}

	// Run the garbage collector if needed (note that there's already
	// a hourly cron job for that purpose, but this call is only used
	// in the event where the admin disabled WP-Cron):
	nfw_garbage_collector();

	if (! empty( $nfw_options['wl_admin']) ) {
		$_SESSION['nfw_goodguy'] = true;
		if (! empty( $nfw_options['bf_enable'] ) && ! empty( $nfw_options['bf_rand'] ) ) {
			$_SESSION['nfw_bfd'] = $nfw_options['bf_rand'];
		}
		return;
	}
	if ( isset( $_SESSION['nfw_goodguy'] ) ) {
		unset( $_SESSION['nfw_goodguy'] );
	}
}

add_action('admin_init', 'nfw_upgrade' );

/* ------------------------------------------------------------------ */

function nfw_login_hook( $user_login, $user ) {

	// Check if the user is an admin and if we must whitelist them:

	nfw_session_start();

	$nfw_options = nfw_get_option( 'nfw_options' );

	if ( empty( $nfw_options['enabled'] ) ) { return; }

	if ( empty( $user->roles[0] ) ) {
		$whoami = '';
		$admin_flag = 1;
	} elseif ( $user->roles[0] == 'administrator' ) {
		$whoami = 'administrator';
		$admin_flag = 2;
	} else {
		$whoami = $user->roles[0];
		$admin_flag = 0;
	}

	if (! empty($nfw_options['a_0']) ) {
		if ( ( ( $nfw_options['a_0'] == 1) && ( $admin_flag )  ) ||	( $nfw_options['a_0'] == 2 ) ) {
			nfw_send_loginemail( $user_login, $whoami );
			if (! empty($nfw_options['a_41']) ) {
				nfw_log2('Logged in user', $user_login .' ('. $whoami .')', 6, 0);
			}
		}
	}

	if (! empty( $nfw_options['wl_admin']) ) {
		if ( ( $nfw_options['wl_admin'] == 1 && $admin_flag == 2 ) || ( $nfw_options['wl_admin'] == 2 ) ) {
			$_SESSION['nfw_goodguy'] = $nfw_options['wl_admin'];
			return;
		}
	}

	if ( isset( $_SESSION['nfw_goodguy'] ) ) {
		unset( $_SESSION['nfw_goodguy'] );
	}
}

add_action( 'wp_login', 'nfw_login_hook', 10, 2 );

/* ------------------------------------------------------------------ */

function nfw_send_loginemail( $user_login, $whoami ) {

	$nfw_options = nfw_get_option( 'nfw_options' );

	if ( ( is_multisite() ) && ( $nfw_options['alert_sa_only'] == 2 ) ) {
		$recipient = get_option('admin_email');
	} else {
		$recipient = $nfw_options['alert_email'];
	}

	$subject = '[NinjaFirewall] ' . __('Alert: WordPress console login', 'ninjafirewall');
	if ( is_multisite() ) {
		$url = __('-Blog :', 'ninjafirewall') .' '. network_home_url('/') . "\n\n";
	} else {
		$url = __('-Blog :', 'ninjafirewall') .' '. home_url('/') . "\n\n";
	}
	if (! empty( $whoami ) ) {
		$whoami = " ($whoami)";
	}
	$message = __('Someone just logged in to your WordPress admin console:', 'ninjafirewall') . "\n\n".
				__('-User :', 'ninjafirewall') .' '. $user_login . $whoami . "\n" .
				__('-IP   :', 'ninjafirewall') .' '. NFW_REMOTE_ADDR . "\n" .
				__('-Date :', 'ninjafirewall') .' '. ucfirst(date_i18n('F j, Y @ H:i:s')) . ' (UTC '. date('O') . ")\n" .
				$url .
				'NinjaFirewall (WP Edition) - https://nintechnet.com/' . "\n" .
				__('Support forum', 'ninjafirewall') . ': http://wordpress.org/support/plugin/ninjafirewall' . "\n";
	wp_mail( $recipient, $subject, $message );

}
/* ------------------------------------------------------------------ */

function nfw_logout_hook() {

	nfw_session_start();

	if ( isset( $_SESSION['nfw_goodguy'] ) ) {
		unset( $_SESSION['nfw_goodguy'] );
	}
	if (isset( $_SESSION['nfw_livelog'] ) ) {
		unset( $_SESSION['nfw_livelog'] );
	}

}

add_action( 'wp_logout', 'nfw_logout_hook' );

/* ------------------------------------------------------------------ */

function is_nfw_enabled() {

	$nfw_options = nfw_get_option( 'nfw_options' );

	if (! defined('NFW_STATUS') ) {
		define('NF_DISABLED', 10);
		return;
	}

	if ( isset($nfw_options['enabled']) && $nfw_options['enabled'] == '0' ) {
		define('NF_DISABLED', 9);
		return;
	}

	if (NFW_STATUS == 21 || NFW_STATUS == 22 || NFW_STATUS == 23) {
		define('NF_DISABLED', 10);
		return;
	}

	if (NFW_STATUS == 20) {
		define('NF_DISABLED', 0);
		return;
	}

	define('NF_DISABLED', NFW_STATUS);
	return;

}

/* ------------------------------------------------------------------ */

function ninjafirewall_admin_menu() {

	if ( nf_not_allowed( 0, __LINE__ ) ) { return; }

	if (! empty($_REQUEST['nfw_act']) && $_REQUEST['nfw_act'] == 99) {
		if ( empty($_GET['nfwnonce']) || ! wp_verify_nonce($_GET['nfwnonce'], 'show_phpinfo') ) {
			wp_nonce_ays('show_phpinfo');
		}
		phpinfo(33);
		exit;
	}

	$message = '<br /><br /><br /><br /><center>' .
				sprintf( __('Sorry %s, your request cannot be processed.', 'ninjafirewall'), '<b>%%REM_ADDRESS%%</b>') .
				'<br />' . __('For security reasons, it was blocked and logged.', 'ninjafirewall') .
				'<br /><br />%%NINJA_LOGO%%<br /><br />' .
				__('If you believe this was an error please contact the<br />webmaster and enclose the following incident ID:', 'ninjafirewall') .
				'<br /><br />[ <b>#%%NUM_INCIDENT%%</b> ]</center>';

	define( 'NFW_DEFAULT_MSG', $message );

	if (! defined('NF_DISABLED') ) {
		is_nfw_enabled();
	}

	if (NF_DISABLED == 10) {
		add_menu_page( 'NinjaFirewall', 'NinjaFirewall', 'manage_options',
			'NinjaFirewall', 'nf_menu_install',	plugins_url( '/images/nf_icon.png', __FILE__ )
		);
		add_submenu_page( 'NinjaFirewall', __('Installation', 'ninjafirewall'), __('Installation', 'ninjafirewall'), 'manage_options',
			'NinjaFirewall', 'nf_menu_install' );
		return;
	}

	add_menu_page( 'NinjaFirewall', 'NinjaFirewall', 'manage_options',
		'NinjaFirewall', 'nf_menu_main',	plugins_url( '/images/nf_icon.png', __FILE__ )
	);

	global $menu_hook;

	require_once plugin_dir_path(__FILE__) . 'lib/help.php';

	$menu_hook = add_submenu_page( 'NinjaFirewall', __('NinjaFirewall: Overview', 'ninjafirewall'), __('Overview', 'ninjafirewall'), 'manage_options',
		'NinjaFirewall', 'nf_menu_main' );
	add_action( 'load-' . $menu_hook, 'help_nfsubmain' );

	$menu_hook = add_submenu_page( 'NinjaFirewall', __('NinjaFirewall: Statistics', 'ninjafirewall'), __('Statistics', 'ninjafirewall'), 'manage_options',
		'nfsubstat', 'nf_sub_statistics' );
	add_action( 'load-' . $menu_hook, 'help_nfsubstat' );

	$menu_hook = add_submenu_page( 'NinjaFirewall', __('NinjaFirewall: Firewall Options', 'ninjafirewall'), __('Firewall Options', 'ninjafirewall'), 'manage_options',
		'nfsubopt', 'nf_sub_options' );
	add_action( 'load-' . $menu_hook, 'help_nfsubopt' );

	$menu_hook = add_submenu_page( 'NinjaFirewall', __('NinjaFirewall: Firewall Policies', 'ninjafirewall'), __('Firewall Policies', 'ninjafirewall'), 'manage_options',
		'nfsubpolicies', 'nf_sub_policies' );
	add_action( 'load-' . $menu_hook, 'help_nfsubpolicies' );

	$menu_hook = add_submenu_page( 'NinjaFirewall',  __('NinjaFirewall: File Guard', 'ninjafirewall'), __( 'File Guard', 'ninjafirewall'), 'manage_options',
		'nfsubfileguard', 'nf_sub_fileguard' );
	add_action( 'load-' . $menu_hook, 'help_nfsubfileguard' );

	$menu_hook = add_submenu_page( 'NinjaFirewall',  __('NinjaFirewall: File Check', 'ninjafirewall'),  __('File Check', 'ninjafirewall'), 'manage_options',
		'nfsubfilecheck', 'nf_sub_filecheck' );
	add_action( 'load-' . $menu_hook, 'help_nfsubfilecheck' );

	$nscan_options = get_option( 'nscan_options' );
	if ( defined('NSCAN_NAME') && defined('NSCAN_SLUG') && ! empty( $nscan_options['scan_nfwpintegration'] ) ) {
		$menu_hook = add_submenu_page( 'NinjaFirewall', NSCAN_NAME, NSCAN_NAME, 'manage_options', NSCAN_NAME, 'nscan_main_menu' );
		require_once dirname( __DIR__ ).'/'. NSCAN_SLUG .'/lib/help.php';
		add_action( 'load-' . $menu_hook, 'nscan_help' );
	} else {
		$menu_hook = add_submenu_page( 'NinjaFirewall', __('NinjaFirewall: Anti-Malware', 'ninjafirewall'), __('Anti-Malware', 'ninjafirewall'), 'manage_options',
		'nfsubmalwarescan', 'nf_sub_malwarescan' );
	}

	$menu_hook = add_submenu_page( 'NinjaFirewall', __('NinjaFirewall: Network', 'ninjafirewall'), __('Network', 'ninjafirewall'), 'manage_network',
		'nfsubnetwork', 'nf_sub_network' );
	add_action( 'load-' . $menu_hook, 'help_nfsubnetwork' );

	$menu_hook = add_submenu_page( 'NinjaFirewall', __('NinjaFirewall: Event Notifications', 'ninjafirewall'), __('Event Notifications', 'ninjafirewall'), 'manage_options',
		'nfsubevent', 'nf_sub_event' );
	add_action( 'load-' . $menu_hook, 'help_nfsubevent' );

	$menu_hook = add_submenu_page( 'NinjaFirewall', __('NinjaFirewall: Log-in Protection', 'ninjafirewall'), __('Login Protection', 'ninjafirewall'), 'manage_options',
		'nfsubloginprot', 'nf_sub_loginprot' );
	add_action( 'load-' . $menu_hook, 'help_nfsublogin' );

	$menu_hook = add_submenu_page( 'NinjaFirewall', __('NinjaFirewall: Firewall Log', 'ninjafirewall'), __('Firewall Log', 'ninjafirewall'), 'manage_options',
		'nfsublog', 'nf_sub_log' );
	add_action( 'load-' . $menu_hook, 'help_nfsublog' );

	$menu_hook = add_submenu_page( 'NinjaFirewall',  __('NinjaFirewall: Live Log', 'ninjafirewall'),  __('Live Log', 'ninjafirewall'), 'manage_options',
		'nfsublive', 'nf_sub_live' );
	add_action( 'load-' . $menu_hook, 'help_nfsublivelog' );

	$menu_hook = add_submenu_page( 'NinjaFirewall', __('NinjaFirewall: Rules Editor', 'ninjafirewall'), __('Rules Editor', 'ninjafirewall'), 'manage_options',
		'nfsubedit', 'nf_sub_editor' );
	add_action( 'load-' . $menu_hook, 'help_nfsubedit' );

	$menu_hook = add_submenu_page( 'NinjaFirewall', __('NinjaFirewall: Rules Update', 'ninjafirewall'), __('Rules Update', 'ninjafirewall'), 'manage_options',
		'nfsubupdates', 'nf_sub_updates' );
	add_action( 'load-' . $menu_hook, 'help_nfsubupdates' );

	$menu_hook = add_submenu_page( 'NinjaFirewall', 'NinjaFirewall: WP+ Edition', '<b style="color:#fcdc25">WP+ Edition</b>', 'manage_options',
		'nfsubwplus', 'nf_sub_wplus' );

	$menu_hook = add_submenu_page( 'NinjaFirewall', __('NinjaFirewall: About', 'ninjafirewall'), __('About...', 'ninjafirewall'), 'manage_options',
		'nfsubabout', 'nf_sub_about' );

}
// Must load before NinjaScanner (11):
if (! is_multisite() )  {
	add_action( 'admin_menu', 'ninjafirewall_admin_menu', 10 );
} else {
	add_action( 'network_admin_menu', 'ninjafirewall_admin_menu', 10 );
}

/* ------------------------------------------------------------------ */

function nf_admin_bar_status() {

	if (! current_user_can( 'manage_options' ) ) {
		return;
	}

	$nfw_options = nfw_get_option( 'nfw_options' );
	if ( @$nfw_options['nt_show_status'] != 1 && ! current_user_can('manage_network') ) {
		return;
	}

	if (! defined('NF_DISABLED') ) {
		is_nfw_enabled();
	}
	if (NF_DISABLED) { return; }

	global $wp_admin_bar;
	$wp_admin_bar->add_menu( array(
		'id'    => 'nfw_ntw1',
		'title' => '<img src="' . plugins_url() . '/ninjafirewall/images/ninjafirewall_20.png" ' .
				'style="vertical-align:middle;margin-right:5px" />',
	) );

	if ( current_user_can( 'manage_network' ) ) {
		$wp_admin_bar->add_menu( array(
			'parent' => 'nfw_ntw1',
			'id'     => 'nfw_ntw2',
			'title'  => __( 'NinjaFirewall Settings', 'ninjafirewall'),
			'href'   => network_admin_url() . 'admin.php?page=NinjaFirewall',
		) );
	} else {
		if ( defined('NFW_STATUS') ) {
			$wp_admin_bar->add_menu( array(
				'parent' => 'nfw_ntw1',
				'id'     => 'nfw_ntw2',
				'title'  => __( 'NinjaFirewall is enabled', 'ninjafirewall'),
			) );
		}
	}
}

if ( is_multisite() )  {
	add_action('admin_bar_menu', 'nf_admin_bar_status', 95);
}

/* ------------------------------------------------------------------ */

function nf_menu_install() {

	nf_not_allowed( 'block', __LINE__ );

	require_once plugin_dir_path(__FILE__) . 'install.php';
}

/* ------------------------------------------------------------------ */

function nf_menu_main() {

	nf_not_allowed( 'block', __LINE__ );

	$nfw_options = nfw_get_option( 'nfw_options' );

	if (! defined('NF_DISABLED') ) {
		is_nfw_enabled();
	}

?>

<div class="wrap">
	<div style="width:33px;height:33px;background-image:url(<?php echo plugins_url() ?>/ninjafirewall/images/ninjafirewall_32.png);background-repeat:no-repeat;background-position:0 0;margin:7px 5px 0 0;float:left;"></div>
	<h1><?php _e('NinjaFirewall (WP Edition)', 'ninjafirewall') ?></h1>
	<?php
	if ( @NFW_STATUS == 20 && ! empty( $_REQUEST['nfw_firstrun']) ) {
		echo '<br><div class="updated notice is-dismissible"><p>' .
			__('Congratulations, NinjaFirewall is up and running!', 'ninjafirewall') .	'<br />' .
			__('If you need help, click on the contextual "Help" menu tab located in the upper right corner of each page.', 'ninjafirewall');
		if (! empty($_SESSION['email_install']) ) {
			echo '<p>' . __('A "Quick Start, FAQ & Troubleshooting Guide" email was sent to', 'ninjafirewall') .' <code>' .htmlspecialchars( $_SESSION['email_install'] ) .'</code>.</p>';
			unset($_SESSION['email_install']);
		}
		echo '</p></div>';
		unset( $_SESSION['abspath'] ); unset( $_SESSION['http_server'] );
		unset( $_SESSION['php_ini_type'] ); unset( $_SESSION['abspath_writable'] );
		unset( $_SESSION['ini_write'] ); unset( $_SESSION['htaccess_write'] );
		unset( $_SESSION['waf_mode'] );
	}

	// Display a one-time notice after two weeks of use:
	nfw_rate_notice( $nfw_options );

	?>
	<br />
	<table class="form-table">

	<?php
	if (NF_DISABLED) {
		if (! empty($GLOBALS['err_fw'][NF_DISABLED]) ) {
			$msg = $GLOBALS['err_fw'][NF_DISABLED];
		} else {
			$msg = __('unknown error', 'ninjafirewall') . ' #' . NF_DISABLED;
		}
	?>
		<tr>
			<th scope="row"><?php _e('Firewall', 'ninjafirewall') ?></th>
			<td width="20" align="left"><img src="<?php echo plugins_url( '/images/glyphicons-error.png', __FILE__ ) ?>"></td>
			<td><?php echo $msg ?></td>
		</tr>

	<?php
	} else {
	?>

		<tr>
			<th scope="row"><?php _e('Firewall', 'ninjafirewall') ?></th>
			<td width="20" align="left">&nbsp;</td>
			<td><?php _e('Enabled', 'ninjafirewall') ?></td>
		</tr>

	<?php
	}

	if ( defined('NFW_WPWAF') ) {
		$mode = __('WordPress WAF', 'ninjafirewall');
	} else {
		$mode = __('Full WAF', 'ninjafirewall');
	}
	?>
		<tr>
			<th scope="row"><?php _e('Mode', 'ninjafirewall') ?></th>
			<td width="20" align="left">&nbsp;</td>
			<td><?php printf( __('NinjaFirewall is running in %s mode.', 'ninjafirewall'), '<a href="https://blog.nintechnet.com/full_waf-vs-wordpress_waf/">'. $mode .'</a>'); ?></td>
		</tr>
	<?php

	if (! empty( $nfw_options['debug']) ) {
	?>
		<tr>
			<th scope="row"><?php _e('Debugging mode', 'ninjafirewall') ?></th>
			<td width="20" align="left"><img src="<?php echo plugins_url( '/images/glyphicons-error.png', __FILE__ ) ?>"></td>
			<td><?php _e('Enabled.', 'ninjafirewall') ?>&nbsp;<a href="?page=nfsubopt"><?php _e('Click here to turn Debugging Mode off', 'ninjafirewall') ?></a></td>
		</tr>
	<?php
	}
	?>
		<tr>
			<th scope="row"><?php _e('PHP SAPI', 'ninjafirewall') ?></th>
			<td width="20" align="left">&nbsp;</td>
			<td>
				<?php
				if ( defined('HHVM_VERSION') ) {
					echo 'HHVM';
				} else {
					echo strtoupper(PHP_SAPI);
				}
				echo ' ~ '. PHP_MAJOR_VERSION .'.'. PHP_MINOR_VERSION .'.'. PHP_RELEASE_VERSION;
				?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php _e('Version', 'ninjafirewall') ?></th>
			<td width="20" align="left">&nbsp;</td>
			<td><?php echo NFW_ENGINE_VERSION . ' ~ ' . __('Security rules:', 'ninjafirewall' ) . ' ' . preg_replace('/(\d{4})(\d\d)(\d\d)/', '$1-$2-$3', $nfw_options['rules_version']) ?></td>
		</tr>
	<?php

	// If security rules updates are disabled, warn the user:
	if ( empty( $nfw_options['enable_updates'] ) ) {
		?>
		<tr>
			<th scope="row"><?php _e('Updates', 'ninjafirewall') ?></th>
			<td width="20" align="left"><img src="<?php echo plugins_url() ?>/ninjafirewall/images/glyphicons-warning.png"></td>
			<td><a href="?page=nfsubupdates"><?php _e( 'Security rules updates are disabled.', 'ninjafirewall' ) ?></a> <?php _e( 'If you want your blog to be protected against the latest threats, enable automatic security rules updates.', 'ninjafirewall' ) ?></td>
		</tr>
		<?php
	}

	if ( empty($_SESSION['nfw_goodguy']) ) {
		?>
		<tr>
			<th scope="row"><?php _e('Admin user', 'ninjafirewall') ?></th>
			<td width="20" align="left"><img src="<?php echo plugins_url() ?>/ninjafirewall/images/glyphicons-warning.png"></td>
			<td><?php printf( __('You are not whitelisted. Ensure that the "Do not block WordPress administrator" option is enabled in the <a href="%s">Firewall Policies</a> menu, otherwise you could get blocked by the firewall while working from your administration dashboard.', 'ninjafirewall'), '?page=nfsubpolicies') ?></td>
		</tr>
	<?php
	} else {
		$current_user = wp_get_current_user();
		?>
		<tr>
			<th scope="row"><?php _e('Admin user', 'ninjafirewall') ?></th>
			<td width="20" align="left">&nbsp;</td>
			<td><code><?php echo htmlspecialchars($current_user->user_login) ?></code>: <?php _e('You are whitelisted by the firewall.', 'ninjafirewall') ?></td>
		</tr>
	<?php
	}
	if ( defined('NFW_ALLOWED_ADMIN') && ! is_multisite() ) {
	?>
		<tr>
			<th scope="row"><?php _e('Restrictions', 'ninjafirewall') ?></th>
			<td width="20" align="left">&nbsp;</td>
			<td><?php _e('Access to NinjaFirewall is restricted to:', 'ninjafirewall') ?> <code><?php echo htmlspecialchars(NFW_ALLOWED_ADMIN) ?></code></td>
		</tr>
	<?php
	}

	// Try to find out if there is any "lost" session between the firewall
	// and the plugin part of NinjaFirewall (could be a buggy plugin killing
	// the session etc), unless we just installed it:
	if ( defined( 'NFW_SWL' ) && ! empty( $_SESSION['nfw_goodguy'] ) && empty( $_REQUEST['nfw_firstrun'] ) ) {
		?>
		<tr>
			<th scope="row"><?php _e('User session', 'ninjafirewall') ?></th>
			<td width="20" align="left"><img src="<?php echo plugins_url() . '/ninjafirewall/images/glyphicons-warning.png' ?>"></td>
			<td><?php _e('It seems that the user session set by NinjaFirewall was not found by the firewall script.', 'ninjafirewall') ?></td>
		</tr>
		<?php
	}

	if ( ! empty( $nfw_options['clogs_pubkey'] ) ) {
		$err_msg = $ok_msg = '';
		if (! preg_match( '/^[a-f0-9]{40}:([a-f0-9:.]{3,39}|\*)$/', $nfw_options['clogs_pubkey'], $match ) ) {
			$err_msg = sprintf( __('the public key is invalid. Please <a href="%s">check your configuration</a>.', 'ninjafirewall'), '?page=nfsublog#clogs');

		} else {
			if ( $match[1] == '*' ) {
				$ok_msg = __( "No IP address restriction.", 'ninjafirewall');

			} elseif ( filter_var( $match[1], FILTER_VALIDATE_IP ) ) {
				$ok_msg = sprintf( __("IP address %s is allowed to access NinjaFirewall's log on this server.", 'ninjafirewall'), htmlspecialchars( $match[1]) );

			} else {
				$err_msg = sprintf( __('the whitelisted IP is not valid. Please <a href="%s">check your configuration</a>.', 'ninjafirewall'), '?page=nfsublog#clogs');
			}
		}
		?>
		<tr>
			<th scope="row"><?php _e('Centralized Logging', 'ninjafirewall') ?></th>
		<?php
		if ( $err_msg ) {
			?>
				<td width="20" align="left"><img src="<?php echo plugins_url() . '/ninjafirewall/images/glyphicons-error.png' ?>"></td>
				<td><?php printf( __('Error: %s', 'ninjafirewall'), $err_msg) ?></td>
			</tr>
			<?php
			$err_msg = '';
		} else {
			?>
				<td width="20" align="left">&nbsp;</td>
				<td><a href="?page=nfsublog#clogs"><?php _e('Enabled', 'ninjafirewall'); echo "</a>. $ok_msg"; ?></td>
			</tr>
		<?php
		}
	}



	if (! filter_var(NFW_REMOTE_ADDR, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) ) {
		?>
		<tr>
			<th scope="row"><?php _e('Source IP', 'ninjafirewall') ?></th>
			<td width="20" align="left"><img src="<?php echo plugins_url( '/images/glyphicons-warning.png', __FILE__ )?>"></td>
			<td><?php printf( __('You have a private IP : %s', 'ninjafirewall') .'<br />'. __('If your site is behind a reverse proxy or a load balancer, ensure that you have setup your HTTP server or PHP to forward the correct visitor IP, otherwise use the NinjaFirewall %s configuration file.', 'ninjafirewall'), htmlentities(NFW_REMOTE_ADDR), '<code><a href="https://nintechnet.com/ninjafirewall/wp-edition/help/?htninja">.htninja</a></code>') ?></td>
		</tr>
		<?php
	}
	if (! empty($_SERVER["HTTP_CF_CONNECTING_IP"]) ) {
		if ( NFW_REMOTE_ADDR != $_SERVER["HTTP_CF_CONNECTING_IP"] ) {
		?>
		<tr>
			<th scope="row"><?php _e('CDN detection', 'ninjafirewall') ?></th>
			<td width="20" align="left"><img src="<?php echo plugins_url( '/images/glyphicons-warning.png', __FILE__ )?>"></td>
			<td><?php printf( __('%s detected: you seem to be using Cloudflare CDN services. Ensure that you have setup your HTTP server or PHP to forward the correct visitor IP, otherwise use the NinjaFirewall %s configuration file.', 'ninjafirewall'), '<code>HTTP_CF_CONNECTING_IP</code>', '<code><a href="https://nintechnet.com/ninjafirewall/wp-edition/help/?htninja">.htninja</a></code>') ?></td>
		</tr>
		<?php
		}
	}
	if (! empty($_SERVER["HTTP_INCAP_CLIENT_IP"]) ) {
		if ( NFW_REMOTE_ADDR != $_SERVER["HTTP_INCAP_CLIENT_IP"] ) {
		?>
		<tr>
			<th scope="row"><?php _e('CDN detection', 'ninjafirewall') ?></th>
			<td width="20" align="left"><img src="<?php echo plugins_url( '/images/glyphicons-warning.png', __FILE__ )?>"></td>
			<td><?php printf( __('%s detected: you seem to be using Incapsula CDN services. Ensure that you have setup your HTTP server or PHP to forward the correct visitor IP, otherwise use the NinjaFirewall %s configuration file.', 'ninjafirewall'), '<code>HTTP_INCAP_CLIENT_IP</code>', '<code><a href="https://nintechnet.com/ninjafirewall/wp-edition/help/?htninja">.htninja</a></code>') ?></td>
		</tr>
		<?php
		}
	}

	if (! is_writable( NFW_LOG_DIR . '/nfwlog') ) {
		?>
			<tr>
			<th scope="row"><?php _e('Log dir', 'ninjafirewall') ?></th>
			<td width="20" align="left"><img src="<?php echo plugins_url( '/images/glyphicons-error.png', __FILE__ )?>"></td>
			<td><?php printf( __('%s directory is not writable! Please chmod it to 0777 or equivalent.', 'ninjafirewall'), '<code>'. htmlspecialchars(NFW_LOG_DIR) .'/nfwlog/</code>') ?></td>
		</tr>
	<?php
	}

	if (! is_writable( NFW_LOG_DIR . '/nfwlog/cache') ) {
		?>
			<tr>
			<th scope="row"><?php _e('Log dir', 'ninjafirewall') ?></th>
			<td width="20" align="left"><img src="<?php echo plugins_url( '/images/glyphicons-error.png', __FILE__ )?>"></td>
			<td><?php printf(__('%s directory is not writable! Please chmod it to 0777 or equivalent.', 'ninjafirewall'), '<code>'. htmlspecialchars(NFW_LOG_DIR) . '/nfwlog/cache/</code>') ?></td>
		</tr>
	<?php
	}

	$doc_root = rtrim($_SERVER['DOCUMENT_ROOT'], '/');
	if ( @file_exists( $file = dirname( $doc_root ) . '/.htninja') ||
		@file_exists( $file = $doc_root . '/.htninja') ) {
		echo '<tr><th scope="row">' . __('Optional configuration file', 'ninjafirewall') . '</th>
		<td width="20">&nbsp;</td>
		<td><code>' .  htmlentities($file) . '</code></td>
		</tr>';
	}

	echo '</table>';
	?>
</div>

<?php
}

/* ------------------------------------------------------------------ */

function nf_sub_statistics() {

	require plugin_dir_path(__FILE__) . 'lib/statistics.php';

}

/* ------------------------------------------------------------------ */

function nf_sub_options() { // i18n

	require plugin_dir_path(__FILE__) . 'lib/nf_sub_options.php';

}

/* ------------------------------------------------------------------ */

function nf_sub_policies() {

	nf_not_allowed( 'block', __LINE__ );

	$yes = __('Yes', 'ninjafirewall');
	$no =  __('No', 'ninjafirewall');
	$default =  ' ' . __('(default)', 'ninjafirewall');
	$full_waf_msg = '<br /><img src="' . plugins_url() . '/ninjafirewall/images/glyphicons-warning.png">&nbsp;<span class="description">' . sprintf( __('This feature is only available when NinjaFirewall is running in %s mode.', 'ninjafirewall'), '<a href="https://blog.nintechnet.com/full_waf-vs-wordpress_waf/">Full WAF</a>') . '</span>';
	if ( defined('NFW_WPWAF') ) {
		$option_disabled = 1;
	} else {
		$option_disabled = 0;
	}

	$nfw_options = nfw_get_option( 'nfw_options' );
	$nfw_rules = nfw_get_option( 'nfw_rules' );

	echo '
<script>
function restore() {
   if (confirm("' . esc_js( __('All fields will be restored to their default values. Go ahead?', 'ninjafirewall') ) . '")){
      return true;
   }else{
		return false;
   }
}
function chksubmenu() {
	if (document.fwrules.elements[\'nfw_options[uploads]\'].value > 0) {
      document.fwrules.san.disabled = false;
      document.fwrules.subs.disabled = false;
      document.getElementById("sanitize-fn").style.color = "#444";
   } else {
      document.fwrules.san.disabled = true;
      document.fwrules.subs.disabled = true;
      document.getElementById("sanitize-fn").style.color = "#bbbbbb";
   }
}
function csp_onoff(what, csp) {
	if (what == 0) {
		document.getElementById(csp).readOnly = true;
	} else {
		document.getElementById(csp).readOnly = false;
		document.getElementById(csp).focus();
	}
}
function ssl_warn() {';
	if ($_SERVER['SERVER_PORT'] == 443 ) {
		echo 'return true;';
	} else {
		echo '
		if (confirm("' . esc_js( __('WARNING: ensure that you can access your admin console over HTTPS before enabling this option, otherwise you will lock yourself out of your site. Go ahead?', 'ninjafirewall') ) . '")){
			return true;
		}
		return false;';
	}
echo '
}
function sanitise_fn(cbox) {
	if(cbox.checked) {
		if (confirm("' . esc_js( __('Any character that is not a letter [a-zA-Z], a digit [0-9], a dot [.], a hyphen [-] or an underscore [_] will be removed from the filename and replaced with the substitution character. Continue?', 'ninjafirewall') ) . '")){
			return true;
		}
		return false;
	}
}
function nfw_switch_tabs(tab) {
	if ( tab == 1 ) {
		jQuery("#basic-options").show(); jQuery("#tab-1").addClass("nav-tab-active");
		jQuery("#intermediate-options").hide(); jQuery("#tab-2").removeClass("nav-tab-active");
		jQuery("#advanced-options").hide(); jQuery("#tab-3").removeClass("nav-tab-active");

	} else if ( tab == 2 ) {
		jQuery("#basic-options").hide(); jQuery("#tab-1").removeClass("nav-tab-active");
		jQuery("#intermediate-options").show(); jQuery("#tab-2").addClass("nav-tab-active");
		jQuery("#advanced-options").hide(); jQuery("#tab-3").removeClass("nav-tab-active");

	} else if ( tab ==3 ) {
		jQuery("#basic-options").hide(); jQuery("#tab-1").removeClass("nav-tab-active");
		jQuery("#intermediate-options").hide(); jQuery("#tab-2").removeClass("nav-tab-active");
		jQuery("#advanced-options").show(); jQuery("#tab-3").addClass("nav-tab-active");
	}
}
</script>

<div class="wrap">
	<div style="width:33px;height:33px;background-image:url( ' . plugins_url() . '/ninjafirewall/images/ninjafirewall_32.png);background-repeat:no-repeat;background-position:0 0;margin:7px 5px 0 0;float:left;"></div>
	<h1>' . __('Firewall Policies', 'ninjafirewall') . '</h1>';

	if ( isset( $_POST['nfw_options']) ) {
		if ( empty($_POST['nfwnonce']) || ! wp_verify_nonce($_POST['nfwnonce'], 'policies_save') ) {
			wp_nonce_ays('policies_save');
		}
		if (! empty($_POST['Save']) ) {
			nf_sub_policies_save();
			echo '<div class="updated notice is-dismissible"><p>' . __('Your changes have been saved.', 'ninjafirewall') . '</p></div>';
		} elseif (! empty($_POST['Default']) ) {
			nf_sub_policies_default();
			echo '<div class="updated notice is-dismissible"><p>' . __('Default values were restored.', 'ninjafirewall') . '</p></div>';
		} else {
			echo '<div class="error notice is-dismissible"><p>' . __('No action taken.', 'ninjafirewall') . '</p></div>';
		}
		$nfw_options = nfw_get_option( 'nfw_options' );
		$nfw_rules = nfw_get_option( 'nfw_rules' );
	}

	?>
	<br />
	<h2 class="nav-tab-wrapper wp-clearfix" style="cursor:pointer">
		<a id="tab-1" class="nav-tab nav-tab-active" onClick="nfw_switch_tabs(1)"><?php _e( 'Basic Policies', 'ninjafirewall' ) ?></a>
		<a id="tab-2" class="nav-tab" onClick="nfw_switch_tabs(2)"><?php _e( 'Intermediate Policies', 'ninjafirewall' ) ?></a>
		<a id="tab-3" class="nav-tab" onClick="nfw_switch_tabs(3)"><?php _e( 'Advanced Policies', 'ninjafirewall' ) ?></a>
	</h2>
	<br />
	<?php

	echo '<form method="post" name="fwrules">';
	wp_nonce_field('policies_save', 'nfwnonce', 0);

	// ==========================================================================
	// Basic options:
	?>
	<div id="basic-options">
	<?php
	if ( ( isset( $nfw_options['scan_protocol']) ) &&
		( preg_match( '/^[123]$/', $nfw_options['scan_protocol']) ) ) {
		$scan_protocol = $nfw_options['scan_protocol'];
	} else {
		$scan_protocol = 3;
	}

	?>
	<h3>HTTP / HTTPS</h3>
	<table class="form-table">
		<tr>
			<th scope="row"><?php _e('Enable NinjaFirewall for', 'ninjafirewall') ?></th>
			<td width="20">&nbsp;</td>
			<td align="left">
			<p><label><input type="radio" name="nfw_options[scan_protocol]" value="3"<?php checked($scan_protocol, 3 ) ?>>&nbsp;<?php _e('HTTP and HTTPS traffic (default)', 'ninjafirewall') ?></label></p>
			<p><label><input type="radio" name="nfw_options[scan_protocol]" value="1"<?php checked($scan_protocol, 1 ) ?>>&nbsp;<?php _e('HTTP traffic only', 'ninjafirewall') ?></label></p>
			<p><label><input type="radio" name="nfw_options[scan_protocol]" value="2"<?php checked($scan_protocol, 2 ) ?>>&nbsp;<?php _e('HTTPS traffic only', 'ninjafirewall') ?></label></p>
			</td>
		</tr>
	</table>

	<?php
		if ( empty( $nfw_options['sanitise_fn']) ) {
		$sanitise_fn = 0;
	} else {
		$sanitise_fn = 1;
	}
	if ( empty( $nfw_options['uploads']) ) {
		$uploads = 0;
		$sanitise_fn = 0;
	} else {
		$uploads = 1;
	}
	if ( empty( $nfw_options['substitute'] ) || strlen( $nfw_options['substitute'] ) > 1 || $nfw_options['substitute'] == '/' ) {
		$substitute = 'X';
	} else {
		$substitute = htmlspecialchars( $nfw_options['substitute'] );
	}
	?>
	<br />
	<h3><?php _e('Uploads', 'ninjafirewall') ?></h3>
	<table class="form-table">
		<tr>
			<th scope="row"><?php _e('File Uploads', 'ninjafirewall') ?></th>
			<td width="20">&nbsp;</td>
			<td align="left">
				<select name="nfw_options[uploads]" onchange="chksubmenu();">
					<option value="1"<?php selected( $uploads, 1 ) ?>><?php _e('Allow uploads', 'ninjafirewall') ?></option>
					<option value="0"<?php selected( $uploads, 0 ) ?>><?php _e('Disallow uploads (default)', 'ninjafirewall') ?></option>
				</select>
				<p id="sanitize-fn"<?php if (! $uploads) { echo ' style="color:#bbbbbb;"'; }?>>
					<label><input type="checkbox" onclick='return sanitise_fn(this);' name="nfw_options[sanitise_fn]"<?php checked( $sanitise_fn, 1 ); disabled( $uploads, 0 ) ?> id="san">&nbsp;<?php _e('Sanitise filenames', 'ninjafirewall') ?> (<?php _e('substitution character:', 'ninjafirewall') ?></label> <input id="subs" maxlength="1" size="1" value="<?php echo $substitute ?>" name="nfw_options[substitute]" type="text" <?php disabled( $uploads, 0 ) ?>/> )
 				</p>
			</td>
		</tr>
	</table>

	<br />

	<?php
	if ( @strpos( $nfw_options['wp_dir'], 'wp-admin' ) !== FALSE ) {
		$wp_admin = 1;
	} else {
		$wp_admin = 0;
	}
	if ( @strpos( $nfw_options['wp_dir'], 'wp-includes' ) !== FALSE ) {
		$wp_inc = 1;
	} else {
		$wp_inc = 0;
	}
	if ( @strpos( $nfw_options['wp_dir'], 'uploads' ) !== FALSE ) {
		$wp_upl = 1;
	} else {
		$wp_upl = 0;
	}
	if ( @strpos( $nfw_options['wp_dir'], 'cache' ) !== FALSE ) {
		$wp_cache = 1;
	} else {
		$wp_cache = 0;
	}
	if ( empty( $nfw_options['disallow_creation']) ) {
		$disallow_creation = 0;
	} else {
		$disallow_creation = 1;
	}
	if ( empty( $nfw_options['enum_archives']) ) {
		$enum_archives = 0;
	} else {
		$enum_archives = 1;
	}
	if ( empty( $nfw_options['enum_login']) ) {
		$enum_login = 0;
	} else {
		$enum_login = 1;
	}
	if ( empty( $nfw_options['enum_restapi']) ) {
		$enum_restapi = 0;
	} else {
		$enum_restapi = 1;
	}
	if ( empty( $nfw_options['no_restapi']) ) {
		$no_restapi = 0;
	} else {
		$no_restapi = 1;
	}
	if ( empty( $nfw_options['no_xmlrpc']) ) {
		$no_xmlrpc = 0;
	} else {
		$no_xmlrpc = 1;
	}
	if ( empty( $nfw_options['no_xmlrpc_multi']) ) {
		$no_xmlrpc_multi = 0;
	} else {
		$no_xmlrpc_multi = 1;
	}
	if ( empty( $nfw_options['no_xmlrpc_pingback']) ) {
		$no_xmlrpc_pingback = 0;
	} else {
		$no_xmlrpc_pingback = 1;
	}
	if ( empty( $nfw_options['no_post_themes']) ) {
		$no_post_themes = 0;
	} else {
		$no_post_themes = 1;
	}

	if ( empty( $nfw_options['force_ssl']) ) {
		$force_ssl = 0;
	} else {
		$force_ssl = 1;
	}
	if ( empty( $nfw_options['disallow_edit']) ) {
		$disallow_edit = 0;
	} else {
		$disallow_edit = 1;
	}
	if ( empty( $nfw_options['disallow_mods']) ) {
		$disallow_mods = 0;
	} else {
		$disallow_mods = 1;
	}

	$force_ssl_already_enabled = 0; $disallow_edit_already_enabled = 0;
	$disallow_mods_already_enabled = 0;
	if ( defined('DISALLOW_FILE_EDIT') && ! $disallow_edit ) {
		$disallow_edit_already_enabled = 1;
	}
	if ( defined('DISALLOW_FILE_MODS') && ! $disallow_mods ) {
		$disallow_mods_already_enabled = 1;
	}
	if ( defined('FORCE_SSL_ADMIN') && FORCE_SSL_ADMIN == true && ! $force_ssl ) {
		$force_ssl_already_enabled = 1;
	}
	?>
	<h3>WordPress</h3>
	<table class="form-table">
		<tr>
			<th scope="row"><?php
				_e('Block direct access to any PHP file located in one of these directories', 'ninjafirewall');
				if ( defined('NFW_WPWAF') ) {
					echo '<br /><font style="font-weight:400">' . $full_waf_msg . '</font>';
				}
			?></th>
			<td width="20">&nbsp;</td>
			<td align="left">
				<table class="form-table">
					<tr style="border: solid 1px #DFDFDF;">
						<td align="center" width="10"><input type="checkbox" name="nfw_options[wp_admin]" id="wp_01"<?php checked( $wp_admin, 1 ); disabled( $option_disabled, 1) ?>></td>
						<td>
						<label for="wp_01">
						<p><code>/wp-admin/css/*</code></p>
						<p><code>/wp-admin/images/*</code></p>
						<p><code>/wp-admin/includes/*</code></p>
						<p><code>/wp-admin/js/*</code></p>
						</label>
						</td>
					</tr>
					<tr style="border: solid 1px #DFDFDF;">
						<td align="center" width="10"><input type="checkbox" name="nfw_options[wp_inc]" id="wp_02"<?php checked( $wp_inc, 1 ); disabled( $option_disabled, 1) ?>></td>
						<td>
						<label for="wp_02">
						<p><code>/wp-includes/*.php</code></p>
						<p><code>/wp-includes/css/*</code></p>
						<p><code>/wp-includes/images/*</code></p>
						<p><code>/wp-includes/js/*</code></p>
						<p><code>/wp-includes/theme-compat/*</code></p>
						</label>
						<br />
						<span class="description"><?php _e('NinjaFirewall will not block access to the TinyMCE WYSIWYG editor even if this option is enabled.', 'ninjafirewall') ?></span>
						</td>
					</tr>
					<tr style="border: solid 1px #DFDFDF;">
						<td align="center" width="10"><input type="checkbox" name="nfw_options[wp_upl]" id="wp_03"<?php checked( $wp_upl, 1 ); disabled( $option_disabled, 1) ?>></td>
						<td><label for="wp_03">
							<p><code>/<?php echo basename(WP_CONTENT_DIR); ?>/uploads/*</code></p>
							<p><code>/<?php echo basename(WP_CONTENT_DIR); ?>/blogs.dir/*</code></p>
						</label></td>
					</tr>
					<tr style="border: solid 1px #DFDFDF;">
						<td align="center" style="vertical-align:top" width="10"><input type="checkbox" name="nfw_options[wp_cache]" id="wp_04"<?php checked( $wp_cache, 1 ); disabled( $option_disabled, 1) ?>></td>
						<td style="vertical-align:top"><label for="wp_04"><code>*/cache/*</code></label>
						<br />
						<br />
						<span class="description"><?php _e('Unless you have PHP scripts in a "/cache/" folder that need to be accessed by your visitors, we recommend to enable this option.', 'ninjafirewall') ?></span>
						</td>
					</tr>
				</table>
				<br />&nbsp;
			</td>
		</tr>
	</table>

	<?php
	if ( is_dir( WP_PLUGIN_DIR . '/jetpack' ) ) {
		$is_JetPack = '<p><img src="' . plugins_url() . '/ninjafirewall/images/glyphicons-warning.png">&nbsp;<span class="description">' . __('If you are using the Jetpack plugin, blocking <code>system.multicall</code> may prevent it from working correctly.', 'ninjafirewall') . '</span></p>';
	} else {
		$is_JetPack = '';
	}
	?>

	<table class="form-table">
		<tr>
			<th scope="row"><?php _e('User accounts', 'ninjafirewall') ?></th>
			<td width="20">&nbsp;</td>
			<td align="left">
				<p><label><input type="checkbox" name="nfw_options[disallow_creation]" value="1"<?php checked( $disallow_creation, 1 ) ?>>&nbsp;<?php _e('Block user accounts creation', 'ninjafirewall') ?></label></p>
				<span class="description"><?php _e('Do not enable this policy if you allow user registration.', 'ninjafirewall') ?></span>
			</td>
		</tr>

		<tr>
			<th scope="row"><?php _e('Protect against username enumeration', 'ninjafirewall') ?></th>
			<td width="20">&nbsp;</td>
			<td align="left">
				<p><label><input type="checkbox" name="nfw_options[enum_archives]" value="1"<?php checked( $enum_archives, 1 ) ?>>&nbsp;<?php _e('Through the author archives', 'ninjafirewall') ?></label></p>
				<p><label><input type="checkbox" name="nfw_options[enum_login]" value="1"<?php checked( $enum_login, 1 ) ?>>&nbsp;<?php _e('Through the login page', 'ninjafirewall') ?></label></p>
				<p><label><input type="checkbox" name="nfw_options[enum_restapi]" value="1"<?php checked( $enum_restapi, 1 ) ?>>&nbsp;<?php _e('Through the WordPress REST API', 'ninjafirewall') ?></label></p>
			</td>
		</tr>

		<?php
		global $wp_version;
		if ( version_compare( $wp_version, '4.7', '<' ) ) {
			$restapi_error = '1';
			$restapi_msg = '<p><img src="' . plugins_url() . '/ninjafirewall/images/glyphicons-warning.png">&nbsp;<span class="description">' . __('This feature is only available when running WordPress 4.7 or above.', 'ninjafirewall') . '</span></p>';
		} else {
			$restapi_msg = '';
			$restapi_error = 0;
		}
		?>
		<tr>
			<th scope="row"><?php _e('WordPress REST API', 'ninjafirewall') ?>*</th>
			<td width="20">&nbsp;</td>
			<td align="left">
				<p><label><input type="checkbox" name="nfw_options[no_restapi]" value="1"<?php checked( $no_restapi, 1 );disabled( $restapi_error, 1) ?>>&nbsp;<?php _e('Block any access to the API', 'ninjafirewall') ?></label></p>
				<?php echo $restapi_msg; ?>
			</td>
		</tr>

		<tr>
			<th scope="row"><?php _e('WordPress XML-RPC API', 'ninjafirewall') ?>*</th>
			<td width="20">&nbsp;</td>
			<td align="left">
				<p><label><input type="checkbox" name="nfw_options[no_xmlrpc]" value="1"<?php checked( $no_xmlrpc, 1 ) ?>>&nbsp;<?php _e('Block any access to the API', 'ninjafirewall') ?></label></p>
				<p><label><input type="checkbox" name="nfw_options[no_xmlrpc_multi]" value="1"<?php checked( $no_xmlrpc_multi, 1 ) ?>>&nbsp;<?php _e('Block <code>system.multicall</code> method', 'ninjafirewall') ?></label></p>
				<?php echo $is_JetPack; ?>
				<p><label><input type="checkbox" name="nfw_options[no_xmlrpc_pingback]" value="1"<?php checked( $no_xmlrpc_pingback, 1 ) ?>>&nbsp;<?php _e('Block Pingbacks', 'ninjafirewall') ?></label></p>
			</td>
		</tr>
	</table>

	<span class="description">*<?php _e('Disabling access to the REST or XML-RPC API may break some functionality on your blog, its themes or plugins.', 'ninjafirewall') ?></span>

	<table class="form-table">
		<tr valign="top">
			<th scope="row" style="vertical-align:top"><?php _e('Block <code>POST</code> requests in the themes folder', 'ninjafirewall') ?> <code>/<?php echo basename(WP_CONTENT_DIR); ?>/themes</code></th>
			<td width="20">&nbsp;</td>
			<td align="left" width="120" style="vertical-align:top">
				<label><input type="radio" name="nfw_options[no_post_themes]" value="1"<?php checked( $no_post_themes, 1 ); disabled( $option_disabled, 1) ?>>&nbsp;<?php _e('Yes', 'ninjafirewall') ?></label>
			</td>
			<td align="left" style="vertical-align:top">
				<label><input type="radio" name="nfw_options[no_post_themes]" value="0"<?php checked( $no_post_themes, 0 ); disabled( $option_disabled, 1) ?>>&nbsp;<?php _e('No (default)', 'ninjafirewall') ?></label>
				<?php
				if ( defined('NFW_WPWAF') ) {
					echo '<br />'. $full_waf_msg;
				}
			?>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><a name="builtinconstants"></a><?php _e('Force SSL for admin and logins', 'ninjafirewall') ?> <code><a href="http://codex.wordpress.org/Editing_wp-config.php#Require_SSL_for_Admin_and_Logins" target="_blank">FORCE_SSL_ADMIN</a></code></th>
			<td width="20">&nbsp;</td>
			<td align="left" width="120">
				<label><input type="radio" name="nfw_options[force_ssl]" value="1"<?php checked( $force_ssl, 1 ) ?> onclick="return ssl_warn();" <?php disabled( $force_ssl_already_enabled, 1 ) ?>>&nbsp;<?php _e('Yes', 'ninjafirewall') ?></label>
			</td>
			<td align="left">
				<label><input type="radio" id="ssl_0" name="nfw_options[force_ssl]" value="0"<?php checked( $force_ssl, 0 ) ?> <?php disabled( $force_ssl_already_enabled, 1 ) ?>>&nbsp;<?php _e('No (default)', 'ninjafirewall') ?></label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Disable the plugin and theme editor', 'ninjafirewall') ?> <code><a href="http://codex.wordpress.org/Editing_wp-config.php#Disable_the_Plugin_and_Theme_Editor" target="_blank">DISALLOW_FILE_EDIT</a></code></th>
			<td width="20">&nbsp;</td>
			<td align="left" width="120">
				<label><input type="radio" name="nfw_options[disallow_edit]" value="1"<?php checked( $disallow_edit, 1 ) ?> <?php disabled( $disallow_edit_already_enabled, 1 ) ?>>&nbsp;<?php _e('Yes', 'ninjafirewall') ?></label>
			</td>
			<td align="left">
				<label><input type="radio" name="nfw_options[disallow_edit]" value="0"<?php checked( $disallow_edit, 0 ) ?> <?php disabled( $disallow_edit_already_enabled, 1 ) ?>>&nbsp;<?php _e('No (default)', 'ninjafirewall') ?></label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Disable plugin and theme update/installation', 'ninjafirewall') ?> <code><a href="http://codex.wordpress.org/Editing_wp-config.php#Disable_Plugin_and_Theme_Update_and_Installation" target="_blank">DISALLOW_FILE_MODS</a></code></th>
			<td width="20">&nbsp;</td>
			<td align="left" width="120">
				<label><input type="radio" name="nfw_options[disallow_mods]" value="1"<?php checked( $disallow_mods, 1 ) ?> <?php disabled( $disallow_mods_already_enabled, 1 ) ?>>&nbsp;<?php _e('Yes', 'ninjafirewall') ?></label>
			</td>
			<td align="left">
				<label><input type="radio" name="nfw_options[disallow_mods]" value="0"<?php checked( $disallow_mods, 0 ) ?> <?php disabled( $disallow_mods_already_enabled, 1 ) ?>>&nbsp;<?php _e('No (default)', 'ninjafirewall') ?></label>
			</td>
		</tr>

	</table>
	<a name="donotblockadmin"></a>
	<br />
	<br />

	<?php
	if ( empty( $nfw_options['wl_admin']) ) {
		$wl_admin = 0;
	} elseif ( $nfw_options['wl_admin'] == 2 ) {
		$wl_admin = 2;
	} else {
		$wl_admin = 1;
	}
	?>
	<table class="form-table">
		<tr style="background-color:#F9F9F9;border: solid 1px #DFDFDF;">
			<th scope="row"><?php _e('Users Whitelist', 'ninjafirewall') ?></th>
			<td width="20">&nbsp;</td>
			<td align="left">
			<p><label><input type="radio" name="nfw_options[wl_admin]" value="1"<?php checked( $wl_admin, 1 ) ?>>&nbsp;<?php _e('Add the Administrator to the whitelist (default).', 'ninjafirewall') ?></label></p>
			<p><label><input type="radio" name="nfw_options[wl_admin]" value="2"<?php checked( $wl_admin, 2 ) ?>>&nbsp;<?php _e('Add all logged in users to the whitelist.', 'ninjafirewall') ?></label></p>
			<p><label><input type="radio" name="nfw_options[wl_admin]" value="0"<?php checked( $wl_admin, 0 ) ?>>&nbsp;<?php _e('Disable users whitelist.', 'ninjafirewall') ?></label></p>
			<p><span class="description"><?php _e('Note: This feature  does not apply to <code>FORCE_SSL_ADMIN</code>, <code>DISALLOW_FILE_EDIT</code> and <code>DISALLOW_FILE_MODS</code> options which, if enabled, are always enforced.', 'ninjafirewall') ?></span></p>
			</td>
		</tr>
	</table>

	</div>


	<?php
	// ==========================================================================
	// Intermediate options:
	?>
	<div id="intermediate-options" style="display:none">
	<?php
	if ( empty( $nfw_options['get_scan']) ) {
		$get_scan = 0;
	} else {
		$get_scan = 1;
	}
	if ( empty( $nfw_options['get_sanitise']) ) {
		$get_sanitise = 0;
	} else {
		$get_sanitise = 1;
	}
	?>
	<h3><?php _e('HTTP GET variable', 'ninjafirewall') ?></h3>
	<table class="form-table">
		<tr>
			<th scope="row"><?php _e('Scan <code>GET</code> variable', 'ninjafirewall') ?></th>
			<td width="20">&nbsp;</td>
			<td align="left" width="120">
				<label><input type="radio" name="nfw_options[get_scan]" value="1"<?php checked( $get_scan, 1 ) ?>>&nbsp;<?php _e('Yes (default)', 'ninjafirewall') ?></label>
			</td>
			<td align="left">
				<label><input type="radio" name="nfw_options[get_scan]" value="0"<?php checked( $get_scan, 0 ) ?>>&nbsp;<?php _e('No', 'ninjafirewall') ?></label>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php _e('Sanitise <code>GET</code> variable', 'ninjafirewall') ?></th>
			<td width="20">&nbsp;</td>
			<td align="left" width="120">
				<label><input type="radio" name="nfw_options[get_sanitise]" value="1"<?php checked( $get_sanitise, 1 ) ?>>&nbsp;<?php _e('Yes', 'ninjafirewall') ?></label>
			</td>
			<td align="left">
				<label><input type="radio" name="nfw_options[get_sanitise]" value="0"<?php checked( $get_sanitise, 0 ) ?>>&nbsp;<?php _e('No (default)', 'ninjafirewall') ?></label>
			</td>
		</tr>
	</table>

	<br /><br />

	<?php
	if ( empty( $nfw_options['post_scan']) ) {
		$post_scan = 0;
	} else {
		$post_scan = 1;
	}
	if ( empty( $nfw_options['post_sanitise']) ) {
		$post_sanitise = 0;
	} else {
		$post_sanitise = 1;
	}
	if ( empty( $nfw_options['post_b64']) ) {
		$post_b64 = 0;
	} else {
		$post_b64 = 1;
	}
	?>
	<h3><?php _e('HTTP POST variable', 'ninjafirewall') ?></h3>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><?php _e('Scan <code>POST</code> variable', 'ninjafirewall') ?></th>
			<td width="20">&nbsp;</td>
			<td align="left" width="120">
				<label><input type="radio" name="nfw_options[post_scan]" value="1"<?php checked( $post_scan, 1 ) ?>>&nbsp;<?php _e('Yes (default)', 'ninjafirewall') ?></label>
			</td>
			<td align="left">
				<label><input type="radio" name="nfw_options[post_scan]" value="0"<?php checked( $post_scan, 0 ) ?>>&nbsp;<?php _e('No', 'ninjafirewall') ?></label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Sanitise <code>POST</code> variable', 'ninjafirewall') ?></th>
			<td width="20">&nbsp;</td>
			<td align="left" width="120" style="vertical-align:top;">
				<label><input type="radio" name="nfw_options[post_sanitise]" value="1"<?php checked( $post_sanitise, 1 ) ?>>&nbsp;<?php _e('Yes', 'ninjafirewall') ?></label>
			</td>
			<td align="left">
				<label><input type="radio" name="nfw_options[post_sanitise]" value="0"<?php checked( $post_sanitise, 0 ) ?>>&nbsp;<?php _e('No (default)', 'ninjafirewall') ?></label><br /><span class="description">&nbsp;<?php _e('Do not enable this option unless you know what you are doing!', 'ninjafirewall') ?></span>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Decode Base64-encoded <code>POST</code> variable', 'ninjafirewall') ?></th>
			<td width="20">&nbsp;</td>
			<td align="left" width="120">
				<label><input type="radio" name="nfw_options[post_b64]" value="1"<?php checked( $post_b64, 1 ) ?>>&nbsp;<?php _e('Yes (default)', 'ninjafirewall') ?></label>
			</td>
			<td align="left">
				<label><input type="radio" name="nfw_options[post_b64]" value="0"<?php checked( $post_b64, 0 ) ?>>&nbsp;<?php _e('No', 'ninjafirewall') ?></label>
			</td>
		</tr>
	</table>

	<br /><br />

	<?php
	if ( empty( $nfw_options['request_sanitise']) ) {
		$request_sanitise = 0;
	} else {
		$request_sanitise = 1;
	}
	?>
	<h3><?php _e('HTTP REQUEST variable', 'ninjafirewall') ?></h3>
	<table class="form-table">
		<tr>
			<th scope="row"><?php _e('Sanitise <code>REQUEST</code> variable', 'ninjafirewall') ?></th>
			<td width="20">&nbsp;</td>
			<td align="left" width="120" style="vertical-align:top;">
				<label><input type="radio" name="nfw_options[request_sanitise]" value="1"<?php checked( $request_sanitise, 1 ) ?>>&nbsp;<?php _e('Yes', 'ninjafirewall') ?></label>
			</td>
			<td align="left">
				<label><input type="radio" name="nfw_options[request_sanitise]" value="0"<?php checked( $request_sanitise, 0 ) ?>>&nbsp;<?php _e('No (default)', 'ninjafirewall') ?></label><br /><span class="description">&nbsp;<?php _e('Do not enable this option unless you know what you are doing!', 'ninjafirewall') ?></span>
			</td>
		</tr>
	</table>

	<br /><br />

	<?php
	if ( empty( $nfw_options['cookies_scan']) ) {
		$cookies_scan = 0;
	} else {
		$cookies_scan = 1;
	}
	if ( empty( $nfw_options['cookies_sanitise']) ) {
		$cookies_sanitise = 0;
	} else {
		$cookies_sanitise = 1;
	}
	?>
	<h3><?php _e('Cookies', 'ninjafirewall') ?></h3>
	<table class="form-table">
		<tr>
			<th scope="row"><?php _e('Scan cookies', 'ninjafirewall') ?></th>
			<td width="20">&nbsp;</td>
			<td align="left" width="120">
				<label><input type="radio" name="nfw_options[cookies_scan]" value="1"<?php checked( $cookies_scan, 1 ) ?>>&nbsp;<?php _e('Yes (default)', 'ninjafirewall') ?></label>
			</td>
			<td align="left">
				<label><input type="radio" name="nfw_options[cookies_scan]" value="0"<?php checked( $cookies_scan, 0 ) ?>>&nbsp;<?php _e('No', 'ninjafirewall') ?></label>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php _e('Sanitise cookies', 'ninjafirewall') ?></th>
			<td width="20">&nbsp;</td>
			<td align="left" width="120">
				<label><input type="radio" name="nfw_options[cookies_sanitise]" value="1"<?php checked( $cookies_sanitise, 1 ) ?>>&nbsp;<?php _e('Yes', 'ninjafirewall') ?></label>
			</td>
			<td align="left">
				<label><input type="radio" name="nfw_options[cookies_sanitise]" value="0"<?php checked( $cookies_sanitise, 0 ) ?>>&nbsp;<?php _e('No (default)', 'ninjafirewall') ?></label>
			</td>
		</tr>
	</table>

	<br /><br />

	<?php
	if ( empty( $nfw_options['ua_scan']) ) {
		$ua_scan = 0;
	} else {
		$ua_scan = 1;
	}
	if ( empty( $nfw_options['ua_sanitise']) ) {
		$ua_sanitise = 0;
	} else {
		$ua_sanitise = 1;
	}


	if ( empty( $nfw_rules[NFW_SCAN_BOTS]['ena']) ) {
		$block_bots = 0;
	} else {
		$block_bots = 1;
	}
	?>
	<h3><?php _e('HTTP_USER_AGENT server variable', 'ninjafirewall') ?></h3>
	<table class="form-table">
		<tr>
			<th scope="row"><?php _e('Scan <code>HTTP_USER_AGENT</code>', 'ninjafirewall') ?></th>
			<td width="20">&nbsp;</td>
			<td align="left" width="120">
				<label><input type="radio" name="nfw_options[ua_scan]" value="1"<?php checked( $ua_scan, 1 ) ?>>&nbsp;<?php _e('Yes (default)', 'ninjafirewall') ?></label>
			</td>
			<td align="left">
				<label><input type="radio" name="nfw_options[ua_scan]" value="0"<?php checked( $ua_scan, 0 ) ?>>&nbsp;<?php _e('No', 'ninjafirewall') ?></label>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php _e('Sanitise <code>HTTP_USER_AGENT</code>', 'ninjafirewall') ?></th>
			<td width="20">&nbsp;</td>
			<td align="left" width="120">
				<label><input type="radio" name="nfw_options[ua_sanitise]" value="1"<?php checked( $ua_sanitise, 1 ) ?>>&nbsp;<?php _e('Yes (default)', 'ninjafirewall') ?></label>
			</td>
			<td align="left">
				<label><input type="radio" name="nfw_options[ua_sanitise]" value="0"<?php checked( $ua_sanitise, 0 ) ?>>&nbsp;<?php _e('No', 'ninjafirewall') ?></label>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php _e('Block suspicious bots/scanners', 'ninjafirewall') ?></th>
			<td width="20">&nbsp;</td>
			<td align="left" width="120">
				<label><input type="radio" name="nfw_rules[block_bots]" value="1"<?php checked( $block_bots, 1 ) ?>>&nbsp;<?php _e('Yes (default)', 'ninjafirewall') ?></label>
			</td>
			<td align="left">
				<label><input type="radio" name="nfw_rules[block_bots]" value="0"<?php checked( $block_bots, 0 ) ?>>&nbsp;<?php _e('No', 'ninjafirewall') ?></label>
			</td>
		</tr>
	</table>

	<br /><br />

	<?php
	if ( empty( $nfw_options['referer_scan']) ) {
		$referer_scan = 0;
	} else {
		$referer_scan = 1;
	}
	if ( empty( $nfw_options['referer_sanitise']) ) {
		$referer_sanitise = 0;
	} else {
		$referer_sanitise = 1;
	}
	if ( empty( $nfw_options['referer_post']) ) {
		$referer_post = 0;
	} else {
		$referer_post = 1;
	}
	?>
	<h3><?php _e('HTTP_REFERER server variable', 'ninjafirewall') ?></h3>
	<table class="form-table">
		<tr>
			<th scope="row"><?php _e('Scan <code>HTTP_REFERER</code>', 'ninjafirewall') ?></th>
			<td width="20">&nbsp;</td>
			<td align="left" width="120">
				<label><input type="radio" name="nfw_options[referer_scan]" value="1"<?php checked( $referer_scan, 1 ) ?>>&nbsp;<?php _e('Yes', 'ninjafirewall') ?></label>
			</td>
			<td align="left">
				<label><input type="radio" name="nfw_options[referer_scan]" value="0"<?php checked( $referer_scan, 0 ) ?>>&nbsp;<?php _e('No (default)', 'ninjafirewall') ?></label>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php _e('Sanitise <code>HTTP_REFERER</code>', 'ninjafirewall') ?></th>
			<td width="20">&nbsp;</td>
			<td align="left" width="120">
				<label><input type="radio" name="nfw_options[referer_sanitise]" value="1"<?php checked( $referer_sanitise, 1 ) ?>>&nbsp;<?php _e('Yes (default)', 'ninjafirewall') ?></label>
			</td>
			<td align="left">
				<label><input type="radio" name="nfw_options[referer_sanitise]" value="0"<?php checked( $referer_sanitise, 0 ) ?>>&nbsp;<?php _e('No', 'ninjafirewall') ?></label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Block <code>POST</code> requests that do not have an <code>HTTP_REFERER</code> header', 'ninjafirewall') ?></th>
			<td width="20">&nbsp;</td>
			<td align="left" width="120" style="vertical-align:top;">
				<label><input type="radio" name="nfw_options[referer_post]" value="1"<?php checked( $referer_post, 1 ) ?>>&nbsp;<?php _e('Yes', 'ninjafirewall') ?></label>
			</td>
			<td align="left" style="vertical-align:top;">
				<label><input type="radio" name="nfw_options[referer_post]" value="0"<?php checked( $referer_post, 0 ) ?>>&nbsp;<?php _e('No (default)', 'ninjafirewall') ?></label><br /><span class="description">&nbsp;<?php _e('Keep this option disabled if you are using scripts like Paypal IPN, WordPress WP-Cron etc', 'ninjafirewall') ?>.</span>
			</td>
		</tr>
	</table>

	<br /><br />

	<?php
	if ( empty( $nfw_rules[NFW_LOOPBACK]['ena']) ) {
		$no_localhost_ip = 0;
	} else {
		$no_localhost_ip = 1;
	}
	if ( empty( $nfw_options['no_host_ip']) ) {
		$no_host_ip = 0;
	} else {
		$no_host_ip = 1;
	}
	if ( empty( $nfw_options['allow_local_ip']) ) {
		$allow_local_ip = 0;
	} else {
		$allow_local_ip = 1;
	}
	?>
	<h3>IP</h3>
	<table class="form-table" border=0>
		<tr>
			<th scope="row"><?php _e('Block localhost IP in <code>GET/POST</code> request', 'ninjafirewall') ?></th>
			<td width="20">&nbsp;</td>
			<td align="left" width="120" style="vertical-align:top">
				<label><input type="radio" name="nfw_rules[no_localhost_ip]" value="1"<?php checked( $no_localhost_ip, 1 ) ?>>&nbsp;<?php _e('Yes (default)', 'ninjafirewall') ?></label>
			</td>
			<td align="left" style="vertical-align:top">
				<label><input type="radio" name="nfw_rules[no_localhost_ip]" value="0"<?php checked( $no_localhost_ip, 0 ) ?>>&nbsp;<?php _e('No', 'ninjafirewall') ?></label>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php _e('Block HTTP requests with an IP in the <code>HTTP_HOST</code> header', 'ninjafirewall') ?></th>
			<td width="20">&nbsp;</td>
			<td align="left" width="120" style="vertical-align:top">
				<label><input type="radio" name="nfw_options[no_host_ip]" value="1"<?php checked( $no_host_ip, 1 ) ?>>&nbsp;<?php _e('Yes', 'ninjafirewall') ?></label>
			</td>
			<td align="left" style="vertical-align:top">
				<label><input type="radio" name="nfw_options[no_host_ip]" value="0"<?php checked( $no_host_ip, 0 ) ?>>&nbsp;<?php _e('No (default)', 'ninjafirewall') ?></label>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php _e('Scan traffic coming from localhost and private IP address spaces', 'ninjafirewall') ?></th>
			<td width="20">&nbsp;</td>
			<td align="left" width="120" style="vertical-align:top">
				<label><input type="radio" name="nfw_options[allow_local_ip]" value="0"<?php checked( $allow_local_ip, 0 ) ?>>&nbsp;<?php _e('Yes (default)', 'ninjafirewall') ?></label>
				</td>
			<td align="left" style="vertical-align:top">
				<label><input type="radio" name="nfw_options[allow_local_ip]" value="1"<?php checked( $allow_local_ip, 1 ) ?>>&nbsp;<?php _e('No', 'ninjafirewall') ?></label>
			</td>
		</tr>
	</table>

	</div>

	<?php
	// ==========================================================================
	// Advanced options:
	?>
	<div id="advanced-options" style="display:none">

	<?php
	$err_msg = $err = '';
	$err_img = '<p><span class="description"><img src="' . plugins_url() . '/ninjafirewall/images/glyphicons-warning.png">&nbsp;';
	$msg = __('This option is disabled because the %s PHP function is not available on your server.', 'ninjafirewall');
	if (! function_exists('header_register_callback') ) {
		$err_msg = $err_img . sprintf($msg, '<code>header_register_callback()</code>') . '</span></p>';
		$err = 1;
	} elseif (! function_exists('headers_list') ) {
		$err_msg = $err_img . sprintf($msg, '<code>headers_list()</code>') . '</span></p>';
		$err = 1;
	} elseif (! function_exists('header_remove') ) {
		$err_msg = $err_img . sprintf($msg, '<code>header_remove()</code>') . '</span></p>';
		$err = 1;
	}
	if ( empty($nfw_options['response_headers']) || ! preg_match( '/^\d+$/', $nfw_options['response_headers'] ) || $err_msg ) {
		$nfw_options['response_headers'] = '000000000';
	}
	?>
	<h3><?php _e('HTTP response headers', 'ninjafirewall')  ?></h3>
	<table class="form-table">
		<tr>
			<th scope="row"><?php printf( __('Set %s to protect against MIME type confusion attacks', 'ninjafirewall'), '<a href="https://nintechnet.com/ninjafirewall/wp-edition/doc/#responseheaders" target="_blank">X-Content-Type-Options</a>') ?></th>
			<td width="20">&nbsp;</td>
			<td align="left" width="120">
				<label><input type="radio" name="nfw_options[x_content_type_options]" value="1"<?php checked( $nfw_options['response_headers'][1], 1 ); disabled($err, 1); ?>><?php echo $yes; ?></label>
			</td>
			<td align="left">
				<label><input type="radio" name="nfw_options[x_content_type_options]" value="0"<?php checked( $nfw_options['response_headers'][1], 0 ); disabled($err, 1); ?>><?php echo $no . $default; ?></label><?php echo $err_msg ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php printf( __('Set %s to protect against clickjacking attempts', 'ninjafirewall'), '<a href="https://nintechnet.com/ninjafirewall/wp-edition/doc/#responseheaders" target="_blank">X-Frame-Options</a>') ?></th>
			<td width="20">&nbsp;</td>
			<td align="left" width="120" style="vertical-align:top;">
				<p><label><input type="radio" name="nfw_options[x_frame_options]" value="1"<?php checked( $nfw_options['response_headers'][2], 1 ); disabled($err, 1); ?>><code>SAMEORIGIN</code></label></p>
				<p><label><input type="radio" name="nfw_options[x_frame_options]" value="2"<?php checked( $nfw_options['response_headers'][2], 2 ); disabled($err, 1); ?>><code>DENY</code></label></p>
			</td>
			<td align="left" style="vertical-align:top;"><p><label><input type="radio" name="nfw_options[x_frame_options]" value="0"<?php checked( $nfw_options['response_headers'][2], 0 ); disabled($err, 1); ?>><?php echo $no . $default; ?></label><?php echo $err_msg ?></p></td>
		</tr>
		<tr>
			<th scope="row"><?php printf( __("Set %s (IE/Edge, Chrome, Opera and Safari browsers)", 'ninjafirewall'), '<a href="https://nintechnet.com/ninjafirewall/wp-edition/doc/#responseheaders" target="_blank">X-XSS-Protection</a>') ?></th>
			<td width="20"></td>

			<td align="left" width="120">
				<p><label><input type="radio" name="nfw_options[x_xss_protection]" value="0"<?php checked( $nfw_options['response_headers'][3], 0 ); disabled($err, 1); ?> /><?php printf( __("Set to %s", "ninjafirewall"), '<code>0</code>' ) ?></label></p>
				<p><label><input type="radio" name="nfw_options[x_xss_protection]" value="2"<?php checked( $nfw_options['response_headers'][3], 2 ); disabled($err, 1); ?> /><?php printf( __("Set to %s", "ninjafirewall"), '<code>1</code>' ) ?></label></p>
			</td>
			<td align="left">
				<p><label><input type="radio" name="nfw_options[x_xss_protection]" value="1"<?php checked( $nfw_options['response_headers'][3], 1 ); disabled($err, 1); ?> /><?php printf( __("Set to %s", "ninjafirewall"), '<code>1; mode=block</code>' ); echo $default; ?></label></p>
				<p><label><input type="radio" name="nfw_options[x_xss_protection]" value="3"<?php checked( $nfw_options['response_headers'][3], 3 ); disabled($err, 1); ?> /><?php _e('No', 'ninjafirewall') ?></label></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php printf( __('Force %s flag on all cookies to mitigate XSS attacks', 'ninjafirewall'), '<a href="https://nintechnet.com/ninjafirewall/wp-edition/doc/#responseheaders" target="_blank">HttpOnly</a>') ?></th>
			<td width="20">&nbsp;</td>
			<td align="left" width="120" style="vertical-align:top;">
				<label><input type="radio" name="nfw_options[cookies_httponly]" value="1"<?php checked( $nfw_options['response_headers'][0], 1 ); disabled($err, 1); ?> >&nbsp;<?php echo $yes ?></label>
			</td>
			<td align="left" style="vertical-align:top;">
				<label><input type="radio" name="nfw_options[cookies_httponly]" value="0"<?php checked( $nfw_options['response_headers'][0], 0 ); disabled($err, 1); ?>>&nbsp;<?php echo $no . $default; ?></label><br /><span class="description"><?php _e('If your PHP scripts use cookies that need to be accessed from JavaScript, you should disable this option.', 'ninjafirewall') ?></span><?php echo $err_msg ?>
			</td>
		</tr>
		<?php
		if ($_SERVER['SERVER_PORT'] != 443 && ! $err && (! isset( $_SERVER['HTTP_X_FORWARDED_PROTO']) || $_SERVER['HTTP_X_FORWARDED_PROTO'] != 'https') ) {
			$hsts_err = 1;
			$hsts_msg = '<br /><img src="' . plugins_url() . '/ninjafirewall/images/glyphicons-warning.png">&nbsp;<span class="description">' . __('HSTS headers can only be set when you are accessing your site over HTTPS.', 'ninjafirewall') . '</span>';
		} else {
			$hsts_msg = '';
			$hsts_err = 0;
		}
		?>
		<tr>
			<th scope="row"><?php printf( __('Set %s (HSTS) to enforce secure connections to the server', 'ninjafirewall'), '<a href="https://nintechnet.com/ninjafirewall/wp-edition/doc/#responseheaders" target="_blank">Strict-Transport-Security</a>') ?></th>
			<td width="20">&nbsp;</td>
			<td align="left" width="120" style="vertical-align:top;">
				<p><label><input type="radio" name="nfw_options[strict_transport]" value="1"<?php checked( $nfw_options['response_headers'][4], 1 ); disabled($hsts_err, 1); ?>><?php _e('1 month', 'ninjafirewall') ?></label></p>
				<p><label><input type="radio" name="nfw_options[strict_transport]" value="2"<?php checked( $nfw_options['response_headers'][4], 2 ); disabled($hsts_err, 1); ?>><?php _e('6 months', 'ninjafirewall') ?></label></p>
				<p><label><input type="radio" name="nfw_options[strict_transport]" value="3"<?php checked( $nfw_options['response_headers'][4], 3 ); disabled($hsts_err, 1); ?>><?php _e('1 year', 'ninjafirewall') ?></label></p>
				<br />
				<label><input type="checkbox" name="nfw_options[strict_transport_sub]" value="1"<?php checked( $nfw_options['response_headers'][5], 1 ); disabled($hsts_err, 1); ?>><?php _e('Apply to subdomains', 'ninjafirewall') ?></label>
			</td>
			<td align="left" style="vertical-align:top;">
				<p><label><input type="radio" name="nfw_options[strict_transport]" value="0"<?php checked( $nfw_options['response_headers'][4], 0 ); disabled($hsts_err, 1); ?>><?php echo $no . $default; ?></label></p>
				<p><label><input type="radio" name="nfw_options[strict_transport]" value="4"<?php checked( $nfw_options['response_headers'][4], 4 ); disabled($hsts_err, 1); ?>><?php _e('Set <code>max-age</code> to 0', 'ninjafirewall'); ?></label><?php echo $err_msg ?></p>
				<?php echo $hsts_msg; ?>
			</td>
		</tr>

		<?php
			if (! isset( $nfw_options['csp_frontend_data'] ) ) {
				$nfw_options['csp_frontend_data'] = '';
			}
			if (! isset( $nfw_options['csp_backend_data'] ) ) {
				$nfw_options['csp_backend_data'] = nf_sub_policies_csp();
			}
			if (! isset( $nfw_options['response_headers'][6] ) ) {
				$nfw_options['response_headers'][6] = 0;
			}
			if (! isset( $nfw_options['response_headers'][7] ) ) {
				$nfw_options['response_headers'][7] = 0;
			}
		?>
		<tr>
			<th scope="row"><?php printf( __('Set %s for the website frontend', 'ninjafirewall'), '<a href="https://nintechnet.com/ninjafirewall/wp-edition/doc/#responseheaders" target="_blank">Content-Security-Policy</a>') ?></th>
			<td width="20">&nbsp;</td>
			<td align="left" width="120" style="vertical-align:top;">
				<p><label><input type="radio" onclick="csp_onoff(1, 'csp_frontend')" name="nfw_options[csp_frontend]" value="1"<?php checked( $nfw_options['response_headers'][6], 1 ); disabled($err, 1); ?>><?php _e('Yes', 'ninjafirewall') ?></label></p>
				<p><label><input type="radio" onclick="csp_onoff(0, 'csp_frontend')" name="nfw_options[csp_frontend]" value="0"<?php checked( $nfw_options['response_headers'][6], 0 ); disabled($err, 1); ?>><?php _e('No (default)', 'ninjafirewall') ?></label></p>
			</td>
			<td align="left" style="vertical-align:top;">
				<textarea autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" name="nfw_options[csp_frontend_data]" id="csp_frontend" class="large-text code" rows="4"<?php __checked_selected_helper($err, 1, true, 'readonly'); __checked_selected_helper($nfw_options['response_headers'][6], 0, true, 'readonly') ?>><?php echo htmlspecialchars( $nfw_options['csp_frontend_data'] ) ?></textarea>
				<span class="description"><?php _e('This CSP header will apply to the website frontend only.', 'ninjafirewall') ?></span>
				<?php echo $err_msg ?>
			</td>
		</tr>

		<tr>
			<th scope="row"><?php printf( __('Set %s for the WordPress admin dashboard', 'ninjafirewall'), '<a href="https://nintechnet.com/ninjafirewall/wp-edition/doc/#responseheaders" target="_blank">Content-Security-Policy</a>') ?></th>
			<td width="20">&nbsp;</td>
			<td align="left" width="120" style="vertical-align:top;">
				<p><label><input type="radio" onclick="csp_onoff(1, 'csp_backend')" name="nfw_options[csp_backend]" value="1"<?php checked( $nfw_options['response_headers'][7], 1 ); disabled($err, 1); ?>><?php _e('Yes', 'ninjafirewall') ?></label></p>
				<p><label><input type="radio" onclick="csp_onoff(0, 'csp_backend')" name="nfw_options[csp_backend]" value="0"<?php checked( $nfw_options['response_headers'][7], 0 ); disabled($err, 1); ?>><?php _e('No (default)', 'ninjafirewall') ?></label></p>
			</td>
			<td align="left" style="vertical-align:top;">
				<textarea autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" name="nfw_options[csp_backend_data]" id="csp_backend" class="large-text code" rows="4"<?php __checked_selected_helper($err, 1, true, 'readonly'); __checked_selected_helper($nfw_options['response_headers'][7], 0, true, 'readonly') ?>><?php echo htmlspecialchars( $nfw_options['csp_backend_data'] ) ?></textarea>
				<span class="description"><?php _e('This CSP header will apply to the WordPress admin dashboard only.', 'ninjafirewall') ?></span>
				<?php echo $err_msg ?>
			</td>
		</tr>

		<?php
		if (! isset( $nfw_options['response_headers'][8] ) ) {
			$nfw_options['response_headers'][8] = 0;
		}
		if ( empty( $nfw_options['referrer_policy_enabled'] ) ) {
			$nfw_options['referrer_policy_enabled'] = 0;
		} else {
			$nfw_options['referrer_policy_enabled'] = 1;
		}
		?>
		<tr>
			<th scope="row"><?php printf( __("Set %s (Chrome, Opera and Firefox browsers)", 'ninjafirewall'), '<a href="https://nintechnet.com/ninjafirewall/wp-edition/doc/#responseheaders" target="_blank">Referrer-Policy</a>') ?></th>
			<td width="20"></td>
			<td align="left" width="120" style="vertical-align:top;">

				<p><label><input onclick="document.getElementById('rp_select').disabled=false" type="radio" name="nfw_options[referrer_policy_enabled]" value="1"<?php checked($nfw_options['referrer_policy_enabled'], 1) ?> /><?php _e('Yes', 'ninjafirewall') ?></label></p>
				<p><label><input onclick="document.getElementById('rp_select').disabled=true" type="radio" name="nfw_options[referrer_policy_enabled]" value="0"<?php checked($nfw_options['referrer_policy_enabled'], 0) ?> /><?php _e('No (default)', 'ninjafirewall') ?></label></p>
			</td>
			<td align="left" style="vertical-align:top;">
				<select id="rp_select" name="nfw_options[referrer_policy]"<?php disabled($nfw_options['referrer_policy_enabled'], 0) ?>>
					<option value="1"<?php selected($nfw_options['response_headers'][8], 1) ?>>no-referrer</option>
					<option value="2"<?php selected($nfw_options['response_headers'][8], 2) ?>>no-referrer-when-downgrade</option>
					<option value="3"<?php selected($nfw_options['response_headers'][8], 3) ?>>origin</option>
					<option value="4"<?php selected($nfw_options['response_headers'][8], 4) ?>>origin-when-cross-origin</option>
					<option value="5"<?php selected($nfw_options['response_headers'][8], 5) ?>>strict-origin</option>
					<option value="6"<?php selected($nfw_options['response_headers'][8], 6) ?>>strict-origin-when-cross-origin</option>
					<option value="7"<?php selected($nfw_options['response_headers'][8], 7) ?>>same-origin</option>
					<option value="8"<?php selected($nfw_options['response_headers'][8], 8) ?>>unsafe-url</option>
				</select>
			</td>
		</tr>
	</table>

	<br /><br />

	<?php
	if ( empty( $nfw_rules[NFW_WRAPPERS]['ena']) ) {
		$php_wrappers = 0;
	} else {
		$php_wrappers = 1;
	}
	if ( empty( $nfw_options['php_errors']) ) {
		$php_errors = 0;
	} else {
		$php_errors = 1;
	}
	if ( empty( $nfw_options['php_self']) ) {
		$php_self = 0;
	} else {
		$php_self = 1;
	}
	if ( empty( $nfw_options['php_path_t']) ) {
		$php_path_t = 0;
	} else {
		$php_path_t = 1;
	}
	if ( empty( $nfw_options['php_path_i']) ) {
		$php_path_i = 0;
	} else {
		$php_path_i = 1;
	}
	?>
	<h3>PHP</h3>
	<table class="form-table">
		<tr>
			<th scope="row"><?php _e('Block PHP built-in wrappers in <code>GET</code>, <code>POST</code>, <code>HTTP_USER_AGENT</code>, <code>HTTP_REFERER</code> and cookies', 'ninjafirewall') ?></th>
			<td width="20">&nbsp;</td>
			<td align="left" width="120" style="vertical-align:top">
				<label><input type="radio" name="nfw_rules[php_wrappers]" value="1"<?php checked( $php_wrappers, 1 ) ?>>&nbsp;<?php _e('Yes (default)', 'ninjafirewall') ?></label>
			</td>
			<td align="left" style="vertical-align:top">
				<label><input type="radio" name="nfw_rules[php_wrappers]" value="0"<?php checked( $php_wrappers, 0 ) ?>>&nbsp;<?php _e('No', 'ninjafirewall') ?></label>
			</td>
		</tr>

		<?php
		if (! empty( $nfw_rules[NFW_OBJECTS]['ena'] ) ) {
			if ( strpos( $nfw_rules[NFW_OBJECTS]['cha'][1]['whe'], 'GET' )  !== FALSE) {
				$NFW_OBJECTS_GET = ' checked="checked"';
			} else {
				$NFW_OBJECTS_GET = '';
			}
			if ( strpos( $nfw_rules[NFW_OBJECTS]['cha'][1]['whe'], 'POST' )  !== FALSE) {
				$NFW_OBJECTS_POST = ' checked="checked"';
			} else {
				$NFW_OBJECTS_POST = '';
			}
			if ( strpos( $nfw_rules[NFW_OBJECTS]['cha'][1]['whe'], 'COOKIE' )  !== FALSE) {
				$NFW_OBJECTS_COOKIE = ' checked="checked"';
			} else {
				$NFW_OBJECTS_COOKIE = '';
			}
			if ( strpos( $nfw_rules[NFW_OBJECTS]['cha'][1]['whe'], 'HTTP_USER_AGENT' )  !== FALSE) {
				$NFW_OBJECTS_HTTP_USER_AGENT = ' checked="checked"';
			} else {
				$NFW_OBJECTS_HTTP_USER_AGENT = '';
			}
			if ( strpos( $nfw_rules[NFW_OBJECTS]['cha'][1]['whe'], 'HTTP_REFERER' )  !== FALSE) {
				$NFW_OBJECTS_HTTP_REFERER = ' checked="checked"';
			} else {
				$NFW_OBJECTS_HTTP_REFERER = '';
			}
		} else {
			$NFW_OBJECTS_GET = ''; $NFW_OBJECTS_POST = ''; $NFW_OBJECTS_COOKIE = '';
			$NFW_OBJECTS_HTTP_USER_AGENT = ''; $NFW_OBJECTS_HTTP_REFERER = '';
		}
		?>
		<tr>
			<th scope="row"><?php _e('Block serialized PHP objects in the following global variables', 'ninjafirewall') ?></th>
			<td width="20">&nbsp;</td>
			<td align="left" width="120" style="vertical-align:top">
				<p><label><input type="checkbox" name="nfw_rules[php_objects_get]" value="1"<?php echo $NFW_OBJECTS_GET ?>><code>GET</code><?php echo $default ?></label><p>
				<p><label><input type="checkbox" name="nfw_rules[php_objects_post]" value="1"<?php echo $NFW_OBJECTS_POST ?>><code>POST</code><?php echo $default ?></label><p>
				<p><label><input type="checkbox" name="nfw_rules[php_objects_cookie]" value="1"<?php echo $NFW_OBJECTS_COOKIE ?>><code>COOKIE</code></label><p>
			</td>
			<td align="left" style="vertical-align:top">
				<p><label><input type="checkbox" name="nfw_rules[php_objects_http_user_agent]" value="1"<?php echo $NFW_OBJECTS_HTTP_USER_AGENT ?>><code>HTTP_USER_AGENT</code><?php echo $default ?></label><p>
				<p><label><input type="checkbox" name="nfw_rules[php_objects_http_referer]" value="1"<?php echo $NFW_OBJECTS_HTTP_REFERER ?>><code>HTTP_REFERER</code><?php echo $default ?></label><p>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php _e('Hide PHP notice and error messages', 'ninjafirewall') ?></th>
			<td width="20">&nbsp;</td>
			<td align="left" width="120">
				<label><input type="radio" name="nfw_options[php_errors]" value="1"<?php checked( $php_errors, 1 ) ?>>&nbsp;<?php _e('Yes (default)', 'ninjafirewall') ?></label>
			</td>
			<td align="left">
				<label><input type="radio" name="nfw_options[php_errors]" value="0"<?php checked( $php_errors, 0 ) ?>>&nbsp;<?php _e('No', 'ninjafirewall') ?></label>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php _e('Sanitise <code>PHP_SELF</code>', 'ninjafirewall') ?></th>
			<td width="20">&nbsp;</td>
			<td align="left" width="120">
				<label><input type="radio" name="nfw_options[php_self]" value="1"<?php checked( $php_self, 1 ) ?>>&nbsp;<?php _e('Yes (default)', 'ninjafirewall') ?></label>
			</td>
			<td align="left">
				<label><input type="radio" name="nfw_options[php_self]" value="0"<?php checked( $php_self, 0 ) ?>>&nbsp;<?php _e('No', 'ninjafirewall') ?></label>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php _e('Sanitise <code>PATH_TRANSLATED</code>', 'ninjafirewall') ?></th>
			<td width="20">&nbsp;</td>
			<td align="left" width="120">
				<label><input type="radio" name="nfw_options[php_path_t]" value="1"<?php checked( $php_path_t, 1 ) ?>>&nbsp;<?php _e('Yes (default)', 'ninjafirewall') ?></label>
			</td>
			<td align="left">
				<label><input type="radio" name="nfw_options[php_path_t]" value="0"<?php checked( $php_path_t, 0 ) ?>>&nbsp;<?php _e('No', 'ninjafirewall') ?></label>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php _e('Sanitise <code>PATH_INFO</code>', 'ninjafirewall') ?></th>
			<td width="20">&nbsp;</td>
			<td align="left" width="120">
				<label><input type="radio" name="nfw_options[php_path_i]" value="1"<?php checked( $php_path_i, 1 ) ?>>&nbsp;<?php _e('Yes (default)', 'ninjafirewall') ?></label>
			</td>
			<td align="left">
				<label><input type="radio" name="nfw_options[php_path_i]" value="0"<?php checked( $php_path_i, 0 ) ?>>&nbsp;<?php _e('No', 'ninjafirewall') ?></label>
			</td>
		</tr>
	</table>

	<br /><br />

	<?php

	if ( strlen( $_SERVER['DOCUMENT_ROOT'] ) < 5 ) {
		$nfw_rules[NFW_DOC_ROOT]['ena'] = 0;
		$greyed = 'style="color:#bbbbbb"';
		$disabled = 'disabled ';
		$disabled_msg = '<br /><span class="description">&nbsp;' .
							__('This option is not compatible with your actual configuration.', 'ninjafirewall') .
							'</span>';
	} else {
		$greyed = '';
		$disabled = '';
		$disabled_msg = '';
	}

	if ( empty( $nfw_rules[NFW_DOC_ROOT]['ena']) ) {
		$block_doc_root = 0;
	} else {
		$block_doc_root = 1;
	}
	if ( empty( $nfw_rules[NFW_NULL_BYTE]['ena']) ) {
		$block_null_byte = 0;
	} else {
		$block_null_byte = 1;
	}
	if ( empty( $nfw_rules[NFW_ASCII_CTRL]['ena']) ) {
		$block_ctrl_chars = 0;
	} else {
		$block_ctrl_chars = 1;
	}
	?>
	<h3><?php _e('Various', 'ninjafirewall') ?></h3>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><?php _e('Block the <code>DOCUMENT_ROOT</code> server variable in HTTP request', 'ninjafirewall') ?></th>
			<td width="20">&nbsp;</td>
			<td align="left" width="120">
				<label <?php echo $greyed ?>><input type="radio" name="nfw_rules[block_doc_root]" value="1"<?php checked( $block_doc_root, 1 ) ?>>&nbsp;<?php _e('Yes (default)', 'ninjafirewall') ?></label>
			</td>
			<td align="left">
				<label <?php echo $greyed ?>><input <?php echo $disabled ?>type="radio" name="nfw_rules[block_doc_root]" value="0"<?php checked( $block_doc_root, 0 ) ?>>&nbsp;<?php _e('No', 'ninjafirewall') ?></label><?php echo $disabled_msg ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php _e('Block ASCII character 0x00 (NULL byte)', 'ninjafirewall') ?></th>
			<td width="20">&nbsp;</td>
			<td align="left" width="120">
				<label><input type="radio" name="nfw_rules[block_null_byte]" value="1"<?php checked( $block_null_byte, 1 ) ?>>&nbsp;<?php _e('Yes (default)', 'ninjafirewall') ?></label>
			</td>
			<td align="left">
				<label><input type="radio" name="nfw_rules[block_null_byte]" value="0"<?php checked( $block_null_byte, 0 ) ?>>&nbsp;<?php _e('No', 'ninjafirewall') ?></label>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php _e('Block ASCII control characters 1 to 8 and 14 to 31', 'ninjafirewall') ?></th>
			<td width="20">&nbsp;</td>
			<td align="left">
				<label><input type="radio" name="nfw_rules[block_ctrl_chars]" value="1"<?php checked( $block_ctrl_chars, 1 ) ?>>&nbsp;<?php _e('Yes', 'ninjafirewall') ?></label>
			</td>
			<td align="left">
				<label><input type="radio" name="nfw_rules[block_ctrl_chars]" value="0"<?php checked( $block_ctrl_chars, 0 ) ?>>&nbsp;<?php _e('No (default)', 'ninjafirewall') ?></label>
			</td>
		</tr>
	</table>

	</div>

	<br />
	<br />

	<input class="button-primary" type="submit" name="Save" value="<?php _e('Save Firewall Policies', 'ninjafirewall') ?>" />
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input class="button-secondary" type="submit" name="Default" value="<?php _e('Restore Default Values', 'ninjafirewall') ?>" onclick="return restore();" />
	</form>
</div>

<?php
}

/* ------------------------------------------------------------------ */			// s2:h1

function nf_sub_policies_save() {

	nf_not_allowed( 'block', __LINE__ );

	$nfw_options = nfw_get_option( 'nfw_options' );
	$nfw_rules = nfw_get_option( 'nfw_rules' );

	if ( (isset( $_POST['nfw_options']['scan_protocol'])) &&
		( preg_match( '/^[123]$/', $_POST['nfw_options']['scan_protocol'])) ) {
			$nfw_options['scan_protocol'] = $_POST['nfw_options']['scan_protocol'];
	} else {
		$nfw_options['scan_protocol'] = 3;
	}

	if ( empty( $_POST['nfw_options']['uploads']) ) {
		$nfw_options['uploads'] = 0;
	} else {
		$nfw_options['uploads'] = 1;
	}

	if ( (isset( $_POST['nfw_options']['sanitise_fn']) ) && ( $nfw_options['uploads'] == 1) ) {
		$nfw_options['sanitise_fn'] = 1;
	} else {
		$nfw_options['sanitise_fn'] = 0;
	}
	// Substitution character:
	// Don't allow the '/' character:
	if ( empty( $_POST['nfw_options']['substitute'] ) || strlen( $_POST['nfw_options']['substitute'] ) > 1 || $_POST['nfw_options']['substitute'] == '/' ) {
		$nfw_options['substitute'] = 'X';
	} else {
		$nfw_options['substitute'] = $_POST['nfw_options']['substitute'];
	}


	if ( empty( $_POST['nfw_options']['get_scan']) ) {
		$nfw_options['get_scan'] = 0;
	} else {
		$nfw_options['get_scan'] = 1;
	}
	if ( empty( $_POST['nfw_options']['get_sanitise']) ) {
		$nfw_options['get_sanitise'] = 0;
	} else {
		$nfw_options['get_sanitise'] = 1;
	}


	if ( empty( $_POST['nfw_options']['post_scan']) ) {
		$nfw_options['post_scan'] = 0;
	} else {
		$nfw_options['post_scan'] = 1;
	}
	if ( empty( $_POST['nfw_options']['post_sanitise']) ) {
		$nfw_options['post_sanitise'] = 0;
	} else {
		$nfw_options['post_sanitise'] = 1;
	}
	if ( empty( $_POST['nfw_options']['post_b64']) ) {
		$nfw_options['post_b64'] = 0;
	} else {
		$nfw_options['post_b64'] = 1;
	}


	if ( empty( $_POST['nfw_options']['request_sanitise']) ) {
		$nfw_options['request_sanitise'] = 0;
	} else {
		$nfw_options['request_sanitise'] = 1;
	}


	if ( function_exists('header_register_callback') && function_exists('headers_list') && function_exists('header_remove') ) {
		$nfw_options['response_headers'] = '000000000';
		$nfw_options['csp_frontend_data'] = '';
		$nfw_options['csp_backend_data'] = '';
		if ( empty( $_POST['nfw_options']['x_content_type_options']) ) {
			$nfw_options['response_headers'][1] = 0;
		} else {
			$nfw_options['response_headers'][1] = 1;
		}
		if ( empty( $_POST['nfw_options']['x_frame_options']) ) {
			$nfw_options['response_headers'][2] = 0;
		} elseif ( $_POST['nfw_options']['x_frame_options'] == 1) {
			$nfw_options['response_headers'][2] = 1;
		} else {
			$nfw_options['response_headers'][2] = 2;
		}
		// XSS filter:
		// 	0 = 0
		// 	1 = 1; mode=block
		// 	2 = 1
		// 	3 = unset
		if ( empty( $_POST['nfw_options']['x_xss_protection'] ) ) {
			$nfw_options['response_headers'][3] = 0;
		} elseif ( $_POST['nfw_options']['x_xss_protection'] == 1 ) {
			$nfw_options['response_headers'][3] = 1;
		} elseif ( $_POST['nfw_options']['x_xss_protection'] == 2 ) {
			$nfw_options['response_headers'][3] = 2;
		} else {
			$nfw_options['response_headers'][3] = 3;
		}

		if ( empty( $_POST['nfw_options']['cookies_httponly']) ) {
			$nfw_options['response_headers'][0] = 0;
		} else {
			$nfw_options['response_headers'][0] = 1;
		}
		if (! isset( $_POST['nfw_options']['strict_transport_sub']) ) {
			$nfw_options['response_headers'][5] = 0;
		} else {
			$nfw_options['response_headers'][5] = 1;
		}
		if ( empty( $_POST['nfw_options']['strict_transport']) ) {
			$nfw_options['response_headers'][4] = 0;
			$nfw_options['response_headers'][5] = 0;
		} elseif ( $_POST['nfw_options']['strict_transport'] == 1) {
			$nfw_options['response_headers'][4] = 1;
		} elseif ( $_POST['nfw_options']['strict_transport'] == 2) {
			$nfw_options['response_headers'][4] = 2;
		} elseif ( $_POST['nfw_options']['strict_transport'] == 3) {
			$nfw_options['response_headers'][4] = 3;
		} else {
			$nfw_options['response_headers'][4] = 4;
		}
		$nfw_options['csp_frontend_data'] = stripslashes( str_replace( array( '<', '>', "\x0a", "\x0d", '%', '$', '&') , '', $_POST['nfw_options']['csp_frontend_data'] ) );
		if ( empty( $_POST['nfw_options']['csp_frontend']) || empty( $nfw_options['csp_frontend_data'] ) ) {
			$nfw_options['response_headers'][6] = 0;
		} else {
			$nfw_options['response_headers'][6] = 1;
		}
		$nfw_options['csp_backend_data'] = stripslashes( str_replace( array( '<', '>', "\x0a", "\x0d", '%', '$', '&') , '', $_POST['nfw_options']['csp_backend_data'] ) );
		if ( empty( $_POST['nfw_options']['csp_backend']) || empty( $nfw_options['csp_backend_data'] ) ) {
			$nfw_options['response_headers'][7] = 0;
		} else {
			$nfw_options['response_headers'][7] = 1;
		}
		if ( empty( $_POST['nfw_options']['referrer_policy_enabled'] ) ) {
			$nfw_options['referrer_policy_enabled'] = 0;
			$_POST['nfw_options']['referrer_policy'] = 0;
		} else {
			$nfw_options['referrer_policy_enabled'] = 1;
		}

		if ( empty( $_POST['nfw_options']['referrer_policy'] ) || ! preg_match('/^[1-8]$/', $_POST['nfw_options']['referrer_policy'] ) ) {
			$nfw_options['response_headers'][8] = 0;
			$nfw_options['referrer_policy_enabled'] = 0;
		} else {
			$nfw_options['response_headers'][8] = (int)$_POST['nfw_options']['referrer_policy'];
		}
	}


	if ( empty( $_POST['nfw_options']['cookies_scan']) ) {
		$nfw_options['cookies_scan'] = 0;
	} else {
		$nfw_options['cookies_scan'] = 1;
	}
	if ( empty( $_POST['nfw_options']['cookies_sanitise']) ) {
		$nfw_options['cookies_sanitise'] = 0;
	} else {
		$nfw_options['cookies_sanitise'] = 1;
	}


	if ( empty( $_POST['nfw_options']['ua_scan']) ) {
		$nfw_options['ua_scan'] = 0;
	} else {
		$nfw_options['ua_scan'] = 1;
	}
	if ( empty( $_POST['nfw_options']['ua_sanitise']) ) {
		$nfw_options['ua_sanitise'] = 0;
	} else {
		$nfw_options['ua_sanitise'] = 1;
	}


	if ( empty( $_POST['nfw_options']['referer_scan']) ) {
		$nfw_options['referer_scan'] = 0;
	} else {
		$nfw_options['referer_scan'] = 1;
	}
	if ( empty( $_POST['nfw_options']['referer_sanitise']) ) {
		$nfw_options['referer_sanitise'] = 0;
	} else {
		$nfw_options['referer_sanitise'] = 1;
	}
	if ( empty( $_POST['nfw_options']['referer_post']) ) {
		$nfw_options['referer_post'] = 0;
	} else {
		$nfw_options['referer_post'] = 1;
	}


	if ( empty( $_POST['nfw_options']['no_host_ip']) ) {
		$nfw_options['no_host_ip'] = 0;
	} else {
		$nfw_options['no_host_ip'] = 1;
	}
	if ( empty( $_POST['nfw_options']['allow_local_ip']) ) {
		$nfw_options['allow_local_ip'] = 0;
	} else {
		$nfw_options['allow_local_ip'] = 1;
	}


	if ( empty( $_POST['nfw_options']['php_errors']) ) {
		$nfw_options['php_errors'] = 0;
	} else {
		$nfw_options['php_errors'] = 1;
	}

	if ( empty( $_POST['nfw_options']['php_self']) ) {
		$nfw_options['php_self'] = 0;
	} else {
		$nfw_options['php_self'] = 1;
	}
	if ( empty( $_POST['nfw_options']['php_path_t']) ) {
		$nfw_options['php_path_t'] = 0;
	} else {
		$nfw_options['php_path_t'] = 1;
	}
	if ( empty( $_POST['nfw_options']['php_path_i']) ) {
		$nfw_options['php_path_i'] = 0;
	} else {
		$nfw_options['php_path_i'] = 1;
	}

	$nfw_options['wp_dir'] = $tmp = '';
	if ( isset( $_POST['nfw_options']['wp_admin']) ) {
		$tmp .= '/wp-admin/(?:css|images|includes|js)/|';
	}
	if ( isset( $_POST['nfw_options']['wp_inc']) ) {
		$tmp .= '/wp-includes/(?:(?:css|images|js(?!/tinymce/wp-tinymce\.php)|theme-compat)/|[^/]+\.php)|';
	}
	if ( isset( $_POST['nfw_options']['wp_upl']) ) {
		$tmp .= '/' . basename(WP_CONTENT_DIR) .'/(?:uploads|blogs\.dir)/|';
	}
	if ( isset( $_POST['nfw_options']['wp_cache']) ) {
		$tmp .= '/cache/|';
	}
	if ( $tmp ) {
		$nfw_options['wp_dir'] = rtrim( $tmp, '|' );
	}

	if (! isset( $_POST['nfw_options']['disallow_creation']) ) {
		$nfw_options['disallow_creation'] = 0;
	} else {
		$nfw_options['disallow_creation'] = 1;
	}
	if (! isset( $_POST['nfw_options']['enum_archives']) ) {
		$nfw_options['enum_archives'] = 0;
	} else {
		$nfw_options['enum_archives'] = 1;
	}
	if (! isset( $_POST['nfw_options']['enum_login']) ) {
		$nfw_options['enum_login'] = 0;
	} else {
		$nfw_options['enum_login'] = 1;
	}
	if (! isset( $_POST['nfw_options']['enum_restapi']) ) {
		$nfw_options['enum_restapi'] = 0;
	} else {
		$nfw_options['enum_restapi'] = 1;
	}
	if (! isset( $_POST['nfw_options']['no_restapi']) ) {
		$nfw_options['no_restapi'] = 0;
	} else {
		$nfw_options['no_restapi'] = 1;
	}


	if ( empty( $_POST['nfw_options']['no_xmlrpc']) ) {
		$nfw_options['no_xmlrpc'] = 0;
	} else {
		$nfw_options['no_xmlrpc'] = 1;
		$_POST['nfw_options']['no_xmlrpc_multi'] = 0;
		$_POST['nfw_options']['no_xmlrpc_pingback'] = 0;
	}
	if ( empty( $_POST['nfw_options']['no_xmlrpc_multi']) ) {
		$nfw_options['no_xmlrpc_multi'] = 0;
	} else {
		$nfw_options['no_xmlrpc_multi'] = 1;
	}
	if ( empty( $_POST['nfw_options']['no_xmlrpc_pingback']) ) {
		$nfw_options['no_xmlrpc_pingback'] = 0;
	} else {
		$nfw_options['no_xmlrpc_pingback'] = 1;
	}

	if ( empty( $_POST['nfw_options']['no_post_themes']) ) {
		$nfw_options['no_post_themes'] = 0;
	} else {
		$nfw_options['no_post_themes'] = '/'. basename(WP_CONTENT_DIR) .'/themes/';
	}

	if ( empty( $_POST['nfw_options']['force_ssl']) ) {
		$nfw_options['force_ssl'] = 0;
	} else {
		$nfw_options['force_ssl'] = 1;
	}

	if ( empty( $_POST['nfw_options']['disallow_edit']) ) {
		$nfw_options['disallow_edit'] = 0;
	} else {
		$nfw_options['disallow_edit'] = 1;
	}

	if ( empty( $_POST['nfw_options']['disallow_mods']) ) {
		$nfw_options['disallow_mods'] = 0;
	} else {
		$nfw_options['disallow_mods'] = 1;
	}


	if ( empty( $_POST['nfw_options']['wl_admin']) ) {
		$nfw_options['wl_admin'] = 0;
		if ( isset( $_SESSION['nfw_goodguy']) ) {
			unset( $_SESSION['nfw_goodguy']);
		}
	} else {
		if ( $_POST['nfw_options']['wl_admin'] == 2 ) {
			$nfw_options['wl_admin'] = 2;
		} else {
			$nfw_options['wl_admin'] = 1;
		}
		$_SESSION['nfw_goodguy'] = $nfw_options['wl_admin'];
	}


	if ( empty( $_POST['nfw_rules']['block_null_byte']) ) {
		$nfw_rules[NFW_NULL_BYTE]['ena'] = 0;
	} else {
		$nfw_rules[NFW_NULL_BYTE]['ena'] = 1;
	}
	if ( empty( $_POST['nfw_rules']['block_bots']) ) {
		$nfw_rules[NFW_SCAN_BOTS]['ena'] = 0;
	} else {
		$nfw_rules[NFW_SCAN_BOTS]['ena'] = 1;
	}
	if ( empty( $_POST['nfw_rules']['block_ctrl_chars']) ) {
		$nfw_rules[NFW_ASCII_CTRL]['ena'] = 0;
	} else {
		$nfw_rules[NFW_ASCII_CTRL]['ena'] = 1;
	}


	if ( empty( $_POST['nfw_rules']['block_doc_root']) ) {
		$nfw_rules[NFW_DOC_ROOT]['ena'] = 0;
	} else {

		if ( strlen( $_SERVER['DOCUMENT_ROOT'] ) > 5 ) {
			$nfw_rules[NFW_DOC_ROOT]['cha'][1]['wha'] = str_replace( '/', '/[./]*', $_SERVER['DOCUMENT_ROOT'] );
			$nfw_rules[NFW_DOC_ROOT]['ena']	= 1;
		} elseif ( strlen( getenv( 'DOCUMENT_ROOT' ) ) > 5 ) {
			$nfw_rules[NFW_DOC_ROOT]['cha'][1]['wha'] = str_replace( '/', '/[./]*', getenv( 'DOCUMENT_ROOT' ) );
			$nfw_rules[NFW_DOC_ROOT]['ena']	= 1;
		} else {
			$nfw_rules[NFW_DOC_ROOT]['ena']	= 0;
		}
	}


	if ( empty( $_POST['nfw_rules']['php_wrappers']) ) {
		$nfw_rules[NFW_WRAPPERS]['ena'] = 0;
	} else {
		$nfw_rules[NFW_WRAPPERS]['ena'] = 1;
	}


	$nfw_objects = '';
	if (! empty( $_POST['nfw_rules']['php_objects_get'] ) ) {
		$nfw_objects .= "GET|";
	}
	if (! empty( $_POST['nfw_rules']['php_objects_post'] ) ) {
		$nfw_objects .= "POST|";
	}
	if (! empty( $_POST['nfw_rules']['php_objects_cookie'] ) ) {
		$nfw_objects .= "COOKIE|";
	}
	if (! empty( $_POST['nfw_rules']['php_objects_http_user_agent'] ) ) {
		$nfw_objects .= "SERVER:HTTP_USER_AGENT|";
	}
	if (! empty( $_POST['nfw_rules']['php_objects_http_referer'] ) ) {
		$nfw_objects .= "SERVER:HTTP_REFERER|";
	}
	if (! empty( $nfw_objects ) ) {
		$nfw_objects = rtrim( $nfw_objects, '|' );
		$nfw_rules[NFW_OBJECTS]['ena'] = 1;
	} else {
		// Disable rule:
		$nfw_rules[NFW_OBJECTS]['ena'] = 0;
	}
	$nfw_rules[NFW_OBJECTS]['cha'][1]['whe'] = $nfw_objects;


	if ( empty( $_POST['nfw_rules']['no_localhost_ip']) ) {
		$nfw_rules[NFW_LOOPBACK]['ena'] = 0;
	} else {
		$nfw_rules[NFW_LOOPBACK]['ena'] = 1;
	}

	nfw_update_option( 'nfw_options', $nfw_options );
	nfw_update_option( 'nfw_rules', $nfw_rules );

}

/* ------------------------------------------------------------------ */

function nf_sub_policies_csp() {
	return "script-src 'self' 'unsafe-inline' 'unsafe-eval' *.videopress.com *.google.com *.wp.com; style-src 'self' 'unsafe-inline' *.googleapis.com *.google.com *.jquery.com; connect-src 'self'; media-src 'self' *.youtube.com *.w.org; child-src 'self' *.videopress.com *.google.com; object-src 'self'; form-action 'self'; img-src 'self' *.gravatar.com *.wp.com *.w.org *.cldup.com woocommerce.com data:;";
}

/* ------------------------------------------------------------------ */

function nf_sub_policies_default() {

	nf_not_allowed( 'block', __LINE__ );

	$nfw_options = nfw_get_option( 'nfw_options' );
	$nfw_rules = nfw_get_option( 'nfw_rules' );

	$nfw_options['scan_protocol']		= 3;
	$nfw_options['uploads']				= 0;
	$nfw_options['sanitise_fn']		= 0;
	$nfw_options['substitute'] 		= 'X';
	$nfw_options['get_scan']			= 1;
	$nfw_options['get_sanitise']		= 0;
	$nfw_options['post_scan']			= 1;
	$nfw_options['post_sanitise']		= 0;
	$nfw_options['request_sanitise'] = 0;
	if ( function_exists('header_register_callback') && function_exists('headers_list') && function_exists('header_remove') ) {
		$nfw_options['response_headers'] = '000100000';
		$nfw_options['referrer_policy_enabled'] = 0;
		$nfw_options['csp_backend_data'] = nf_sub_policies_csp();
		$nfw_options['csp_frontend_data'] = '';
	}
	$nfw_options['cookies_scan']		= 1;
	$nfw_options['cookies_sanitise']	= 0;
	$nfw_options['ua_scan']				= 1;
	$nfw_options['ua_sanitise']		= 1;
	$nfw_options['referer_scan']		= 0;
	$nfw_options['referer_sanitise']	= 1;
	$nfw_options['referer_post']		= 0;
	$nfw_options['no_host_ip']			= 0;
	$nfw_options['allow_local_ip']	= 0;
	$nfw_options['php_errors']			= 1;
	$nfw_options['php_self']			= 1;
	$nfw_options['php_path_t']			= 1;
	$nfw_options['php_path_i']			= 1;
	$nfw_options['wp_dir'] 				= '/wp-admin/(?:css|images|includes|js)/|' .
		'/wp-includes/(?:(?:css|images|js(?!/tinymce/wp-tinymce\.php)|theme-compat)/|[^/]+\.php)|' .
		'/'. basename(WP_CONTENT_DIR) .'/(?:uploads|blogs\.dir)/';
	$nfw_options['disallow_creation']= 0;
	$nfw_options['enum_archives']		= 0;
	$nfw_options['enum_login']			= 0;
	$nfw_options['enum_restapi']		= 0;
	$nfw_options['no_restapi']			= 0;
	$nfw_options['no_xmlrpc']			= 0;
	$nfw_options['no_xmlrpc_multi']	= 0;
	$nfw_options['no_xmlrpc_pingback']= 0;
	$nfw_options['no_post_themes']	= 0;
	$nfw_options['force_ssl'] 			= 0;
	$nfw_options['disallow_edit'] 	= 0;
	$nfw_options['disallow_mods'] 	= 0;
	$nfw_options['post_b64']			= 1;
	$nfw_options['wl_admin']			= 1;
	$_SESSION['nfw_goodguy'] 			= true;

	$nfw_rules[NFW_SCAN_BOTS]['ena']	= 1;
	$nfw_rules[NFW_LOOPBACK]['ena']	= 1;
	$nfw_rules[NFW_WRAPPERS]['ena']	= 1;

	$nfw_rules[NFW_OBJECTS]['ena'] = 1;
	$nfw_rules[NFW_OBJECTS]['cha'][1]['whe'] = 'GET|POST|SERVER:HTTP_USER_AGENT|SERVER:HTTP_REFERER';

	if ( strlen( $_SERVER['DOCUMENT_ROOT'] ) > 5 ) {
		$nfw_rules[NFW_DOC_ROOT]['cha'][1]['wha'] = str_replace( '/', '/[./]*', $_SERVER['DOCUMENT_ROOT'] );
		$nfw_rules[NFW_DOC_ROOT]['ena'] = 1;
	} elseif ( strlen( getenv( 'DOCUMENT_ROOT' ) ) > 5 ) {
		$nfw_rules[NFW_DOC_ROOT]['cha'][1]['wha'] = str_replace( '/', '/[./]*', getenv( 'DOCUMENT_ROOT' ) );
		$nfw_rules[NFW_DOC_ROOT]['ena'] = 1;
	} else {
		$nfw_rules[NFW_DOC_ROOT]['ena']  = 0;
	}

	$nfw_rules[NFW_NULL_BYTE]['ena']  = 1;
	$nfw_rules[NFW_ASCII_CTRL]['ena'] = 0;

	nfw_update_option( 'nfw_options', $nfw_options);
	nfw_update_option( 'nfw_rules', $nfw_rules);

}

/* ------------------------------------------------------------------ */

function nf_sub_fileguard() {

	nf_not_allowed( 'block', __LINE__ );

	$nfw_options = nfw_get_option( 'nfw_options' );

	?>
	<script>
	function toggle_table(off) {
		if ( off == 1 ) {
			jQuery("#fg_table").slideDown();
		} else if ( off == 2 ) {
			jQuery("#fg_table").slideUp();
		}
		return;
	}
	function is_number(id) {
		var e = document.getElementById(id);
		if (! e.value ) { return }
		if (! /^[1-9][0-9]?$/.test(e.value) ) {
			alert("<?php echo esc_js( __('Please enter a number from 1 to 99.', 'ninjafirewall') ) ?>");
			e.value = e.value.substring(0, e.value.length-1);
		}
	}
	function check_fields() {
		if (! document.nfwfilefuard.elements["nfw_options[fg_mtime]"]){
			alert("<?php echo esc_js( __('Please enter a number from 1 to 99.', 'ninjafirewall') ) ?>");
			return false;
		}
		return true;
	}
	</script>

	<div class="wrap">
		<div style="width:33px;height:33px;background-image:url(<?php echo plugins_url() ?>/ninjafirewall/images/ninjafirewall_32.png);background-repeat:no-repeat;background-position:0 0;margin:7px 5px 0 0;float:left;"></div>
		<h1><?php _e('File Guard', 'ninjafirewall') ?></h1>
	<?php
	if ( defined('NFW_WPWAF') ) {
		?>
		<div class="notice-warning notice is-dismissible"><p><?php printf( __('You are running NinjaFirewall in <i>WordPress WAF</i> mode. The %s feature will be limited to a few WordPress files only (e.g., index.php, wp-login.php, xmlrpc.php, admin-ajax.php, wp-load.php etc). If you want it to apply to any PHP script, you will need to run NinjaFirewall in %s mode.', 'ninjafirewall'), 'File Guard', '<a href="https://blog.nintechnet.com/full_waf-vs-wordpress_waf/">Full WAF</a>') ?></p></div>
		<?php
	}

	if (! is_writable( NFW_LOG_DIR . '/nfwlog/cache/') ) {
		echo '<div class="error notice is-dismissible"><p>' .
			sprintf( __('The cache directory %s is not writable. Please change its permissions (0777 or equivalent).', 'ninjafirewall'), '('. htmlspecialchars(NFW_LOG_DIR) . '/nfwlog/cache/)' ) . '</p></div>';
	}

	if ( isset( $_POST['nfw_options']) ) {
		if ( empty($_POST['nfwnonce']) || ! wp_verify_nonce($_POST['nfwnonce'], 'fileguard_save') ) {
			wp_nonce_ays('fileguard_save');
		}
		nf_sub_fileguard_save();
		$nfw_options = nfw_get_option( 'nfw_options' );
		echo '<div class="updated notice is-dismissible"><p>' . __('Your changes have been saved.', 'ninjafirewall') .'</p></div>';
	}

	if ( empty($nfw_options['fg_enable']) ) {
		$nfw_options['fg_enable'] = 0;
	} else {
		$nfw_options['fg_enable'] = 1;
	}
	if ( empty($nfw_options['fg_mtime']) || ! preg_match('/^[1-9][0-9]?$/', $nfw_options['fg_mtime']) ) {
		$nfw_options['fg_mtime'] = 10;
	}
	if ( empty($nfw_options['fg_exclude']) ) {
		$fg_exclude = '';
	} else {
		$tmp = str_replace('|', ',', $nfw_options['fg_exclude']);
		$fg_exclude = preg_replace( '/\\\([`.\\/\\\+*?\[^\]$(){}=!<>:-])/', '$1', $tmp );
	}
	?>
	<br />
	<form method="post" name="nfwfilefuard" onSubmit="return check_fields();">
		<?php wp_nonce_field('fileguard_save', 'nfwnonce', 0); ?>
		<table class="form-table">
			<tr style="background-color:#F9F9F9;border: solid 1px #DFDFDF;">
				<th scope="row"><?php _e('Enable File Guard', 'ninjafirewall') ?></th>
				<td align="left">
				<label><input type="radio" id="fgenable" name="nfw_options[fg_enable]" value="1"<?php checked($nfw_options['fg_enable'], 1) ?> onclick="toggle_table(1);">&nbsp;<?php _e('Yes (recommended)', 'ninjafirewall') ?></label>
				</td>
				<td align="left">
				<label><input type="radio" name="nfw_options[fg_enable]" value="0"<?php checked($nfw_options['fg_enable'], 0) ?> onclick="toggle_table(2);">&nbsp;<?php _e('No', 'ninjafirewall') ?></label>
				</td>
			</tr>
		</table>

		<br />

		<div id="fg_table"<?php echo $nfw_options['fg_enable'] == 1 ? '' : ' style="display:none"' ?>>
			<table class="form-table" border="0">
				<tr valign="top">
					<th scope="row"><?php _e('Real-time detection', 'ninjafirewall') ?></th>
					<td align="left">
					<?php
						printf( __('Monitor file activity and send an alert when someone is accessing a PHP script that was modified or created less than %s hour(s) ago.', 'ninjafirewall'), '<input maxlength="2" size="2" value="'. $nfw_options['fg_mtime'] .'" name="nfw_options[fg_mtime]" id="mtime" onkeyup="is_number(\'mtime\')" class="small-text" type="number" />');
					?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Exclude the following files/folders (optional)', 'ninjafirewall') ?></th>
					<td align="left"><input class="large-text" type="text" maxlength="255" name="nfw_options[fg_exclude]" value="<?php echo htmlspecialchars( $fg_exclude ); ?>" placeholder="<?php _e('e.g.,', 'ninjafirewall') ?> /foo/bar/cache/ <?php _e('or', 'ninjafirewall') ?> /cache/" /><br /><span class="description"><?php _e('Full or partial case-sensitive string(s), max. 255 characters. Multiple values must be comma-separated', 'ninjafirewall') ?> (<code>,</code>).</span></td>
				</tr>
			</table>
		</div>
		<br />
		<input class="button-primary" type="submit" name="Save" value="<?php _e('Save File Guard options', 'ninjafirewall') ?>" />
	</form>
	</div>
<?php

}

/* ------------------------------------------------------------------ */

function nf_sub_fileguard_save() {

	nf_not_allowed( 'block', __LINE__ );

	$nfw_options = nfw_get_option( 'nfw_options' );

	if ( empty($_POST['nfw_options']['fg_enable']) ) {
		$nfw_options['fg_enable'] = 0;
	} else {
		$nfw_options['fg_enable'] = $_POST['nfw_options']['fg_enable'];
	}

	if ( empty($_POST['nfw_options']['fg_mtime']) || ! preg_match('/^[1-9][0-9]?$/', $_POST['nfw_options']['fg_mtime']) ) {
		$nfw_options['fg_mtime'] = 10;
	} else {
		$nfw_options['fg_mtime'] = $_POST['nfw_options']['fg_mtime'];
	}

	if ( empty($_POST['nfw_options']['fg_exclude']) || strlen($_POST['nfw_options']['fg_exclude']) > 255 ) {
		$nfw_options['fg_exclude'] = '';
	} else {
		$exclude = '';
		$fg_exclude =  explode(',', $_POST['nfw_options']['fg_exclude'] );
		foreach ($fg_exclude as $path) {
			if ( $path ) {
				$path = str_replace( array(' ', '\\', '|'), '', $path);
				$exclude .= preg_quote( rtrim($path, ','), '`') . '|';
			}
		}
		$nfw_options['fg_exclude'] = rtrim($exclude, '|');
	}

	nfw_update_option( 'nfw_options', $nfw_options );

}
/* ------------------------------------------------------------------ */

function nf_sub_network() {


	if (! current_user_can( 'manage_network' ) ) {
		die( '<br /><br /><br /><div class="error notice is-dismissible"><p>' .
			sprintf( __('You are not allowed to perform this task (%s).', 'ninjafirewall'), __LINE__) .
			'</p></div>' );
	}

	$nfw_options = nfw_get_option( 'nfw_options' );

	echo '
<div class="wrap">
	<div style="width:33px;height:33px;background-image:url( ' . plugins_url() . '/ninjafirewall/images/ninjafirewall_32.png);background-repeat:no-repeat;background-position:0 0;margin:7px 5px 0 0;float:left;"></div>
	<h1>' . __('Network', 'ninjafirewall') . '</h1>';
	if (! is_multisite() ) {
		echo '<div class="updated notice is-dismissible"><p>' . __('You do not have a multisite network.', 'ninjafirewall') . '</p></div></div>';
		return;
	}

	if ( isset( $_POST['nfw_options']) ) {
		if ( empty($_POST['nfwnonce']) || ! wp_verify_nonce($_POST['nfwnonce'], 'network_save') ) {
			wp_nonce_ays('network_save');
		}
		if ( $_POST['nfw_options']['nt_show_status'] == 2 ) {
			$nfw_options['nt_show_status'] = 2;
		} else {
			$nfw_options['nt_show_status'] = 1;
		}
		nfw_update_option( 'nfw_options', $nfw_options );
		echo '<div class="updated notice is-dismissible"><p>' . __('Your changes have been saved.', 'ninjafirewall') . '</p></div>';
		$nfw_options = nfw_get_option( 'nfw_options' );
	}

	if ( empty($nfw_options['nt_show_status']) ) {
		$nfw_options['nt_show_status'] = 1;
	}
?>
<form method="post" name="nfwnetwork">
<?php wp_nonce_field('network_save', 'nfwnonce', 0); ?>
<h3><?php _e('NinjaFirewall Status', 'ninjafirewall') ?></h3>
	<table class="form-table">
		<tr>
			<th scope="row"><?php _e('Display NinjaFirewall status icon in the admin bar of all sites in the network', 'ninjafirewall') ?></th>
			<td align="left" width="200"><label><input type="radio" name="nfw_options[nt_show_status]" value="1"<?php echo $nfw_options['nt_show_status'] != 2 ? ' checked' : '' ?>>&nbsp;<?php _e('Yes (default)', 'ninjafirewall') ?></label></td>
			<td align="left"><label><input type="radio" name="nfw_options[nt_show_status]" value="2"<?php echo $nfw_options['nt_show_status'] == 2 ? ' checked' : '' ?>>&nbsp;<?php _e('No', 'ninjafirewall') ?></label></td>
		</tr>
	</table>

	<br />
	<br />
	<input class="button-primary" type="submit" name="Save" value="<?php _e('Save Network options', 'ninjafirewall') ?>" />
</form>
</div>
<?php
}

/* ------------------------------------------------------------------ */

function nf_sub_filecheck() {

	require plugin_dir_path(__FILE__) . 'lib/nf_sub_filecheck.php';

}

add_action('nfscanevent', 'nfscando');

function nfscando() {

	define('NFSCANDO', 1);
	nf_sub_filecheck();
}

/* ------------------------------------------------------------------ */

function nf_sub_malwarescan() {

	require plugin_dir_path(__FILE__) . 'lib/nf_sub_malwarescan.php';

}

/* ------------------------------------------------------------------ */

function nf_sub_event() {

	require plugin_dir_path(__FILE__) . 'lib/event_notifications.php';

}

add_action('init', 'nf_check_dbdata', 1);

add_action('nfdailyreport', 'nfdailyreportdo');

function nfdailyreportdo() {
	define('NFREPORTDO', 1);
	nf_sub_event();
}

/* ------------------------------------------------------------------ */

function nf_sub_log() {

	require plugin_dir_path(__FILE__) . 'lib/nf_sub_log.php';

}
/* ------------------------------------------------------------------ */

function nf_sub_live() {

	require plugin_dir_path(__FILE__) . 'lib/nf_sub_livelog.php';

}
/* ------------------------------------------------------------------ */

function nf_sub_loginprot() {

	require plugin_dir_path(__FILE__) . 'lib/login_protection.php';

}

/* ------------------------------------------------------------------ */

function nfw_log2($loginfo, $logdata, $loglevel, $ruleid) { // i18n


	$nfw_options = nfw_get_option( 'nfw_options' );

	if (! empty($nfw_options['debug']) ) {
		$num_incident = '0000000';
		$loglevel = 7;
		$http_ret_code = '200';
	} else {
		$num_incident = mt_rand(1000000, 9000000);
		$http_ret_code = $nfw_options['ret_code'];
	}
   if (strlen($logdata) > 200) { $logdata = mb_substr($logdata, 0, 200, 'utf-8') . '...'; }
	$res = '';
	$string = str_split($logdata);
	foreach ( $string as $char ) {
		if ( ( ord($char) < 32 ) || ( ord($char) > 126 ) ) {
			$res .= '%' . bin2hex($char);
		} else {
			$res .= $char;
		}
	}
	nfw_get_blogtimezone();

	$cur_month = date('Y-m');
	$stat_file = NFW_LOG_DIR . '/nfwlog/stats_' . $cur_month . '.php';
	$log_file  = NFW_LOG_DIR . '/nfwlog/firewall_' . $cur_month . '.php';

	if ( file_exists( $stat_file ) ) {
		$nfw_stat = file_get_contents( $stat_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
	} else {
		$nfw_stat = '0:0:0:0:0:0:0:0:0:0';
	}
	$nfw_stat_arr = explode(':', $nfw_stat . ':');
	++$nfw_stat_arr[$loglevel];
	@file_put_contents( $stat_file, $nfw_stat_arr[0] . ':' . $nfw_stat_arr[1] . ':' .
		$nfw_stat_arr[2] . ':' . $nfw_stat_arr[3] . ':' . $nfw_stat_arr[4] . ':' .
		$nfw_stat_arr[5] . ':' . $nfw_stat_arr[6] . ':' . $nfw_stat_arr[7] . ':' .
		$nfw_stat_arr[8] . ':' . $nfw_stat_arr[9], LOCK_EX );

	if ( $loglevel == 4 ) {
		$SCRIPT_NAME = '-';
		$REQUEST_METHOD = 'N/A';
		$REMOTE_ADDR = '0.0.0.0';
		$loglevel = 6;
	} else {
		$SCRIPT_NAME = $_SERVER['SCRIPT_NAME'];
		$REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
		$REMOTE_ADDR = NFW_REMOTE_ADDR;
	}

	if (! file_exists($log_file) ) {
		$tmp = '<?php exit; ?>' . "\n";
	} else {
		$tmp = '';
	}

	// Which encoding to use?
	if ( defined('NFW_LOG_ENCODING') ) {
		if ( NFW_LOG_ENCODING == 'b64' ) {
			$encoding = '[b64:' . base64_encode( $res ) . ']';
		} elseif ( NFW_LOG_ENCODING == 'none' ) {
			$encoding = '[' . $res . ']';
		} else {
			$unp = unpack('H*', $res);
			$encoding = '[hex:' . array_shift( $unp )  . ']';
		}
	} else {
		$unp = unpack('H*', $res);
		$encoding = '[hex:' . array_shift( $unp )  . ']';
	}

	@file_put_contents( $log_file,
      $tmp . '[' . time() . '] ' . '[0] ' .
      '[' . $_SERVER['SERVER_NAME'] . '] ' . '[#' . $num_incident . '] ' .
      '[' . $ruleid . '] ' .
      '[' . $loglevel . '] ' . '[' . nfw_anonymize_ip2( $REMOTE_ADDR ) . '] ' .
      '[' . $http_ret_code . '] ' . '[' . $REQUEST_METHOD . '] ' .
      '[' . $SCRIPT_NAME . '] ' . '[' . $loginfo . '] ' .
      $encoding . "\n", FILE_APPEND | LOCK_EX);
}

/* ------------------------------------------------------------------ */

function nfw_anonymize_ip2( $ip ) {

	$nfw_options = nfw_get_option( 'nfw_options' );

	if (! empty( $nfw_options['anon_ip'] ) && filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) {
		return substr( $ip, 0, -3 ) .'xxx';
	}

	return $ip;
}

/* ------------------------------------------------------------------ */

function nf_sub_editor() {

	// Rules Editor menu :
	require plugin_dir_path(__FILE__) . 'lib/rules_editor.php';

}

/* ------------------------------------------------------------------ */

function nf_sub_updates() {

	require plugin_dir_path(__FILE__) . 'lib/nf_sub_updates.php';

}

add_action('nfsecupdates', 'nfupdatesdo');

function nfupdatesdo() {
	define('NFUPDATESDO', 1);
	nf_sub_updates();
}

/* ------------------------------------------------------------------ */

function nf_sub_wplus() {

	require plugin_dir_path(__FILE__) . 'lib/nf_sub_wplus.php';
}

/* ------------------------------------------------------------------ */

function nf_sub_about() {

	require plugin_dir_path(__FILE__) . 'lib/nf_sub_about.php';

}
/* ------------------------------------------------------------------ */

function ninjafirewall_settings_link( $links ) {

	// Check if access is restricted to one or more specific admins
	// See: https://blog.nintechnet.com/restricting-access-to-ninjafirewall-wp-edition-settings/
	if ( nf_not_allowed( 0, __LINE__ ) ) {
		unset( $links );
		$links[] = __('Access Restricted', 'ninjafirewall');
		return $links;
	}

	if ( is_multisite() ) {	$net = 'network/'; } else { $net = '';	}

	$links[] = '<a href="'. get_admin_url(null, $net .'admin.php?page=NinjaFirewall') .'">'. __('Settings', 'ninjafirewall') .'</a>';
	$links[] = '<a href="https://nintechnet.com/ninjafirewall/wp-edition/?pricing" target="_blank">'. __('Upgrade to Premium', 'ninjafirewall'). '</a>';
	$links[] = '<a href="https://wordpress.org/support/view/plugin-reviews/ninjafirewall?rate=5#postform" target="_blank">'. __('Rate it!', 'ninjafirewall'). '</a>';
	unset( $links['edit'] );
   return $links;

}

if ( is_multisite() ) {
	add_filter( 'network_admin_plugin_action_links_' . plugin_basename(__FILE__), 'ninjafirewall_settings_link' );
} else {
	add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'ninjafirewall_settings_link' );
}

/* ------------------------------------------------------------------ */

function nfw_get_blogtimezone() {

	$tzstring = get_option( 'timezone_string' );
	if (! $tzstring ) {
		$tzstring = ini_get( 'date.timezone' );
		if (! $tzstring ) {
			$tzstring = 'UTC';
		}
	}
	date_default_timezone_set( $tzstring );
}
/* ------------------------------------------------------------------ */

function nfw_check_emailalert() {

	$nfw_options = nfw_get_option( 'nfw_options' );

	if ( ( is_multisite() ) && ( $nfw_options['alert_sa_only'] == 2 ) ) {
		$recipient = get_option('admin_email');
	} else {
		$recipient = $nfw_options['alert_email'];
	}

	global $current_user;
	$current_user = wp_get_current_user();

	list( $a_1, $a_2, $a_3 ) = explode( ':', NFW_ALERT . ':' );

	if (! empty($nfw_options['a_' . $a_1 . $a_2]) ) {
		$alert_array = array(
			'1' => array (
				'0' => __('Plugin', 'ninjafirewall'), '1' => __('uploaded', 'ninjafirewall'),	'2' => __('installed', 'ninjafirewall'), '3' => __('activated', 'ninjafirewall'),
				'4' => __('updated', 'ninjafirewall'), '5' => __('deactivated', 'ninjafirewall'), '6' => __('deleted', 'ninjafirewall'), 'label' => __('Name', 'ninjafirewall')
			),
			'2' => array (
				'0' => __('Theme', 'ninjafirewall'), '1' => __('uploaded', 'ninjafirewall'), '2' => __('installed', 'ninjafirewall'), '3' => __('activated', 'ninjafirewall'),
				'4' => __('deleted', 'ninjafirewall'), 'label' => __('Name', 'ninjafirewall')
			),
			'3' => array (
				'0' => 'WordPress', '1' => __('upgraded', 'ninjafirewall'),	'label' => __('Version', 'ninjafirewall')
			)
		);

		if ( substr_count($a_3, ',') ) {
			$alert_array[$a_1][0] .= 's';
			$alert_array[$a_1]['label'] .= 's';
		}
		$subject = __('[NinjaFirewall] Alert:', 'ninjafirewall') . ' ' . $alert_array[$a_1][0] . ' ' . $alert_array[$a_1][$a_2];
		if ( is_multisite() ) {
			$url = __('-Blog :', 'ninjafirewall') .' '. network_home_url('/') . "\n\n";
		} else {
			$url = __('-Blog :', 'ninjafirewall') .' '. home_url('/') . "\n\n";
		}
		$message = __('NinjaFirewall has detected the following activity on your account:', 'ninjafirewall') . "\n\n".
			'-' . $alert_array[$a_1][0] . ' ' . $alert_array[$a_1][$a_2] . "\n" .
			'-' . $alert_array[$a_1]['label'] . ' : ' . $a_3 . "\n\n" .
			__('-User :', 'ninjafirewall') .' '. $current_user->user_login . ' (' . $current_user->roles[0] . ")\n" .
			__('-IP   :', 'ninjafirewall') .' '. NFW_REMOTE_ADDR . "\n" .
			__('-Date :', 'ninjafirewall') .' '. ucfirst( date_i18n('F j, Y @ H:i:s O') ) ."\n" .
			$url .
			'NinjaFirewall (WP Edition) - https://nintechnet.com/' . "\n" .
			__('Support forum:', 'ninjafirewall') . ' http://wordpress.org/support/plugin/ninjafirewall' . "\n";
		wp_mail( $recipient, $subject, $message );

		if (! empty($nfw_options['a_41']) ) {
			nfw_log2(
				$alert_array[$a_1][0] . ' ' . $alert_array[$a_1][$a_2] . ' by '. $current_user->user_login,
				$alert_array[$a_1]['label'] . ': ' . $a_3,
				6,
				0
			);
		}

	}
}
/* ------------------------------------------------------------------ */

function nfw_dashboard_widgets() {

	require plugin_dir_path(__FILE__) . 'lib/dashboard_widget.php';

}

if ( is_multisite() ) {
	add_action( 'wp_network_dashboard_setup', 'nfw_dashboard_widgets' );
} else {
	add_action( 'wp_dashboard_setup', 'nfw_dashboard_widgets' );
}

/* ------------------------------------------------------------------ */

function nf_not_allowed($block, $line = 0) {

	if ( is_multisite() ) {
		if ( current_user_can('manage_network') ) {
			return false;
		}
	} else {
		if ( current_user_can('manage_options') &&
		     current_user_can('unfiltered_html') ) {
			// Check if that admin is allowed to use NinjaFirewall
			// (see NFW_ALLOWED_ADMIN at http://nin.link/nfwaa ):
			if ( defined('NFW_ALLOWED_ADMIN') ) {
				$current_user = wp_get_current_user();
				$admins = explode(',', NFW_ALLOWED_ADMIN);
				foreach ($admins as $admin) {
					if ( trim($admin) == $current_user->user_login ) {
						return false;
					}
				}
			} else {
				return false;
			}
		}
	}

	if ( $block ) {
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			// Format text for WP-CLI:
			WP_CLI::error(
				sprintf( __('You are not allowed to perform this task (%s).', 'ninjafirewall'), $line)
			);
		} else {
			die( '<br /><br /><br /><div class="error notice is-dismissible"><p>' .
				sprintf( __('You are not allowed to perform this task (%s).', 'ninjafirewall'), $line) .
				'</p></div>' );
		}
	}
	return true;
}

/* ------------------------------------------------------------------ */
// EOF //
