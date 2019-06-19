<?php
require_once __DIR__ . '/api/utils/utils.php';
define ( HOLIDAYS_PERIOD, 'period' );
define ( HOLIDAYS_ABSENT, 'absent' );

/**
 * Main class of plugin Holidays for Mantis
 *
 * @author c2pil
 *
 */
class HolidaysPlugin extends MantisPlugin {

    /**
     * Sets informations of plugin Holidays
     */
    public function register() {
        $this->name = 'Holidays';
        $this->description = plugin_lang_get ( 'description' );
        $this->version = '2.21';
        $this->requires = array (
                'MantisCore' => '2.21.0'
        );
        // Plugin put from Mantis issue to GitHub
        $this->author = array (
                'Cas Nuy',
                'c2pil'
        );
        $this->contact = 'c2pil@gmail.com';
    }

    /**
     * Hooks functions to events
     */
    public function init() {
        // Add Absence definition to preference form
        plugin_event_hook ( 'EVENT_ACCOUNT_PREF_UPDATE_FORM', 'def_holiday' );
        // Handle absence when updating preferences
        plugin_event_hook ( 'EVENT_ACCOUNT_PREF_UPDATE', 'update_holiday' );
        // Handle absence on user deletion
        plugin_event_hook ( 'EVENT_MANAGE_USER_DELETE', 'delete_holiday' );
        // Handle notification for absent user
        plugin_event_hook ( 'EVENT_NOTIFY_USER_EXCLUDE', 'is_user_absent' );
    }

    /**
     * Returns the configuration for this plugin
     *
     * @return string[]
     */
    public function config() {
        return array (
                'date_format' => 'Y-m-d H:i:s'
        );
    }

    /**
     * Includes absence field in preferences form
     */
    public function def_holiday() {
        return include_once (plugin_page ( 'holiday_form.php' ));
    }

    /**
     * Removes a user from mail notification if he is absent
     *
     * @param unknown $p_event
     * @param unknown $p_bug_id
     * @param unknown $p_notification_type
     * @param unknown $p_user_id
     * @return boolean
     */
    public function is_user_absent($p_event, $p_bug_id, $p_notification_type, $p_user_id) {
        // get the handler absence status
        $hol_table = plugin_table ( HOLIDAYS_PERIOD );
        $sql = "select * from $hol_table where user_id=$p_user_id";
        $result = db_query ( $sql );
        $remove = false;
        // check if this person is on holiday
        if (db_num_rows ( $result ) > 0) {
            $row = db_fetch_array ( $result );
            // first check absent indicator
            if ($row [HOLIDAYS_ABSENT] > 0) {
                // now check if today is within period defined
                $today = mktime ( 0, 0, 0, date ( "m" ), date ( "d" ), date ( "Y" ) );
                if (($today >= $row ['periodfrom']) && ($today <= $row ['periodto']) || ($row [HOLIDAYS_ABSENT] == 2)) {
                    // Remove the absent user form notification
                    $remove = true;
                }
            }
        }
        return $remove;
    }

    /**
     * Update user status on preferences update
     */
    public function update_holiday() {

        // Get params from request
        $user_id = $_REQUEST ['user_id'];
        $hol_table = plugin_table ( HOLIDAYS_PERIOD );
        $from = strtotime ( substr ( $_REQUEST ['holidays_start_date'], 0, 10 ) );
        $to = strtotime ( substr ( $_REQUEST ['holidays_end_date'], 0, 10 ) );
        $absent = $_REQUEST [HOLIDAYS_ABSENT];
        // check on valid absence date
        if ($absent == 1 && ($from == '' || $to == '' || $from > $to)) {
            trigger_error ( 'ERROR_INVALID_DATE', ERROR );
        }

        // perform update
        if ($absent == 1) {
            $sql = "UPDATE $hol_table set periodfrom=$from, periodto=$to, absent=$absent WHERE user_id=$user_id";
        } else {
            $sql = "UPDATE $hol_table set absent=$absent WHERE user_id= $user_id";
        }
        db_query ( $sql );

        $auth_username = user_get_name ( auth_get_current_user_id () );
        $updated_username = user_get_name ( $user_id );
        $log_from = substr ( $_REQUEST ['holidays_start_date'], 0, 10 );
        $log_to = substr ( $_REQUEST ['holidays_end_date'], 0, 10 );

        // Build log message
        switch ($absent) {
            case 0 :
                $message_log = sprintf ( plugin_lang_get ( 'log_present' ), $updated_username, $auth_username );
                break;
            case 1 :
                $message_log = sprintf ( plugin_lang_get ( 'log_absent_period' ),
                                         $updated_username, $log_from, $log_to, $auth_username );
                break;
            case 2 :
                $message_log = sprintf ( plugin_lang_get ( 'log_absent_indefinite' ),
                                         $updated_username, $auth_username );
                break;
            default :
                break;
        }
        // Log event in log file
        write_log ( $message_log );
    }

    /**
     * Delete user entry in plugin table if the user is deleted
     *
     * @param unknown $p_event
     * @param unknown $f_user_id
     */
    public function delete_holiday($p_event, $f_user_id) {
        $hol_table = plugin_table ( HOLIDAYS_PERIOD );
        $sql = "delete from $hol_table where user_id=$f_user_id";
        db_query ( $sql );
    }

    /**
     * Table definition on plugin installation
     *
     * @return
     */
    public function schema() {
        return array (
                array (
                        'CreateTableSQL',
                        array (
                                plugin_table ( HOLIDAYS_PERIOD ),
                                "
                        user_id             I       NOTNULL UNSIGNED PRIMARY,
                        periodfrom          I       ,
                        periodto            I       ,
                        absent              I
                        "
                        )
                )
        );
    }
}