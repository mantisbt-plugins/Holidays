<?PHP
/*
 * This file is part of the Holidays plugin for MantisBT.
 *
 * Holidays is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 
 * The Holidays plugin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 
 * You should have received a copy of the GNU General Public License
 * along with the Holidays plugin.  If not, see <https://www.gnu.org/licenses/>.
 */

// Split URL to get current page file name
$file = $_SERVER ["SCRIPT_NAME"];
$break = Explode ( '/', $file );
$script = $break [count ( $break ) - 1];
// Get logged in user if in his preference page
if (strtolower ( $script ) == "account_prefs_page.php") {
    $user_id = auth_get_current_user_id ();
} else {
    // Get user_id from request otherwise
    $user_id = gpc_get_int ( 'user_id' );
}

// Retrieve current available settings
$hol_table = plugin_table ( 'period', 'Holidays' );
// Get all info from plugin table
$sql = "select * from $hol_table where user_id=$user_id";
$result = db_query ( $sql );
$count = db_num_rows ( $result );
// If not there create with blanks
if ($count == 0) {
    $sql2 = "insert into $hol_table values ($user_id, 0,0,0,0)";
    db_query ( $sql2 );
    // Get new result
    $result = db_query ( $sql );
}
// Get data from results
while ( $row = db_fetch_array ( $result ) ) {
    $from = $row ['periodfrom'];
    $to = $row ['periodto'];
    $absent = $row ['absent'];
}

// Format dates for display purpose
if ($from == '0') {
    $from = '';
} else {
    $from = date ( config_get ( 'short_date_format' ), $from );
}
if ($to == '0') {
    $to = '';
} else {
    $to = date ( config_get ( 'short_date_format' ), $to );
}
?>
<div align="center">
    <input type="hidden" name="user_id" value="<?php echo $user_id ?>" />
    <input type="hidden" name="script" value="<?php echo $script ?>" />
    <!-- Titles -->
    <tr valign="top">
        <td class="category">
            <?php echo plugin_lang_get( 'absent' ) ?>
        </td>
        <td><label> <input type="radio" name='absent' value="2"
                <?php check_checked( 2, $absent, false );?> />
                <?php echo plugin_lang_get( 'holidays_anyway' )?>
            </label> <label> <input type="radio" name='absent' value="1"
                <?php check_checked( 1, $absent, false );?> />
                <?php echo plugin_lang_get( 'holidays_enabled' )?>
            </label> <label> <input type="radio" name='absent' value="0"
                <?php check_checked( 0, $absent, false );?> />
                <?php echo plugin_lang_get( 'holidays_disabled' )?>
            </label> <br /> <label><?php echo plugin_lang_get( 'start_date' ) ?></label>
            <input type="text" id="holidays_start_date"
            name="holidays_start_date" class="datetimepicker input-sm"
            size="16" maxlength="16"
            data-picker-locale="<?php lang_get_current_datetime_locale() ?>"
            data-picker-format="<?php echo config_get( 'datetime_picker_format' ) ?>"
            <?php helper_get_tab_index() ?> value="<?php echo $from ?>" />
            <i class="fa fa-calendar fa-xlg datetimepicker"></i>
            <label><?php echo plugin_lang_get( 'end_date' ) ?></label>
            <input type="text" id="holidays_end_date"
            name="holidays_end_date" class="datetimepicker input-sm"
            size="16" maxlength="16"
            data-picker-locale="<?php lang_get_current_datetime_locale() ?>"
            data-picker-format="<?php echo config_get( 'datetime_picker_format' ) ?>"
            <?php helper_get_tab_index() ?> value="<?php echo $to ?>" />
            <i class="fa fa-calendar fa-xlg datetimepicker"></i></td>