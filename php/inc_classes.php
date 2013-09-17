<?php
	/*
	 * inc_classes.php
	 * 
	 * Stores information about a Gateway
	 */
	class Gateway {
		protected $setEC;		// The method (or equivalent) corresponding to SetExpressCheckout
		protected $getECD;		// The method (or equivalent) corresponding to GetExpressCheckoutDetails
		protected $doEC;		// The method (or equivalent) corresponding to DoExpressCheckout
		protected $auth;		// The method (or equivalent) corresponding to DoAuthorization
		protected $references;	// The reference(s) used by the gateway to recognise a transaction
		/*
		 * Specify the name of the Gateway 
		 */
		function __construct($newSet, $newGet, $newDo, $newAuth, $newReferences) {
			self::setDetail(setEC, $newSet);
			self::setDetail(getECD, $newGet);
			self::setDetail(doEC, $newDo);
			self::setDetail(auth, $newAuth);
			self::setDetail(references, $newReferences);
		}
		
		/*
		 * Set the a given detail associated with this gateway
		 */
		function setDetail($detail, $value) {
			$this->$detail = $value;
			//echo("The " . $detail . " has been set with value: " . $this->$detail . "<br>");
		}
		
		function getDetail($detail) {
			return $this->$detail;
		}
	}
?>