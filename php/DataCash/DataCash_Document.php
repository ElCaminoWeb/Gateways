<?php
/**
 * This module is used to build an XML document.
 *
 * @internal $Id: DataCash_Document.php,v 1.18 2007/03/20 10:08:12 sxf Exp $
 *
 * @package DataCash
 * @author Kenny Scott, Dave MacRae
 * @copyright DataCash Group plc 2003
 */
 
$CVSid = explode (" ", '$Id: DataCash_Document.php,v 1.18 2007/03/20 10:08:12 sxf Exp $');
$included_classes[basename($CVSid[1], '.php,v')] = $CVSid{2};

/**
 * This class is used to build an XML document.
 *
 * @package DataCash
 * @access public
 */
class DataCash_Document {

  /**
   * DataCash_Document constructor
   *
   * Create a new DataCash_Document object.
   * If an optional string is supplied create a root element using the supplied string as the element name.
   * 
   * Create a DataCash_Document.  
   *
   * @access public
   * @param string $name Optional argument - name of root element.
   */
  function DataCash_Document ( ) {

    $this->data = array();
    if (func_num_args() == 1) {
      $this->data[] = new DataCash_Element(func_get_arg(0));
    }
    $this->attributes = array();
  }

  /**
   * Set the root element of the DataCash_Document document to the supplied XML element.
   *
   * @access public
   * @param DataCash_Element &$element Supplied XML element.
   */
  function setRootElement (&$element) {
    $this->data = array(&$element);
  }
  
  /**
   * Returns the root element of the document or NULL if no root element has been set.
   *
   * @access public
   * @return DataCash_Element|NULL
   */
  function &getRootElement ( ) {
    return $this->data[0];
  }
  
  /**
   * Returns a whitespace seperated list of all the values from this DataCash_Document.
   *
   * This method should NOT be called on an empty document.
   *
   * @access public
   * @return string
   */
  function getDocument ( ) {

    $document = "";
    $this->indent = 0;
    $this->indent_quantity = 2;
    $root = $this->data[0];
    $document = $this->getElements($root);
    return $document;

  }
  
  /**
   * 
   * Returns a whitespace seperated list of values belonging to the specified element and its descendants.
   *
   * @access public
   * @param DataCash_Element $main_element Specified XML element.
   * @return string
   */
  function getElements ($main_element) {


    $elements =& $main_element->getAllElements();
    $data = $this->getIndent() . "<$main_element->name";

    $attributes = $main_element->getAllAttributes();
    if ($attributes) {
      foreach ($attributes as $attr) {
  $data .= ' ' . $attr->name . '="' . $attr->value . '"';
      }
    }
    $data .= ">";

    $this->indent += $this->indent_quantity;
    if ($elements != 0) {
      $data .= "\n";
      foreach ($elements as $element) {
  $data .= $this->getElements($element);
      }
      $this->indent -= $this->indent_quantity;
      $data .= $this->getIndent();
    } else {
      $this->indent -= $this->indent_quantity;
      $data .= $main_element->data;
    }
    $data .= "</$main_element->name>\n";
    return $data;
  }
  
  /**
   * Return the value of the specified XML element.
   *
   * If the specified element does not exist: 
   * - returns false.
   * Otherwise if the optional argument is passed and its value is false:
   * - returns a string containing the value of the specified XML element.
   * Otherwise if the optional argument is not passed or its value is true:
   * - returns the specified DataCash_Element.
   *
   * @access public
   * @param string $fullpath Fully qualified element name.
   * @param boolean $return_element Optional argument - Return the element if true, otherwise return the value of the element.
   * @return string|DataCash_Element|boolean
   */
  function get ($fullpath) {

    $root =& $this->getRootElement();

    if ( strtolower(get_class( $root )) != "datacash_element" ) {
      return;
    }

    $allElements = explode(".", $fullpath);
    array_shift($allElements);
    $aElements = array(&$root);

    foreach ($allElements as $e) {
      $el = $aElements[count($aElements)-1]->getElement($e);
      if (!$el) {
  return (FALSE);
      }
      $aElements[] = &$el;
    }

    $element = $aElements[count($aElements)-1];
    if (func_num_args() > 1 && !func_get_arg(1)) {
      return $element;
    }
    return $element->getText();

  }
  
  /**
   * Set the value of the specified XML element.
   *
   * This method accepts an optional third argument.  Supply an associative array of 
   * attribute name/attribute value pairs to set attributes of this element.
   *
   * @access public
   * @param string $fullpath Fully qualified element name - e.g. Request.Authentication.client
   * @param string $value Value of this element
   * @param array $attributes Optional argument - associative array of attribute name/attribute value pairs
   */
  function set ($fullpath, $value) {

    if ( $fullpath != "Request.Transaction.CardTxn.Card.pan" && 
   $fullpath != "Request.Authentication.password" && 
   $fullpath != "Request.Transaction.CardTxn.Card.Cv2Avs.cv2" ) {
      dc_log( 3, "Setting $fullpath to [$value]" );
    }
    else {
      dc_log( 3, "Setting $fullpath" );
    }
    $root =& $this->getRootElement();
    $allElements = explode(".", $fullpath);
    $addElement = array_pop($allElements);
    array_shift($allElements);
    $aElements = array(&$root);

    foreach ($allElements as $e) {
      $thisElement =& $aElements[count($aElements)-1];
      $el =& $thisElement->getElement($e);
      if (!$el) {
  $el = new DataCash_Element($e);
  $thisElement->addElement($el);
      }
      $aElements[] = &$el;
    }

    $newElement = new DataCash_Element($addElement);
    $newElement->setText($value);

    if (func_num_args() == 3) {
      $attributes = func_get_arg(2);
      foreach ($attributes as $attributeKey => $attributeValue) {
  $newAttribute = new DataCash_Attribute($attributeKey, $attributeValue);
  $newElement->addAttribute($newAttribute);
      }
    }

    $aElements[count($aElements)-1]->addElement($newElement);
  }

  /**
   * 
   * @access public
   * @return string $spaces
   */
  function getIndent () {
    $spaces = "";
    for ($i = 0; $i < $this->indent; $i++) {
      $spaces .= " ";
    }
    return $spaces;
  }

  /**
   * Free the specified XML Parser.
   *
   * @access public
   */
  function destroy ( ) {
    xml_parser_free($this->xml_parser);
  }

  /**
   * Start Element Handler for the XML parser
   *
   * @access private 
   * @param resource $parser Reference to the XML parser calling the handler. 
   * @param string $name Name of the element for which this handler is called.
   * @param array $attrs Associative array of the elements attributes (if any) The keys of this array are the attribute names, the values are the attribute values.
   */
  function startElement($parser, $name, $attrs) {

    $this->value = "";

    $element = new DataCash_Element($name);
    foreach ($attrs as $aKey => $aValue) {
      $element->addAttribute(new DataCash_Attribute($aKey, $aValue));
    }
    if (count($this->elementTree) == 0) {
      $this->setRootElement($element);
    } else {
      $this->elementTree[count($this->elementTree)-1]->addElement($element);
    }
    $this->elementTree[] = &$element;
  }
  
  /**
   * End Element Handler for the XML parser
   *
   * @access private 
   * @param resource $parser Reference to the XML parser calling the handler. 
   * @param resource $name Name of the element for which this handler is called.
   */
  function endElement($parser, $name) {
    if (strlen($this->value) > 0) {
      $this->elementTree[count($this->elementTree)-1]->setText($this->value);
    }
    array_pop($this->elementTree);
    $this->value = "";
  }

  /**
   * Character data handler function for the XML parser
   *
   * @access private 
   * @param resource $parser Reference to the XML parser calling the handler. 
   * @param string $data Character data
   */
  function characterData ($parser, $name) {
    if (preg_match("/[\w&]/", $name)) {
      $this->value .= $name;
    }
  }

  /**
   * Read the supplied string into this DataCash_Document. 
   *
   * @access public 
   * @param string $input String to read. 
   */
  function readDocumentFromString($input) {
    $this->elementTree = array();
    $this->data[0] = array();
    $this->value = "";
    $this->xml_parser = xml_parser_create();
    xml_set_object($this->xml_parser, $this);
    xml_parser_set_option($this->xml_parser, XML_OPTION_CASE_FOLDING, false);
    xml_set_element_handler($this->xml_parser, "startElement", "endElement");
    xml_set_character_data_handler($this->xml_parser, "characterData");

    if (!xml_parse($this->xml_parser, $input)) {
      $this->error_msg = sprintf("XML Error: %s at line %d",
         xml_error_string(xml_get_error_code($this->xml_parser)),
         xml_get_current_line_number($this->xml_parser));
    }
    $this->destroy();

  }
  
  /**
   * Read the data in the specified file into this DataCash_Document. 
   *
   * @access public 
   * @param string $input Full path to file to read.
   */
  function readDocumentFromFile($input) {

    $this->elementTree = array();
    $this->data[0] = array();
    $this->value = "";

    $this->xml_parser = xml_parser_create();
    xml_set_object($this->xml_parser, $this);
    xml_parser_set_option($this->xml_parser, XML_OPTION_CASE_FOLDING, false);
    xml_set_element_handler($this->xml_parser, "startElement", "endElement");
    xml_set_character_data_handler($this->xml_parser, "characterData");

    if (!($fp = fopen($input, "r"))) {
     	$this->file_read = false;
      	$this->error_msg = "Could not open XML input file $input";
    } else {
      	$this->file_read = true;
      	while ($data = fread($fp, 4096)) {
				if (!xml_parse($this->xml_parser, $data, feof($fp))) {
					fclose ($fp);
					$this->file_read = false;
					$this->error_msg = sprintf("XML Error: %s at line %d",
					xml_error_string(xml_get_error_code($this->xml_parser)),
					xml_get_current_line_number($this->xml_parser));
					return;
				}
      		}
      		fclose ($fp);
    	}
    	$this->destroy();
  	}
}

?>
