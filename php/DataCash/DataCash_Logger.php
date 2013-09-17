<?php
/**
 * This module contains the log function used by all DataCash PHP API routines.
 *
 * @internal $Id: DataCash_Logger.php,v 1.10 2007/02/28 14:19:59 sxf Exp $
 *
 * @package DataCash
 * @author Dave MacRae
 * @copyright DataCash Group plc 2003
 */

$CVSid = explode (" ", '$Id: DataCash_Logger.php,v 1.10 2007/02/28 14:19:59 sxf Exp $');
$included_classes[basename($CVSid[1], '.php,v')] = $CVSid[2];

/**
 * Log the supplied message at the specified log level (1-5).
 *
 * Logs to the log file specified in DataCash API configuration file.
 *
 * @access public
 * @param int $level Log level (1 = most severe, 5 = least severe)
 * @param string $message Log message.
 */
function dc_log ($level, $message) {
  static $log_file_fp = FALSE;
  global $config;

  if (!$log_file_fp && $config->get("Configuration.logging") >= $level) {
    $log_file = $config->get("Configuration.logfile");
    if (!($log_file_fp = fopen($log_file, "ab"))) {
      print "<!-- Cannot open log file -->\n";
      return;
    }
  }

  if ($log_file_fp && $config->get("Configuration.logging") >= $level) {
    list($usec, $sec) = explode(" ", microtime());
    $usec = $usec * 1000;
    $now = gmdate ("Y d m H:i:s", $sec);
    $out_str = sprintf ("%s.%03d: %02d: %s\r\n", $now, $usec, $level, $message);
    fputs ($log_file_fp, $out_str);
  }

  if ($log_file_fp) {
    fclose($log_file_fp);
    $log_file_fp = FALSE;
  }
}
?>
