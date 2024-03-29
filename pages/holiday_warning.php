<?PHP
if( auth_is_user_authenticated()  ) { 
	$u_id = auth_get_current_user_id();
	$hol_table	= plugin_table('period','Holidays');
	$sql 	=  "select * from {plugin_Holidays_period} where user_id=$u_id";
	$result	= db_query($sql);

	if (db_num_rows($result) > 0) {
		$row = db_fetch_array($result);
		// first check absent indicator
		if ($row['absent'] >0){
			if ($row['absent']== 1){
				// now check if today is within period defined
				$today  = mktime(0, 0, 0, date("m")  , date("d"), date("Y"));
				if (($today>= $row['periodfrom']) and ($today <= $row['periodto'])){
					echo lang_get('hol_warning');
				}
			} else {
				// period is not relevant
				echo lang_get('hol_warning');
			}
		}
	}
}