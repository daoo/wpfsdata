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

if (empty($removed_addresses) && empty($added_addresses)) {
?>
<div class="wrap">
  <h1>E-postlisthantering</h1>
  <p>Inga ändringar gjorda</p>
  <p>
    <form method="post" action="">
      <input type="hidden" name="list" value="<?php echo(urlencode($current_list)); ?>" />
      <input type="submit" name="ok" class="button-primary" value="Ok" />
    </form>
  </p>
</div>
<?php
} else {
?>
<div class="wrap">
  <h1>E-postlisthantering</h1>
  <h2>Är du säker?</h2>
  <p>Gör följande ändringar på listan <?php echo(htmlspecialchars($current_list)); ?>:</p>
  <?php if (!empty($removed_addresses)) { ?>
  <h3>Ta bort</h3>
  <pre><?php echo(htmlspecialchars(implode("\n", $removed_addresses))); ?></pre>
  <?php } ?>
  <?php if (!empty($added_addresses)) { ?>
  <h3>Lägg till</h3>
  <pre><?php echo(htmlspecialchars(implode("\n", $added_addresses))); ?></pre>
  <?php } ?>
  <form method="post" action="">
    <input type="hidden" name="list" value="<?php echo(urlencode($current_list)); ?>" />
    <input type="hidden" name="addresses" value="<?php echo(urlencode(implode("\n", $addresses))); ?>" />
    <input type="submit" name="yes" class="button-primary" value="Ja" />
    <input type="submit" name="no" class="button-primary" value="Nej" />
  </form>
</div>
<?php
}
?>
