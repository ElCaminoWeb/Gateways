<?php
/**
 * This module is used to supply details of a card.
 *
 * @internal $Id: DataCash_CardInfo.php,v 1.13 2007/02/28 14:19:59 sxf Exp $
 *
 * @package DataCash
 * @author Dave MacRae
 * @copyright DataCash Group plc 2003
 */

$CVSid = explode (" ", '$Id: DataCash_CardInfo.php,v 1.13 2007/02/28 14:19:59 sxf Exp $');
$included_classes[basename($CVSid[1], '.php,v')] = $CVSid[2];

/**
 * DataCash_CardInfo
 *
 * This class is used to supply details on a card. It does this by reading two binary files which together hold details 
 * of the issuer, scheme, country of issue, number of digits and issue number / start date requirements of most cards.
 *
 * Example usage:
 *
 * $cardinfo = new DataCash_CardInfo (array("datadir" => "PATH_TO_CARDINFO_FILES"));
 * $cardinfo = new DataCash_CardInfo (array("datadir" => "PATH_TO_CARDINFO_FILES", "pan" => "4444333322221111"));
 *
 * $cardinfo->country()
 *
 * @package DataCash
 * @access public
 */
Class DataCash_CardInfo {

  /**
   * DataCash_CardInfo constructor
   *
   * Constructs a DataCash cardinfo object.  Use this to obtain information on a card.
   *
   * Expects to be passed a path to the cardinfo files (datadir) and the cardnumber (pan)
   *
   * @access public
   * @param array $params Array of key/value pairs (strings) including datadir and pan
   */
  function DataCash_CardInfo ($params) {

    foreach ($params as $key => $value) {
      $this->{$key}=$value;
    }

    // We assume that the config parameter is an already built XML
    // document.
    // $self->config=new DataCash::Config($params[configfile]);

    if ($this->datadir) {
      $this->fh_bins_path = $this->datadir . "/CardInfo1.bin";
      $this->fh_data_path = $this->datadir . "/CardInfo2.bin";
    }

    if (file_exists($this->fh_bins_path)) {
      $this->fh_bins = fopen ($this->fh_bins_path, "rb") or die ("Cannot open CardInfo1.bin: $!");
    }

    if (file_exists($this->fh_data_path)) {
      $this->fh_data = fopen($this->fh_data_path,"rb") or die ("Cannot open CardInfo2.bin: $!");
    }

    // If a PAN has been entered then check its requirements
    if ($this->pan) {
      $this->_find(0);
    }

    return;

  }
  
  /**
   * @access private
   */
  function _unbin ($buffer) {

    $len = strlen($buffer);

    if ($len != 16) {
      dc_log (5, "CardInfo: card not found\n");
      return false;
    }

    $start = $this->_chunk ($buffer, 0, 3);
    $length = $this->_chunk ($buffer,3,1);
    $left = $this->_chunk ($buffer,4,4);
    $right = $this->_chunk ($buffer,8,4);
    $data = $this->_chunk ($buffer,12,4);

    return array ($start,$length,$left,$right,$data);
  }
  
  /**
   * @access private
   */
  function _chunk ($data, $offset, $length) {

    // build the unpack string.
    $template = $this->_x("x", $offset, "nul") . $this->_x("C", $length, "char");
    $octets = unpack($template, $data);

    $i=0;
    $total=0;

    foreach (array_reverse ($octets) as $element) {
      $total += $element * pow(2, $i);
      $i += 8;
    }

    return $total;
  }
  
  /**
   * @access private
   */
  function _x ($char, $count, $name) {
    // This function emulates the Perl "x" oprator. It has an extra paramerter that is
    // used to build a proper unpack operator. This is not portable back to Perl
    static $index = 1;
    $x = '';
    for ($i = 0;$i < $count;$i++)
      $x .= $char . $name . $index++ . "/";
    return ($x);
  }
  
  /**
   * @access private
   */
  function _find ($offset) {
    $bin = substr($this->pan, 0, 6);
    fseek ($this->fh_bins, $offset*16, 0);
    $buffer = fread ($this->fh_bins, 16);
    list ($start,$length,$left,$right,$data) = $this->_unbin($buffer);
    if (! $start) {
      return;
    }

    if ($bin >= ($start+$length)) {
      $this->_find($right+$offset);
    } else {
      if ($bin>=$start) {
  $this->_data($data);
      } else {
  $this->_find($left+$offset);
      }
    }
  }
  
  /**
   * @access private
   */
  function _get_data ($fh, $offset) {

    rewind($fh);
    while (!feof($fh)) {
      // get more data into the buffer
      $buffer = fread ($fh, 8192);
      $no_newlines = substr_count ($buffer, "\n");
      $run_tot += $no_newlines;
      if ($offset - $no_newlines <= 0) {
  // The data probably appears in this block, but all the data might not be there
  // If the last character isn't a newline then it probably isn't
  $line_array = explode ("\n", $buffer);
  if ($buffer[strlen($buffer) - 1] == "\n") {
    return ($line_array[$offset]);
  } else {
    // get a small chunk rom the file
    $next = fread ($fh, 512);
    // add it to the existing chunk
    $last = $buffer . $next;
    $line_array = explode ("\n", $last);
    return ($line_array[$offset]);
  }
  // line requires is in this block;
  $line_array = explode ("\n", $buffer);
  return ($line_array[$offset]);
      } else {
  $offset -= $no_newlines;
      }
    }
    return "";
  }
  
  /**
   * @access private
   */
  function _data ($offset) {

# Note the fact that we have located the bin details
    $this->binfound = 'true';

    $buffer = $this->_get_data($this->fh_data, $offset - 1);
    list ($scheme,$issuer,$country,$requirements) = explode(chr(0), $buffer);
    $this->scheme=$scheme;
    $this->issuer=$issuer;
    $this->country=$country;
    $this->requirements=$requirements;

    dc_log(2, "Requirements " . $requirements . "| Scheme $scheme | Issuer $issuer | Country $country ");

    return $this;
  }

  /**
   * Returns the card scheme, if known. e.g. VISA, Mastercard
   *
   * @access public
   * @return string Card scheme
   */
  function scheme () {
    return $this->scheme;
  }
  
  /**
   * Returns the card issuer, if known. e.g. Royal Bank of Scotland
   *
   * @access public
   * @return string Card issuer
   */
  function issuer () {
    return $this->issuer;
  }
  
  /**
   * Returns the number of digits in the PAN, or 0 if not known.
   *
   * @access public
   * @return integer Number of digits in PAN
   */
  function digits () {
    return($this->requirements & 31);
  }

  /**
   * Returns the country of issue, if known.
   *
   * @access public
   * @return string Country of issue
   */
  function country () {
    return $this->country;
  }

  /**
   * Returns true if the card number is valid (passes luhn check)
   *
   * @access public
   * @return boolean 
   */
  function validnumber () {
    return $this->_luhn();
  }

  /**
   * Returns true if an issue number is required for this card, false otherwise
   *
   * @access public
   * @return boolean
   */
  function issuenumber () {
    return (($this->requirements & 192)/64);
  }
  
  /**
   * Returns true if a start date is required for this card, false otherwise.
   *
   * @access public
   * @return boolean
   */
  function startdate () {
    return (($this->requirements & 32)!=0);
  }

  /**
   * Returns true if a client password required for this card, false otherwise.
   *
   * @deprecated This method is deprecated.
   * @access public
   * @return boolean
   */
  function cpass () {
    return (($this->requirements & 256)!=0);
  }
  
  /**
   * Returns true if this card supports non-gbp currencies, false otherwise.
   *
   * @access public
   * @return boolean
   */
  function nongbp () {
    return (($this->requirements & 1024)!=0);
  }
  
  /**
   * Returns true if this card supports Line Item Detail, false otherwise.
   *
   * @access public
   * @return boolean
   */
  function supportslid () {
    return (($this->requirements & 512)!=0);
  }
  
  /**
   * @access private
   */
  function _luhn () {
    $pan=$this->pan;

    $total = 0;
    $valid = 1;
    $numOfDigits = 0 - strlen($pan);

    $i = -1;
    while ($i >= $numOfDigits){
      if (($i % 2) == 0){
  $double = 2 * (substr($pan, $i, 1));
  $total += substr($double,0,1);
  if (strlen($double > 1)){
    $total += substr($double,1,1);
  }
      } else {
  $total += substr($pan, $i, 1);
      }
      $i--;
    }

    if (($total % 10) != 0){
      $valid = 0;
    }

    $this->validnumber=$valid;
    return ($valid);
  }
  
  /**
   * @access private
   */
  function _cccheck () {

# Check we have located the bin details.  If not, there ain't
# much point in going any further.
    if (!$this->binfound) {
      return;
    }

# check length
    $expected_length = $this->digits();
    $mode = $this->request->get("Request.Transaction.TxnDetails.mode");
      
# ficticious test card, let it through
    if ($expected_length == 0 and preg_match("/test/i", $mode)){
      return;
    }
      
# is it valid - pass luhn check?
    if (!$this->validnumber()) {
      return $this->do_failure("-25", "Bad checksum");
    }

    if ($expected_length != 0) {
      if (strlen ($this->pan) > $expected_length ) {
  return ($this->do_failure("-26", "card number too long"));
      }
      if (strlen ($this->pan) < $expected_length) {
  return $this->do_failure("-26", "card number too short");
      }
    }

# check the expiry date
    if (!$this->request->get("Request.Transaction.CardTxn.Card.expirydate")) {
      return $this->do_failure("-5", "Expiry date required");
    }
    
    $expiry = $this->request->get("Request.Transaction.CardTxn.Card.expirydate");
    if (preg_match("/^\d{2}\/\d{2}$/", $expiry, $matches) == 0) {
      return $this->do_failure("-23", "Invalid Expiry date");
    }
      
# check issue number/startdate
    if (!$this->request->get("Request.Transaction.CardTxn.Card.issuenumber") and $this->issuenumber() != 0){
      return $this->do_failure("-5", "Issuenumber required");
    } else if ($this->issuenumber() != 0) {
      $issuenumber = $this->request->get("Request.Transaction.CardTxn.Card.issuenumber");
      if (preg_match("/^\d+$/", $issuenumber) == 0) {
  return $this->do_failure("-27", "Bad issue");
      }
    }

    if (!$this->request->get("Request.Transaction.CardTxn.Card.startdate") and $this->startdate() != 0){
      return $this->do_failure("-5", "Startdate required");
    } else if ($this->request->get("Request.Transaction.CardTxn.Card.startdate")) {
      $startdate = $this->request->get("Request.Transaction.CardTxn.Card.startdate");
      if (preg_match("/^\d{2}\/\d{2}$/", $startdate) == 0) {
  return $this->do_failure("-28","Bad start date");
      }
    }
    return;
  }
 
  /**
   * Performs client side validation of the card identified by the PAN in the supplied DataCash_Document request.
   *
   * Returns NULL if the supplied request passes validation.  Otherwise returns a simulated DataCash response 
   * in the following form:
   *
   * <Response><br />
   *   <status>status_code</status><br />
   *   <reason>reason</reason><br />
   *   <merchantreference>ref</merchantreference><br />
   * </Response><br />
   *
   * @access public
   * @param DataCash_Document $request
   * @return DataCash_Document|NULL
   */
  function validation ($request) {

# Get the DataCash::Request object
    $this->request = $request;

  
# Get the PAN before we try to find the binary details, in case
# there are any non-numerics in it
    $this->pan= $this->request->get("Request.Transaction.CardTxn.Card.pan");
    $this->pan = preg_replace("/\D/", "", $this->pan);
  
# First, let's find the details from the binary file
    $this->_find(0);

# Note DataCash_Document::get() *can* take two arguments although only one is specified in the prototype.
    if ($this->request->get("Request.Transaction.CardTxn", FALSE)) {
# do credit card checks
      if ($this->pan == "") {
  return $this->do_failure("-5", "card number required");
      }
      $response = $this->_cccheck();
      if (!is_null($response)) return $response;
    }
      
    if ($this->request->get("Request.Transaction.ChequeTxn")) {
    #  $this->_chequecheck();
    }

    if ($this->request->get("Request.Transaction.TxnDetails.merchantreference")) {
      $response = $this->_refcheck();
      if (!is_null($response)) return $response;
    } else {
      return $this->do_failure("-5", "reference field required");
    }

      
    return;
  }
  
  /**
   * @access private
   */
  function _refcheck () {
  
    $ref = $this->request->get("Request.Transaction.TxnDetails.merchantreference");
    if(strlen ($ref) < 6 || strlen($ref) > 30 || preg_match ("/[^\w-]/", $ref)) {
      return $this->do_failure("-22","Invalid reference");
    }
  
  }
  
  /**
   * @access private
   */
  function do_failure ($status, $reason) {

    $merchantreference = $this->request->get("Request.Transaction.TxnDetails.merchantreference");

    $xml = new DataCash_Document("Response");
    $xml->set("Response.status", $status);
    $xml->set("Response.reason", $reason);
    $xml->set("Response.merchantreference", $merchantreference);

    return $xml;

  }

}

?>
