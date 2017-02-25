<?php
/*
Plugin Name: WPFSdata
Plugin URI: github.com/daoo/wpfsdata
Description: FSData mailing list management plugin.
Version: 2.0
Author: Daniel Oom
License: GPLv2

Copyright (C) 2017  Daniel Oom

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

defined('ABSPATH') or die();

define('WPFSDATA_DIR', realpath(ABSPATH . '../waff/groupmail'));
define('WPFSDATA_LOGDIR', realpath(ABSPATH . '../mailinglist'));

function wpfsdata_die_permissions() {
  wp_die('You do not have sufficient permission to access this page.');
}

WPFSDATA_DIR or wpfsdata_die_permissions();
WPFSDATA_LOGDIR or wpfsdata_die_permissions();

function wpfsdata_split($string) {
  return preg_split("/\r\n|\n|\r/", $string);
}

function wpfsdata_read_mailing_lists() {
  return array_values(array_diff(scandir(WPFSDATA_DIR), array('.', '..')));
}

function wpfsdata_list_path($list) {
  $path = realpath(WPFSDATA_DIR . '/' . $list);
  if (strpos($path, WPFSDATA_DIR) !== 0) {
    wpfsdata_die_permissions();
  }
  return $path;
}

function wpfsdata_log_path($list, $date, $user) {
  $path = WPFSDATA_LOGDIR . '/' . $list . '_' $user . '_' . $date . '.txt';
  if (strpos($path, WPFSDATA_LOGDIR) !== 0) {
    wpfsdata_die_permissions();
  }
  return $path;
}

function wpfsdata_read_addresses($list) {
  return file_get_contents(wpfsdata_list_path($list));
}

function wpfsdata_read_addresses_split($list) {
  return file(wpfsdata_list_path($list), FILE_IGNORE_NEW_LINES);
}

function wpfsdata_compute_diff($old, $new) {
  $removed = array_diff($old, $new);
  $added = array_diff($new, $old);
  return array($removed, $added);
}

function wpfsdata_write_addreses($list, $addresses) {
  assert(strlen($list) > 0);
  assert(strlen($addresses) >= 0);
  $date = date(DATE_ISO8601);
  $user = wp_get_current_user()->user_login;
  $log_path = wpfsdata_log_path($list, $date, $user);
  $log_handle = fopen($log_path, 'w');
  if ($log_handle) {
    fwrite($log_handle, $addresses);
    fclose($log_handle);

    $list_path = wpfsdata_list_path($list);
    $list_handle = fopen($list_path, 'w');
    if ($list_handle) {
      fwrite($list_handle, $addresses);
      fclose($list_handle);
      return true;
    }
  }
  return false;
}

function wpfsdata_edit_form($mailing_lists, $current_list, $current_addresses, $update_notice) {
  assert(count($mailing_lists) > 0);
  assert(strlen($current_list) > 0);
  assert(strlen($current_addresses) >= 0);
  require(plugin_dir_path(__FILE__) . 'wpfsdata/edit.php');
}

function wpfsdata_confirm_form($current_list, $removed_addresses, $added_addresses, $addresses) {
  assert(strlen($current_list) > 0);
  assert(count($removed_addresses) >= 0);
  assert(count($added_addresses) >= 0);
  assert(count($addresses) >= 0);
  require(plugin_dir_path(__FILE__) . 'wpfsdata/confirm.php');
}

function wpfsdata_page() {
  if (!current_user_can('manage_options')) {
    wpfsdata_die_permissions();
  }

  if (isset($_POST['edit'])) {
    $list = urldecode($_POST['list']);
    $old = wpfsdata_read_addresses_split($list);
    $new = wpfsdata_split(stripslashes($_POST['addresses']));
    list($removed, $added) = wpfsdata_compute_diff($old, $new);
    wpfsdata_confirm_form($list, $removed, $added, $new);
  } else {
    $mailing_lists = wpfsdata_read_mailing_lists();
    if (isset($_POST['yes'])) {
      $list = urldecode($_POST['list']);
      $addresses_updated = urldecode($_POST['addresses']);
      $successful = wpfsdata_write_addreses($list, $addresses_updated);
      $update_notice = $successful
        ? 'Listan uppdaterad!'
        : 'Uppdatering misslyckades, kontakta administratören.';
      $addresses = $successful ? $addresses_updated : wpfsdata_read_addresses($list);
      wpfsdata_edit_form($mailing_lists, $list, $addresses, $update_notice);
    } else {
      $list = isset($_GET['list']) ? urldecode($_GET['list']) : $mailing_lists[0];
      $addresses = wpfsdata_read_addresses($list);
      wpfsdata_edit_form($mailing_lists, $list, $addresses, FALSE);
    }
  }
}

function wpfsdata_admin_menu() {
  add_submenu_page('tools.php', 'E-postlisthantering', 'E-postlisthantering', 'manage_options', 'wpfsdata', 'wpfsdata_page');
}

add_action('admin_menu', 'wpfsdata_admin_menu');
?>
