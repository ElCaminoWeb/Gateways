<?php
/**
 * This module contains the DataCash_UserAgent function.
 *
 * @internal $Id: DataCash_UserAgent.php,v 1.10 2007/02/28 14:19:59 sxf Exp $
 *
 * @package DataCash
 * @author Dave MacRae
 * @copyright DataCash Group plc 2003
 */
 
$CVSid = explode (" ", '$Id: DataCash_UserAgent.php,v 1.10 2007/02/28 14:19:59 sxf Exp $');
$included_classes[basename($CVSid[1], '.php,v')] = $CVSid[2];


/**
 * 
 * Set the User Agent fields in the supplied XML request.
 *
 * Sets the Request.UserAgent.architecture, Request.UserAgent.language and Request.UserAgent.Libraries, 
 * Request.UserAgent.Libraries.lib fields in the XML request.
 * 
 * This method should be called before sending the XML request to the DataCash Payment Gateway.
 * 
 * @access public
 * @param DataCash_Document &$doc Affected document.
 */
function DataCash_UserAgent (&$doc) {

  global $included_classes;
  
  $os = php_uname();
  $doc->set ("Request.UserAgent.architecture", $os);
  $doc->set ("Request.UserAgent.language", "PHP", array('version' => phpversion()));
  
  $doc->set("Request.UserAgent.Libraries", "", array('bundle' => "DataCash-XML-PHP", 'version' => $included_classes["Bundle"]));
  
  while (list($key, $value) = each($included_classes)) {
    if ( $key != "Bundle" ) {
      $doc->set("Request.UserAgent.Libraries.lib", $key, array('version' => $value));
    }
  }
}

?>
