<?PHP
$file = $_SERVER["SCRIPT_NAME"];
$break = Explode('/', $file);
$script = $break[count($break) - 1];
$t_date_to_display = date(config_get( 'normal_date_format' )); 
if (strtolower($script) == "account_page.php"){
	$user_id = auth_get_current_user_id();
} else {
	$user_id = gpc_get_int( 'user_id' );
}
?>

	<div class="col-md-12 col-xs-12">
	<div class="space-10"></div>
	<div class="form-container" > 
<form method="post" action="plugin.php?page=Holidays/holiday_update.phpp">
<input type="hidden" name="user_id" value="<?php echo $user_id ?>" />
<input type="hidden" name="script" value="<?php echo $script ?>" />
	<div class="widget-box widget-color-blue2">
	<div class="widget-header widget-header-small">
	<h4 class="widget-title lighter">
		<i class="ace-icon fa fa-text-width"></i>
		<?php echo lang_get( 'holiday_title' )  ?>
	</h4>
	</div>
	<div class="widget-body">
	<div class="widget-main no-padding">
	<div class="table-responsive"> 
	<table class="table table-bordered table-condensed table-striped"> 	
<tr><td colspan=2 class="row-category"><div align="left"><a name="holiday_record"></a>

</td>
</tr>
<!-- Titles -->
<tr  valign="top">
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
$sql 	=  "select * from {plugin_Holidays_period} where user_id=$user_id";
$result	= db_query($sql);
$count	= db_num_rows($result) ;
// if not there create with blanks
if ($count == 0){
	$sql2 		= "insert into {plugin_Holidays_period} values ($user_id, 0,0,0,0)";
	$result2	= db_query($sql2);
	$result		= db_query($sql);
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
<tr valign="top">
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


	echo '<input ' . helper_get_tab_index() . ' type="text" id="from" name="from" class="datetimepicker input-sm" ' .
				'data-picker-locale="' . lang_get_current_datetime_locale() .
				'" data-picker-format="' . config_get( 'datetime_picker_format' ) . '" ' .
				'size="20" maxlength="16" value="' . $from . '" />' ?>
			<i class="fa fa-calendar fa-xlg datetimepicker"></i> 

</td>
<td>
<?php 

	echo '<input ' . helper_get_tab_index() . ' type="text" id="to" name="to" class="datetimepicker input-sm" ' .
				'data-picker-locale="' . lang_get_current_datetime_locale() .
				'" data-picker-format="' . config_get( 'datetime_picker_format' ) . '" ' .
				'size="20" maxlength="16" value="' . $to . '" />' ?>
			<i class="fa fa-calendar fa-xlg datetimepicker"></i> 
</td>
<td>
<?php 
echo '<select name="bu_handler">';
echo '<option value="0"';
echo '> </option>' . "\n"; 
print_assign_to_option_list( intval( $backup ) );
//print_assign_to_option_list( 0 );
echo '</select>';
?>
</td>
<td >
<input type="submit" class="button" value="<?php echo lang_get( 'update' ) ?>" />
</tr>
</table>
</div>
</div>
</div>
</div>
</form>
