<?php
/*
WPFSdata, FSData mailing list management plugin.
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
?>
<div class="wrap">
  <h1>E-postlisthantering</h1>
  <p>Hantera e-postlistorna här.</p>
  <form method="get" action="">
    Lista: <select name="list">
      <?php
        foreach ($mailing_lists as $list) {
          $selected = $list === $current_list ? 'selected="selected"' : "";
          printf(
            '<option value="%s" %s>%s</option>',
            urlencode($list),
            $selected,
            htmlspecialchars($list));
        }
      ?>
    </select>
    <input type="hidden" name="page" value="wpfsdata" />
    <input type="submit" class="button-primary" value="Välj" />
  </form>
  <?php
    if ($update_notice) {
      echo("<p>$update_notice</p>");
    }
  ?>
  <form method="post" action="">
    <h2>Redigera</h2>
    <p><textarea name="addresses" rows="20" cols="80"><?php echo(htmlspecialchars($current_addresses)); ?></textarea></p>
    <input type="hidden" name="list" value="<?php echo(urlencode($current_list)); ?>" />
    <input type="submit" name="edit" class="button-primary" value="Uppdatera" />
  </form>
</div>
