<?php
/**
 * This module is used to build an XML element as a component of an XML document.
 *
 * @internal $Id: DataCash_Element.php,v 1.12 2007/02/28 14:19:59 sxf Exp $
 *
 * @package DataCash
 * @author Kenny Scott, Dave MacRae
 * @copyright DataCash Group plc 2003
 */

$CVSid = explode (" ", '$Id: DataCash_Element.php,v 1.12 2007/02/28 14:19:59 sxf Exp $');
$included_classes[basename($CVSid[1], '.php,v')] = $CVSid[2];

/**
 * This class is used to build an XML element as a component of an XML document.
 *
 * An XML element takes the form: <name attribute="atribute_value">element_value</name>
 *
 * @package DataCash
 * @access public
 */
class DataCash_Element {
  
  /**
   * DataCash_Element constructor
   *
   * Create an DataCash_Element using the supplied name (<name></name>).  
   *
   * @access public
   * @param string $name Element name
   */
  function DataCash_Element ($name) {
    $this->name = $name;
    $this->attributes = array();
    $this->data = array();
  }

  /**
   * Add a child element to this element
   *
   * @access public
   * @param DataCash_Element &$element XML element to add.
   */
  function addElement (&$element) {
    if (!is_array($this->data)) {
      $this->data = array();
    }
    $this->data[] = &$element;
  }

  /**
   * Return a reference to an array of the descendants of this element or 0 if no attributes exist.
   *
   * @access public
   * @return array
   */ 
  function &getAllElements ( ) {
    if (is_array($this->data)) {
      return $this->data;
    }
    return 0;
  }
    
  /**
   * Return a reference to the child object identified by the supplied name or 0 if no child object exists.
   *
   * This only works for children of the element, not all descendants.
   *
   * @access public
   * @param string $name Element name.
   * @return DataCash_Element
   */  
  function &getElement ($name) {
      
    $elements =& $this->getAllElements();
    if ($elements != 0) {
      for ($i=0; $i<count($elements); $i++) {
  $element =& $elements[$i];
  if (strcmp($element->name, $name) == 0) {
    return $element;
  }
      }
    }
    return 0;
  }
    
  /**
   * Set the value of this element to the supplied string.  
   *
   * @access public
   * @param string $text Element value.
   */
  function setText ($text) {
    $this->data = $text;
  }
  
  /**
   * Returns the value of this element, or NULL if the element has no value set
   *
   * @access public
   * @return string|NULL
   */
  function getText ( ) { 
    if (!(is_array($this->data))) {
      return $this->data;
    } else {
      return null;
    }
  }

  /**
   * Add an attribute to the element.  
   *
   * More than one attributes can be added to an element.
   *
   * @access public
   * @param DataCash_Attribute $attribute Attribute to add.
   */
  function addAttribute ($attribute) {
    $this->attributes[] = $attribute;
  }
    
  /**
   * Returns a reference to an array of the attributes belonging to this element or 0 if no attributes exist.
   *
   * @access public
   * @return array|int
   */  
  function &getAllAttributes ( ) {
    if (is_array($this->attributes)) {
      return $this->attributes;
    }
    return 0;
  }
}

?>
