<?php
auth_reauthenticate();
access_ensure_global_level( config_get( 'manage_plugin_threshold' ) );
html_page_top1( lang_get( 'plugin_format_title' ) );
html_page_top2();
print_manage_menu();
?>
<br/>
<form action="<?php echo plugin_page( 'config_edit' ) ?>" method="post">
<table align="center" class="width50" cellspacing="1">
<tr>
<td class="category" colspan="2">
</td>
</tr>
<tr>
<td class="form-title" colspan="2">
<?php echo lang_get( 'holidays_title' ) . ': ' . lang_get( 'plugin_tasks_config' ) ?>
</td>
</tr>
<tr>
<td class="category" width="60%">
<?php echo lang_get( 'holidays_reminders' )?>
</td>
<td class="center" width="40%">
<label><input type="radio" name='holidays_reminders' value="1" <?php echo( ON == plugin_config_get( 'holidays_reminders' ) ) ? 'checked="checked" ' : ''?>/>
<?php echo lang_get( 'holidays_on' )?></label>
<label><input type="radio" name='holidays_reminders' value="0" <?php echo( OFF == plugin_config_get( 'holidays_reminders' ) )? 'checked="checked" ' : ''?>/>
<?php echo lang_get( 'holidays_off' )?></label>
</td>
</tr> 

<tr>
<td class="category" width="60%">
<?php echo lang_get( 'holidays_notes' )?>
</td>
<td class="center" width="40%">
<label><input type="radio" name='holidays_notes' value="1" <?php echo( ON == plugin_config_get( 'holidays_notes' ) ) ? 'checked="checked" ' : ''?>/>
<?php echo lang_get( 'holidays_on' )?></label>
<label><input type="radio" name='holidays_notes' value="0" <?php echo( OFF == plugin_config_get( 'holidays_notes' ) )? 'checked="checked" ' : ''?>/>
<?php echo lang_get( 'holidays_off' )?></label>
</td>
</tr> 

<tr>
<td class="center" colspan="3">
<input type="submit" class="button" value="<?php echo lang_get( 'change_configuration' ) ?>" />
</td>
</tr>
</table>
<form>
<?php
html_page_bottom1( __FILE__ );