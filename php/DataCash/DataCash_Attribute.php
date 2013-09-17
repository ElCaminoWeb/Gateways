<?php
/**
 * This module is used to build an XML attribute as a component of an XML document.
 *
 * @internal $Id: DataCash_Attribute.php,v 1.8 2007/02/28 14:19:59 sxf Exp $
 *
 * @package DataCash
 * @author Kenny Scott, Dave MacRae
 * @copyright DataCash Group plc 2003
 */

$CVSid = explode (' ', '$Id: DataCash_Attribute.php,v 1.8 2007/02/28 14:19:59 sxf Exp $');
$included_classes[basename($CVSid[1], '.php,v')] = $CVSid[2];


/**
 * This class is used to build an XML attribute as a component of an XML document.
 *
 * @package DataCash
 * @access public
 */
class DataCash_Attribute {

  /**
   * DataCash_Attribute constructor
   *
   * Used to represent an XML attribute (name="value")   
   *
   * @access public
   * @param string $name Attribute key
   * @param string $value Attribute value
   */
  function DataCash_Attribute ($name, $value) {
    $this->name = $name;
    $this->value = $value;
  }

  /**
   * Returns the attribute name.
   *
   * @access public
   * @return string
   */
  function getName ( ) {
    return $this->name;
  }
  
  /**
   * Returns the attribute value.
   *
   * @access public
   * @return string
   */    
  function getValue ( ) {
    return $this->value;
  }

}

?>
