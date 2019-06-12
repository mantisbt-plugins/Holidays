<?PHP
require_once( '../../../core.php' );
$user_id			= gpc_get_int( 'user_id' );
$hol_table	= plugin_table('period','Holidays');
$from		= strtotime(substr($_REQUEST['from'],0,10));
$to			= strtotime(substr($_REQUEST['to'],0,10));
$absent		= $_REQUEST['absent'];
$backup		= $_REQUEST['bu_handler'];
// perform update
$sql = "UPDATE $hol_table set periodfrom=$from,periodto=$to, absent=$absent, user_Backup=$backup  WHERE user_id = $user_id";
$result = db_query_bound($sql);
print_header_redirect( 'account_page.php' );