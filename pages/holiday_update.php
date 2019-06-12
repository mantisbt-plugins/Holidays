<?PHP
require_once( '../../../core.php' );
$user_id	= gpc_get_int( 'user_id' );
$script		= gpc_get_string('script');
$hol_table	= plugin_table('period','Holidays');
$from		= strtotime(substr($_REQUEST['from'],0,10));
$to			= strtotime(substr($_REQUEST['to'],0,10));
$absent		= $_REQUEST['absent'];
$backup		= $_REQUEST['bu_handler'];
// check on valid backup user
if ($absent > 0){
	if (($user_id == $backup) or ($backup ==0)) {
		trigger_error( ERROR_INVALID_BACKUP, ERROR );
	}
}
// perform update
$sql = "UPDATE $hol_table set periodfrom=$from,periodto=$to, absent=$absent, backup_user=$backup  WHERE user_id = $user_id";
$result = db_query_bound($sql);
$script .="?user_id=";
$script .=$user_id;
print_header_redirect( $script );