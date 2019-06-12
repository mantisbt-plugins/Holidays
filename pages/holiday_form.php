<?PHP
$file = $_SERVER["SCRIPT_NAME"];
$break = Explode('/', $file);
$script = $break[count($break) - 1];
if (strtolower($script) == "account_page.php"){
	$user_id = auth_get_current_user_id();
} else {
	$user_id = gpc_get_int( 'user_id' );
}
?>
<br />
<div align="center">
<table class="width75" cellspacing="1">
<!-- Title -->
<tr>
<td class="form-title" colspan="2">
<?php echo lang_get( 'holiday_title' ) ?>
</td>
</tr>
<form method="post" action="plugins/Holidays/pages/holiday_update.php">
<input type="hidden" name="user_id" value="<?php echo $user_id ?>" />
<input type="hidden" name="script" value="<?php echo $script ?>" />
<!-- Titles -->
<tr <?php echo helper_alternate_class( 1 ) ?> valign="top">
<td class="category" >
<?php echo lang_get( 'absent' ) ?>
</td>
<td class="category">
<?php echo lang_get( 'start_date' ) ?>
</td>
<td class="category">
<?php echo lang_get( 'end_date' ) ?>
</td>
<td class="category">
<?php echo lang_get( 'backup' ) ?>
</td>
<td class="category">
<?php echo lang_get( 'update' ) ?>
</td>
</tr>
<?PHP
// retrieve current available settings
$hol_table	= plugin_table('period','Holidays');
$sql 	=  "select * from $hol_table where user_id=$user_id";
$result	= db_query_bound($sql);
$count	= db_num_rows($result) ;
// if not there create with blanks
if ($count == 0){
	$sql2 		= "insert into $hol_table values ($user_id, '','','',0)";
	$result2	= db_query_bound($sql2);
	$result		= db_query_bound($sql);
}
while ($row = db_fetch_array($result)) {
	$backup	= $row['backup_user'] ;
	$from	= $row['periodfrom'] ;
	$to		= $row['periodto'] ;
	$absent	= $row['absent'];
}
if ( $from == '0' ) {
	$from = '';
} else { 
	$from 	= date( config_get( 'short_date_format' ), $from); 
}
if ( $to == '0' ) {
	$to = '';
} else { 
	$to	= date( config_get( 'short_date_format' ), $to); 
}
?>
<!-- Settings -->
<tr <?php echo helper_alternate_class() ?> valign="top">
<td >
<label><input type="radio" name='absent' value="2" <?php echo(($absent==2)?'checked=checked':'');?>/>
<?php echo lang_get( 'holidays_anyway' )?></label>
<br>
<label><input type="radio" name='absent' value="1" <?php echo(($absent==1)?'checked=checked':'');?>/>
<?php echo lang_get( 'holidays_enabled' )?></label>
<br>
<label><input type="radio" name='absent' value="0" <?php echo(($absent==0)?'checked=checked':'');?>/>
<?php echo lang_get( 'holidays_disabled' )?></label>

</td>
<td>
<?php
print "<input ".helper_get_tab_index()." type=\"text\" id=\"from\" name=\"from\" size=\"12\" maxlength=\"20\" value=\"".$from."\" />";
date_print_calendar('trigger1');
date_finish_calendar( 'from', 'trigger1' );
?>
</td>
<td>
<?php 
print "<input ".helper_get_tab_index()." type=\"text\" id=\"to\" name=\"to\" size=\"12\" maxlength=\"20\" value=\"".$to."\" />";
date_print_calendar('trigger2');
date_finish_calendar( 'to', 'trigger2' );
?>	
</td>
<td>
<?php 
echo '<select name="bu_handler">';
echo '<option value="0"';
echo '> </option>' . "\n"; 
print_assign_to_option_list( $backup );
echo '</select>';
?>
</td>
<td >
<input type="submit" class="button" value="<?php echo lang_get( 'update' ) ?>" />
</td>
</tr>
</form>
</table>
</div>
<center>