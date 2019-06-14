<?php
require_once __DIR__.'/api/utils/utils.php';

class HolidaysPlugin extends MantisPlugin {
    
    
	function register() {
		$this->name        = 'Holidays';
		$this->description = plugin_lang_get( 'description' );
		$this->version     = '2.21';
		$this->requires    = array('MantisCore'       => '2.21.0',);
		$this->author      = array('Cas Nuy', 'c2pil');
		$this->contact     = 'c2pil@gmail.com';
	}

	function init() { 
	
		plugin_event_hook('EVENT_ACCOUNT_PREF_UPDATE_FORM', 'DefHoliday');
		plugin_event_hook('EVENT_ACCOUNT_PREF_UPDATE', 'UpdateHoliday');
		plugin_event_hook('EVENT_MANAGE_USER_DELETE', 'DelHoliday');
		plugin_event_hook('EVENT_NOTIFY_USER_EXCLUDE', 'MailHoliday');
		
	}
	
	function config() {
	    return array(
	        'date_format' => 'Y-m-d H:i:s'
	    );
	}

	function DefHoliday(){
		include( config_get( 'plugin_path' ) . 'Holidays' . DIRECTORY_SEPARATOR . 'pages' . DIRECTORY_SEPARATOR . 'holiday_form.php');  
	}
	
	function MailHoliday($p_event, $p_bug_id, $p_notification_type, $p_user_id ){
		// get the handler absence status
		$hol_table	= plugin_table('period','Holidays');
		$sql 		=  "select * from $hol_table where user_id=$p_user_id";
		$result	    = db_query($sql);
		$remove     = false;
		// check if this person is on holiday		
		if (db_num_rows($result) > 0) {
			$row = db_fetch_array($result);
			// first check absent indicator
			if ($row['absent'] >0 ){
				// now check if today is within period defined
				$today  = mktime(0, 0, 0, date("m")  , date("d"), date("Y"));
				if (($today>= $row['periodfrom']) and ($today <= $row['periodto']) or ( $row['absent'] == 2 ) ){
					// Remove the absent user form notification
					$remove = true;
 				}
			}
		}
		return $remove;
	}
	
	function UpdateHoliday(){
	    $user_id    = $_REQUEST['user_id'];
	    $hol_table	= plugin_table('period','Holidays');
	    $from		= strtotime(substr($_REQUEST['holidays_start_date'],0,10));
	    $to			= strtotime(substr($_REQUEST['holidays_end_date'],0,10));
	    $absent		= $_REQUEST['absent'];
	    // check on valid absence date
        if ($absent == 1 and ($from == '' or $to == '' or $from > $to)){
            trigger_error( 'ERROR_INVALID_DATE', ERROR );
        }
	    
	    // perform update
	    if ($absent == 1){
	        $sql = "UPDATE $hol_table set periodfrom=$from, periodto=$to, absent=$absent  WHERE user_id = $user_id";
	    } else {
	        $sql = "UPDATE $hol_table set absent=$absent WHERE user_id = $user_id";
	    }
	    $result = db_query($sql);
	    
	    $auth_username = user_get_name(auth_get_current_user_id());
	    $updated_username = user_get_name($user_id);
	    $log_from = substr($_REQUEST['holidays_start_date'],0,10);
	    $log_to = substr($_REQUEST['holidays_end_date'],0,10);
	    
	    switch($absent){
	        case 0:
	            $message_log = sprintf( plugin_lang_get('log_present'),
	                           $updated_username, $auth_username);
	            break;
	        case 1:
	            $message_log = sprintf( plugin_lang_get('log_absent_period'),
	                           $updated_username, $log_from, $log_to, $auth_username);
	            break;
	        case 2:
	            $message_log = sprintf( plugin_lang_get('log_absent_indefinite'),
	                           $updated_username, $auth_username);
	            break;
	    }
	    
	    write_log($message_log);
	}
	
	function DelHoliday($p_event,$f_user_id){
 		$hol_table	= plugin_table('period');
		$sql = "delete from $hol_table where user_id=$f_user_id";
		$result		= db_query($sql);
	}

	function schema() {
		return array(
			array( 'CreateTableSQL', array( plugin_table( 'period' ), "
						user_id 			I       NOTNULL UNSIGNED PRIMARY,
						periodfrom			I		,
						periodto			I		,
						absent				I		
						" ) ),
		);
	} 
	
}