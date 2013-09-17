<?php
	$dateTime = date("Y:m:d-H:i:s");

	function getDateTime() {
		global $dateTime;
		return $dateTime;
	}

	function createHash($chargetotal, $currency) {
		$storename = "13205400147";
                $sharedSecret = "k49HRjeCxa";

		$stringToHash = $storename . getDateTime() . $chargetotal . $currency . $sharedSecret;

		$ascii = bin2hex($stringToHash);

		return sha1($ascii);
	}

?>
