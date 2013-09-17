<?php
/**
 * This module is used to include all the files necessary for DataCash to
 * function in the PHP environment.
 *
 * @internal $Id: DataCash.php,v 1.14 2007/02/28 14:19:59 sxf Exp $
 *
 * @package DataCash
 * @author Dave MacRae
 * @copyright DataCash Group plc 2003
 */


$CVSid = explode (" ", '$Id: DataCash.php,v 1.14 2007/02/28 14:19:59 sxf Exp $');
global $included_classes;
$included_classes[basename($CVSid[1], '.php,v')] = $CVSid[2];

$package = explode( " ", '$Name: version_1_05 $' );
preg_match( "/version_(.*)/", $package[1], $matches );
$package_version = preg_replace( '/_/', '.', $matches[1] );
$included_classes["Bundle"] = $package_version;

include ("DataCash_SSLAgent.php");
include ("DataCash_Attribute.php");
include ("DataCash_Element.php");
include ("DataCash_Document.php");
include ("DataCash_Logger.php");
include ("DataCash_CardInfo.php");
include ("DataCash_UserAgent.php");

?>