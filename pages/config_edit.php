<?php
// authenticate
auth_reauthenticate();
access_ensure_global_level( config_get( 'manage_plugin_threshold' ) );
// Read results
$f_holidays_notes 		= gpc_get_int( 'holidays_notes', ON );
$f_holidays_reminders 	= gpc_get_int( 'holidays_reminders', ON );
// update results
plugin_config_set( 'holidays_notes', $f_holidays_notes );
plugin_config_set( 'holidays_reminders', $f_holidays_reminders );
// redirect
print_successful_redirect( plugin_page( 'config',TRUE ) );