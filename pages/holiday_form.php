<?PHP
$file = $_SERVER["SCRIPT_NAME"];
$break = Explode('/', $file);
$script = $break[count($break) - 1];
if (strtolower($script) == "account_prefs_page.php"){
	$user_id = auth_get_current_user_id();
} else {
	$user_id = gpc_get_int( 'user_id' );
}
?>
<div align="center">
<input type="hidden" name="user_id" value="<?php echo $user_id ?>" />
<input type="hidden" name="script" value="<?php echo $script ?>" />
<!-- Titles -->
<tr class="row-1" valign="top">
<td class="category" >
<?php echo lang_get( 'absent' ) ?>
</td>
<td class="category">
<?php echo lang_get( 'start_date' ) ?>
</td>
<td class="category">
<?php echo lang_get( 'end_date' ) ?>
</td>
</tr>
<?PHP
// retrieve current available settings
$hol_table	= plugin_table('period','Holidays');
$sql 	=  "select * from $hol_table where user_id=$user_id";
$result	= db_query($sql);
$count	= db_num_rows($result) ;
// if not there create with blanks
if ($count == 0){
	$sql2 		= "insert into $hol_table values ($user_id, 0,0,0,0)";
	$result2	= db_query($sql2);
	$result		= db_query($sql);
}
while ($row = db_fetch_array($result)) {
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
<tr valign="top">
<td >
<label><input type="radio" name='absent' value="2" <?php echo(($absent==2)?'checked=checked':'');?>/>
<?php echo lang_get( 'holidays_anyway' )?></label>

<label><input type="radio" name='absent' value="1" <?php echo(($absent==1)?'checked=checked':'');?>/>
<?php echo lang_get( 'holidays_enabled' )?></label>

<label><input type="radio" name='absent' value="0" <?php echo(($absent==0)?'checked=checked':'');?>/>
<?php echo lang_get( 'holidays_disabled' )?></label>

</td>
<?php 

?>
<td>
	<input type="text" id="holidays_start_date" name="holidays_start_date" class="datetimepicker input-sm" size="16" maxlength="16"
		data-picker-locale="<?php lang_get_current_datetime_locale() ?>"
		data-picker-format="<?php echo config_get( 'datetime_picker_format' ) ?>"
		<?php helper_get_tab_index() ?> value="<?php echo $from ?>" />
	<i class="fa fa-calendar fa-xlg datetimepicker"></i>
</td>
<td>
<input type="text" id="holidays_end_date" name="holidays_end_date" class="datetimepicker input-sm" size="16" maxlength="16"
		data-picker-locale="<?php lang_get_current_datetime_locale() ?>"
		data-picker-format="<?php echo config_get( 'datetime_picker_format' ) ?>"
		<?php helper_get_tab_index() ?> value="<?php echo $to ?>" />
	<i class="fa fa-calendar fa-xlg datetimepicker"></i>
</td>
</tr>