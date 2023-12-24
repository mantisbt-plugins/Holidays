<?PHP
$user_id	= gpc_get_int( 'user_id' );
$script		= gpc_get_string('script');
$from		= strtotime(substr($_REQUEST['from'],0,10));
$to			= strtotime(substr($_REQUEST['to'],0,10));
$absent		= $_REQUEST['absent'];
$backup		= $_REQUEST['bu_handler'];
// check on valid backup user
if ($absent > 0){
	if (($user_id == $backup) or ($backup ==0)) {
		trigger_error( ERROR_INVALID_BACKUP, ERROR );
	}
	if ($absent == 1){
		if (($from == '') or ($to == '')) {
			trigger_error( ERROR_INVALID_DATE, ERROR );
		}
	}
}
// perform update
if ($absent == 1){
	$sql = "UPDATE {plugin_Holidays_period} set periodfrom=$from,periodto=$to, absent=$absent, backup_user=$backup  WHERE user_id = $user_id";
} else {
	$sql = "UPDATE {plugin_Holidays_period}  set absent=$absent, backup_user=$backup  WHERE user_id = $user_id";
}
$result = db_query($sql);
if (!$result) {
//	die ($sql);
} else {
//	die ($sql);
}
$script .="?user_id=";
$script .=$user_id;
print_header_redirect( $script );