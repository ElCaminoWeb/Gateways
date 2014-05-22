<?php

/*
 * inc_classes.php
 * 
 * Stores information about a Gateway
 */

class Gateway {

    protected $setEC;  // The method (or equivalent) corresponding to SetExpressCheckout
    protected $getECD;  // The method (or equivalent) corresponding to GetExpressCheckoutDetails
    protected $doEC;  // The method (or equivalent) corresponding to DoExpressCheckout
    protected $auth;  // The method (or equivalent) corresponding to DoAuthorization
    protected $masspay;  // The method (or equivalent) corresponding to MassPay
    protected $references; // The reference(s) used by the gateway to recognise a transaction

    /*
     * Specify the name of the Gateway 
     */

    public function __construct($newSet, $newGet, $newDo, $newAuth, $newMassPay, $newReferences) {
        self::setDetail('setEC', $newSet);
        self::setDetail('getECD', $newGet);
        self::setDetail('doEC', $newDo);
        self::setDetail('auth', $newAuth);
        self::setDetail('masspay', $newMassPay);
        self::setDetail('references', $newReferences);
    }

    /*
     * Set the a given detail associated with this gateway
     */

    public function setDetail($detail, $value) {
        $this->$detail = $value;
        //echo("The " . $detail . " has been set with value: " . $this->$detail . "<br>");
    }

    public function getDetail($detail) {
        return $this->$detail;
    }

}

?>