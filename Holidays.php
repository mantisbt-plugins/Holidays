<?php
class HolidaysPlugin extends MantisPlugin {
 
	function register() {
		$this->name        = 'Holidays';
		$this->description = lang_get( 'holidays_description' );
		$this->version     = '2.11';
		$this->requires    = array('MantisCore'       => '2.0.0',);
		$this->author      = 'Cas Nuy';
		$this->contact     = 'Cas-at-nuy.info';
		$this->url         = 'http://www.nuy.info';
	}

	function init() { 
		// Allow defining holiday on account page
		event_declare('EVENT_ACCOUNT_UPDATE_FORM');
		// Allow defining holiday by administrator
		event_declare('EVENT_MANAGE_USER_FORM');
		// Delete holiday settings when user is deleted
		event_declare('EVENT_ACCOUNT_DELETED');
		// above declarations may become obsolete once these are part of standard mantis
	
		plugin_event_hook('EVENT_ACCOUNT_UPDATE_FORM', 'DefHoliday');
		plugin_event_hook('EVENT_MANAGE_USER_FORM', 'DefHoliday');
		plugin_event_hook('EVENT_ACCOUNT_DELETED', 'DelHoliday');
		plugin_event_hook('EVENT_LAYOUT_CONTENT_BEGIN', 'WarnHoliday');
		plugin_event_hook('EVENT_NOTIFY_USER_INCLUDE', 'MailHoliday');
		
	}

	function DefHoliday(){
		include( config_get( 'plugin_path' ) . 'Holidays' . DIRECTORY_SEPARATOR . 'pages' . DIRECTORY_SEPARATOR . 'holiday_form.php');  
	}
	
	function WarnHoliday(){
		include( config_get( 'plugin_path' ) . 'Holidays' . DIRECTORY_SEPARATOR . 'pages' . DIRECTORY_SEPARATOR . 'holiday_warning.php');  
	}
	
	function MailHoliday($p_event, $p_bug_id){
		$bug_info = bug_get( $p_bug_id, true ); 
		$handler = $bug_info->handler_id;
		// get the handler of the issue
		$handler	= $bug_info->handler_id ;
		$sql 		=  "select * from {plugin_Holidays_period} where user_id=$handler";
		$result	= db_query($sql);
		// check if this person is on holiday		
		if (db_num_rows($result) > 0) {
			$row = db_fetch_array($result);
			// first check absent indicator
			if ($row['absent'] >0 ){
				// now check if today is within period defined
				$today  = mktime(0, 0, 0, date("m")  , date("d"), date("Y"));
				if (($today>= $row['periodfrom']) and ($today <= $row['periodto']) or ( $row['absent'] == 2 ) ){
					// add the backup to the recipients
					// has to be an array
					$mail = array();
					$mail[1] = $row['backup_user'];
					return $mail ;
 				}
			}
		}
		return;
	}
	
	function DelHoliday($p_event,$f_user_id){
 		$sql = "delete from {plugin_Holidays_period} where user_id=$f_user_id";
		$result		= db_query($sql);
	}

	function schema() {
		return array(
			array( 'CreateTableSQL', array( plugin_table( 'period' ), "
						user_id 			I       NOTNULL UNSIGNED PRIMARY,
						backup_user			I		NOTNULL UNSIGNED ,
						periodfrom			I		,
						periodto			I		,
						absent				I		
						" ) ),
		);
	} 
	
}
