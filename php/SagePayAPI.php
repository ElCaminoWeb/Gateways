<?php
	
	function getResponse($input, $end) {
        // Security Parameters
		/* UK Account */
		$header = array("TxType" => "PAYMENT");
		$header = array_merge($header, array("Vendor" => "sptest"));
		$rand = time();
		
		$url = "https://test.sagepay.com/Simulator/VSPServerGateway.asp?Service=VendorRegisterTx";
		/*$header = array_merge($header, array("VendorTxCode" => $rand));
		$header = array_merge($header, array("CardType" => "PAYPAL"));
		$header = array_merge($header, array("Description" => "Test"));
		// Load XML doc
		$xml = new DOMDocument('1.0');
		//$xml->load("SP1_Set.xml");	
		$xml->formatOutput = true;
		$basket = $xml->createElement('basket');
		$basket = $xml->appendChild($basket);
		// Convert array to correctly formatted string
        $paramArray = convert($input, $xml);
		$data = setUpParams(array_merge($header,$paramArray));
		//return $data;
		//echo $data;*/		
		// Talk to API Server
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $end);
        curl_setopt($ch, CURLOPT_POSTFIELDS, setUpParams($header));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); 
        curl_setopt($ch, CURLOPT_HEADER, true); 
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $response = curl_exec($ch); 
        $curlerr = curl_error($ch);
        curl_close($ch);
        $reponse = stripslashes($response);
        $splitLen = stripos($response, "charset=utf-8") + 17;
        $newResp = str_split($response, $splitLen);
        $array2 = $newResp[1];
        $len = count($newResp);
        for($inc = 1; $inc < ($len - 1); $inc ++){
        	$array2 .= $newResp[$inc + 1];
        }
        $array = explode("&",$array2);
        $arrayLen = count($array);
        for($inc = 0; $inc < $arrayLen; $inc ++) {
        	$newArray[$inc] = explode("=",$array[$inc]);
        	$finalArray[$newArray[$inc][0]] = urldecode($newArray[$inc][1]);
        }
        if(!isset($finalArray['METHOD'])) { $finalArray['METHOD'] = $method; }
        $finalArray['MESSAGE'] = preg_replace("/&/","\n",urldecode(setUpParams($finalArray)));
        return $finalArray;
		
		// Decode API response
//        $decoded = decodeResponse($response); 
       // return $response; 
	}
	function convert($inp, $xml) {
		$titles = array_keys($inp);
		$numberOfParams = sizeOf($titles);
		$basket = false;
		$new = array();
		for ($inc = 0; $inc < $numberOfParams; $inc ++) {
			$cur = $titles[$inc];
			$newTitle = convertParam($cur);
			if ($newTitle == "basket") {
				$basket = true;
				updateBasket($xml, $cur, $inp[$cur]);
			} else {
				if ($newTitle != -1) {
					$new=array_merge($new, array($newTitle => $inp[$cur]));
				}
			}
		}
		if ($basket) {$new = array_merge($new, array("BasketXML"=> $xml->saveXML())); }
		return $new;
	}
	function convertParam($param) {
		if(preg_match("/LOCALECODE/",$param)) {$new = "Language"; }
		else if(preg_match("/^PAYMENTREQUEST_0_AMT/", $param)) {$new = "Amount"; }
		else if(preg_match("/PAYMENTREQUEST_0_CURRENCYCODE/", $param)) { $new = "Currency"; }
		else if(preg_match("/^L_PAYMENTREQUEST_/", $param)) { $new = "basket"; } 
		else if(preg_match("/PAYMENTREQUEST_0_SELLERPAYPALACCOUNTID/", $param)) { $new="basket"; }
		else { $new = -1 ;}
		return $new;
	}
	function updateBasket($xml, $title, $val) {
		$basket = $xml->getElementsByTagName('basket')->item(0);
		if ($title == "PAYMENTREQUEST_0_SELLERPAYPALACCOUNTID") {
				$agentId = $xml->createElement('agentId',$val);
				$agentId = $basket->appendChild($agentId);
		} else	{
			$item = $xml->getElementsByTagName('item')->item(0);
			if (!isset($item)) { 
				$item = $xml->createElement('item'); 
				$item = $basket->appendChild($item);
			}
			switch ($title) {
				case "L_PAYMENTREQUEST_0_NAME0":		
					$elmt = $xml->createElement('productSku', $val);
					break;
				case "L_PAYMENTREQUEST_0_DESC0":		
					$elmt = $xml->createElement('description', $val);
					break;
				case "L_PAYMENTREQUEST_0_QTY0":		
					$elmt = $xml->createElement('quantity', $val);
					break;
				case "L_PAYMENTREQUEST_0_AMT0":		
					$elmt = $xml->createElement('unitGrossAmount', $val);
					break;			
			}
			$elmt = $item->appendChild($elmt);
		}
	}
	function xmlToString($xml) {
		$str = "<basket>";
		$agents = $xml->getElementsByTagName('agentId');
		$numberOfAgents = $agents->length;
		$agentId = $agents->item(0)->textContent;
		$str .= "<agentId>" . $agentId . "</agentId>";
		$str .= "<item><productSku>" . $xml->getElementsByTagName('productSku')->item(0)->textContent . "</productSku>";
		$str .= "<description>" . $xml->getElementsByTagName('description')->item(0)->textContent . "</description>";
		$str .= "<quantity>" . $xml->getElementsByTagName('quantity')->item(0)->textContent . "</quantity>";
		$str .= "<unitGrossAmount>" . $xml->getElementsByTagName('unitGrossAmount')->item(0)->textContent . "</unitGrossAmount>";
		$str .= "</item></basket>";
		
		return $str;
		
	}
	
?>