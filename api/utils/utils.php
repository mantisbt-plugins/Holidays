<?php 
/*
* This file is part of the Holidays plugin for MantisBT.
*
* Holidays is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.

* The Holidays plugin is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.

* You should have received a copy of the GNU General Public License
* along with the Holidays plugin.  If not, see <https://www.gnu.org/licenses/>.
*/

/**
 * File aggregating static methods
 */

/**
 * Function logging messages in the plugin's tmp/logs folder
 * 
 * @param string $message
 * @param boolean $datetime_prefix
 */
function write_log($message, $datetime_prefix = true){
    
    // Creating folder if necessary
    $log_dir = "plugins/" . plugin_get_current() . "/tmp/logs";
    // Check if folder exists
    if(! file_exists($log_dir)) {
        // Create folder, forcing creation with 3rd parameter
        mkdir($log_dir, 0777, true);
    }
    
    $log_file = $log_dir . "/log_" . date("Y-m-d") . ".txt";
    
    // Write in log file
    if($datetime_prefix) {
        $content = date(plugin_config_get('date_format')) . " : " . $message;
    } else {
        $content = $message;
    }
    
    // Open log file
    if(! $handle = fopen($log_file, 'ab')) {
        return false;
    }
    
    // Write, close file and return
    if(fwrite($handle, $content . "\n") === false){
        fclose($handle);
        return false;
    }
    
    fclose($handle);
    return true;
}

?>
