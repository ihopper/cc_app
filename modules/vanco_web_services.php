<?php 

/***************************************************************************************/
/* Function Name: VANCO_LOGIN */
/* Description: */
/***************************************************************************************/
function VANCO_LOGIN() {

	global $vanco_vars;

	$timestamp =  date("Y-m-d H:i:s");

	//Get the user's Vanco ID and Password from the database
	
	//Build the XML string
	$ReqBody="<VancoWS>
				<Auth>
					<RequestType>Login</RequestType>
					<RequestID>" . $vanco_vars['RequestID'] . "</RequestID>
					<RequestTime>'$timestamp'</RequestTime>
					<Version>2</Version>
				</Auth>
				<Request>
					<RequestVars>
						<UserID>ES8377WS</UserID>
						<Password>test1234</Password>
					</RequestVars>
				</Request>
			</VancoWS>";
	
	//Perform the Vanco Login method.
	$response = VANCO_TRANSACT($ReqBody);
	
	//Parse XML response.
	$xml_vars = VANCO_PARSE_XML($response);

	//Get variables from the parsed XML array
	foreach ($xml_vars as $xml_var) {
		if ($xml_var['tag'] == 'SESSIONID') {
			$vanco_vars['SessionID'] = $xml_var['value'];
		} //end if
	} // end foreach

}

/***************************************************************************************/
/* Function Name: VANCO_GET_PAYMENT_METHOD */
/* Description: */
/***************************************************************************************/

function VANCO_GET_PAYMENT_METHOD($vanco_vars) {

	global $vanco_vars;

	$timestamp =  date("Y-m-d H:i:s");

	//Use the Vanco login information to build the XML string.
	$ReqBody = "<VancoWS>
					<Auth>
						<RequestType>EFTGetPaymentMethod</RequestType>
						<RequestID>" . $vanco_vars['RequestID'] . "</RequestID>
						<RequestTime>" . $timestamp . "</RequestTime>
						<SessionID>" . $vanco_vars['SessionID'] . "</SessionID>
						<Version>2</Version>
					</Auth>
					<Request>
						<RequestVars>
							<ClientID>" . $vanco_vars['ClientID'] . "</ClientID>
							<CustomerRef>" . $vanco_vars['CustomerRef'] . "</CustomerRef>
						</RequestVars>
					</Request>
				</VancoWS>";
//echo "XMLSENDSTRING: " . $ReqBody;

	//Perform the Vanco Login method.
	$response = VANCO_TRANSACT($ReqBody);
	
	//Parse XML response.
	$xml_vars = VANCO_PARSE_XML($response);

	//echo $response;
	//print_r($xml_vars);


	//Get variables from the parsed XML array
	foreach ($xml_vars as $xml_var) {
		//Check to see if this method is the default
		//if ($xml_var['tag'] == 'DEFAULT' && $xml_var['value'] == 'YES') {				
			if ($xml_var['tag'] == 'PAYMENTMETHODREF') {
				$vanco_vars['PaymentMethodRef'] = $xml_var['value'];
			} else if ($xml_var['tag'] == 'ACCOUNTTYPE') {
				$vanco_vars['AccountType'] = $xml_var['value'];
			} else if ($xml_var['tag'] == 'ACCOUNTNUMBER') {
				$vanco_vars['AccountNumber'] = $xml_var['value'];
			} else if ($xml_var['tag'] == 'ROUTINGNUMBER') {
				$vanco_vars['RoutingNumber'] = $xml_var['value'];
			} else if ($xml_var['tag'] == 'CARDTYPE') {
				$vanco_vars['CardType'] = $xml_var['value'];
			} //end if
		//} //end if
	} // end foreach

	//Return the results
	return $vanco_vars;
}

/***************************************************************************************/
/* Function Name: VANCO_ADD_TRANSACTION */
/* Description: */
/***************************************************************************************/

function VANCO_ADD_TRANSACTION($vanco_vars) {

	global $vanco_vars;

	$timestamp =  date("Y-m-d H:i:s");

	//Set the transaction type code
	if ($vanco_vars['AccountType'] == 'CC') {
		$TransactionTypeCode = '';
	} else {
		$TransactionTypeCode = 'WEB';
	} //end if
	
	
	//Store the amount and userid in the database for later. No other transaction information is stored.
	
	
	//Build the XML string
	//With Funds
	$ReqBody = "<VancoWS>
					<Auth>

						<RequestType>EFTAddTransaction</RequestType>
						<RequestID>" . $vanco_vars['RequestID'] . "</RequestID>
						<RequestTime>" . $timestamp . "</RequestTime>
						<SessionID>" . $vanco_vars['SessionID'] . "</SessionID>
						<Version>2</Version>

					</Auth>
					<Request>
						<RequestVars>
							<ClientID>" . $vanco_vars['ClientID'] . "</ClientID>
							<CustomerRef>" . $vanco_vars['CustomerRef'] . "</CustomerRef>
							<PaymentMethodRef>" . $vanco_vars['PaymentMethodRef'] . "</PaymentMethodRef>
							<Funds>
								<Fund>
									<FundID>0001</FundID>
									<FundAmount>" . $vanco_vars['FundAmount'] . "</FundAmount>
								</Fund>
							</Funds>
							<FrequencyCode>" . $vanco_vars['FrequencyCode'] . "</FrequencyCode>
							<StartDate>0000-00-00</StartDate>
							<TransactionTypeCode>" . $TransactionTypeCode . "</TransactionTypeCode>
						</RequestVars>
					</Request>
				</VancoWS>";	
	
//echo "XMLSENDSTRING: <br />" . $ReqBody;

	//Perform the Vanco Add Transaction method.
	$response = VANCO_TRANSACT($ReqBody);
	
	//Parse XML response.
	$xml_vars = VANCO_PARSE_XML($response);


	//Get any errors
	//Get variables from the parsed XML array
	foreach ($xml_vars as $xml_var) {
		if ($xml_var['tag'] == 'ERRORCODE') {
			$vanco_vars['ErrorCode'] = $xml_var['value'];
		} //end if
		if ($xml_var['tag'] == 'ERRORMESSAGE') {
			$vanco_vars['ErrorMessage'] = $xml_var['value'];
		} //end if
	} // end foreach


	//echo $response;
	//print_r($xml_vars);
	return $vanco_vars;
}

/***************************************************************************************/
/* Function Name: VANCO_LOGOUT */
/* Description: */
/***************************************************************************************/

function VANCO_LOGOUT($vanco_vars) {

	global $vanco_vars;

	$timestamp =  date("Y-m-d H:i:s");

	//Build the XML string
	$ReqBody = "<VancoWS>
					<Auth>
						<RequestType>Logout</RequestType>
						<RequestID>" . $vanco_vars['RequestID'] . "</RequestID>
						<RequestTime>" . $timestamp . "</RequestTime>
						<SessionID>" . $vanco_vars['SessionID'] . "</SessionID>
						<Version>2</Version>
					</Auth>
					<Request>
						<RequestVars>
							<Logout></Logout>
						</RequestVars>
					</Request>
				</VancoWS>";

	//Perform the Vanco Add Transaction method.
	$response = VANCO_TRANSACT($ReqBody);
	
	//Parse XML response.
	$xml_vars = VANCO_PARSE_XML($response);


	//Get any errors
	//Get variables from the parsed XML array
	foreach ($xml_vars as $xml_var) {
		if ($xml_var['tag'] == 'ERRORCODE') {
			$vanco_vars['ErrorCode'] = $xml_var['value'];
		} //end if
		if ($xml_var['tag'] == 'ERRORMESSAGE') {
			$vanco_vars['ErrorMessage'] = $xml_var['value'];
		} //end if
	} // end foreach

	//echo $response;
	//print_r($xml_vars);
	return $vanco_vars;
}


/***************************************************************************************/
/* Function Name: VANCO_TRANSACT */
/* Description: */
/***************************************************************************************/

function VANCO_TRANSACT($ReqBody) {


	//--- Open Connection ---
	$socket = fsockopen("ssl://www.vancodev.com", 443, $errno, $errstr, 15);

	if (!$socket) {

		echo 'Fail<br>';
		$Result['errno']=$errno;
		$Result['errstr']=$errstr;
		return $Result;

	} else {

		//--- Create Header ---
		$ReqHeader = "POST /cgi-bin/wstest2.vps HTTP/1.1\n";
		$ReqHeader .= "Host: " . $_SERVER['HTTP_HOST'] . "\n";
		$ReqHeader .= "User-Agent: " . $_SERVER['HTTP_USER_AGENT'] . "\n";
		$ReqHeader .= "Content-Type: application/x-www-form-urlencoded\n";
		$ReqHeader .= "Content-length: " . strlen($ReqBody) . "\n";
		$ReqHeader .= "Connection: close\n\n";
		$ReqHeader .= $ReqBody . "\n\n";

		// --- Send XML ---
		fwrite($socket, $ReqHeader);

		// --- Retrieve XML ---
		while (!feof($socket)) {
			$_return .= fgets($socket, 4096);
		} //end while

		fclose($socket);

		//Get the XML string from the response
		$xml = strstr($_return, '<?xml');

		//Return the results
		//echo $xml;
		return $xml;

	} //end if
}


/***************************************************************************************/
/* Function Name: VANCO_PARSE_ERRORS */
/* Description: */
/***************************************************************************************/

function VANCO_PARSE_XML($response){


//echo "DEBUG: " . $response;

	//Collect the returned XML string
	$err_msg = '<Response>
					<Errors>
						<Error>
							<ErrorCode>ERROR_CODE_NUMBER_1</ErrorCode>
							<ErrorDescription>ERROR_DESC_1</ErrorDescription>
							</Error>
							<Error>
							<ErrorCode>ERROR_CODE_NUMBER_2</ErrorCode>
							<ErrorDescription>ERROR_DESC_2</ErrorDescription>
						</Error>
					</Errors>
				</Response>';

	//Create the parser object
	$xmlparser = xml_parser_create();

	//Parse the XML string
	xml_parse_into_struct($xmlparser,$response,$xml_vars);

	//print_r($vanco_vars);

	//Return the array
	return $xml_vars;

}

?>