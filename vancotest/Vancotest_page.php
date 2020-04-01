<?php


//insert and continue to next page

if( $userdata['session_logged_in'] )
{
	$UserId = $userdata['user_id'];
	$template->assign_vars(array('USERID'=>$UserId,
								 'GROUP_ID' => $g_id));
	
	// Donor details
	$D_Firstname = $userdata['users_fname'];
	$D_Lastname = $userdata['users_lname'];
	$D_Address = $userdata['user_street_address'];
	$D_City = $userdata['user_city'];
	$D_State = $userdata['user_state'];
	$D_Zip = $userdata['user_postalcode'];
	$D_phone_number = $userdata['user_vanco_phonenumber'];

	$template->assign_vars(array(
			'DONOR_FIRSTNAME'=>$D_Firstname ,
			'DONOR_LASTNAME'=>$D_Lastname ,
			'DONOR_ADDRESS'=>$D_Address,
			'DONOR_CITY'=>$D_City,
			'DONOR_STATE'=>$D_State,
			'DONOR_ZIP'=>$D_Zip,
			'DONOR_PHONE_NUMBER'=>$D_phone_number));

		///// May 19th 2009
		if($HTTP_GET_VARS['donation_Id']>0 && isset($HTTP_GET_VARS['donation_Id']))
		{
			$ID = $HTTP_GET_VARS['donation_Id'];
			
			$Info = $InstanceReDistribution->GetVancoPaymentInfo($ID, 'phpbb_Vanco_temp');
			
			$Selected='';
			$Selected_df='';
			$PaymentOptions='';
			
			$display_status_s='';
			$display_status_ck='';
			$display_status_onetime='';
			$display_status_semimonthly='';
			$display_status_monthly1st='';
			$display_status_monthly15th='';
			$display_status_quarterly='';
			
			$Donation_Frequency_Options='';
			$Monthly_Options='';
			
			//Selecting Routing number, Account number, common fund, seed fund
			$routing_number=$Info['routing_number'];
			$account_number=$Info['account_number'];
			$common_fund=$Info['common_fund'];
			$seed_fund=$Info['seed_fund'];
			$spl_instructions=$Info['special_instructions'];

			//Selecting payment method type
			$Options = array('Choose One','Checking Account','Savings Account');
			$Selected = $Info['account_type'];
			
			if($Selected=='0')
			{
				$display_status_ck='style="display:none;"';
				$display_status_s='style="display:none;"';
			}
			else if($Selected=='1')
			{
				$display_status_ck='style="display:block;"';
				$display_status_s='style="display:none;"';
			}
			else if($Selected=='2')
			{
				$display_status_ck='style="display:none;"';
				$display_status_s='style="display:block;"';
			}
			
			for ($i= 0;$i<count($Options); $i++)
			{
				$SelectedIndex='';
				if ($Selected == $i)
				{
					$SelectedIndex='selected';
				}
				$PaymentOptions.= "<option value=$i $SelectedIndex> $Options[$i] </option>";
			}


			//Selecting donation frequency
			$donation_frq_Options = array('Choose One', 'One-Time', 'Semi-Monthly - 1st and 15th', 'Monthly on the 1st', 'Monthly on the 15th', 'Quarterly on the 1st');
			$Selected_df = $Info['donation_frequency'];
			
			if($Selected_df=='0')
			{
				$display_status_onetime='style="display:none;"';
				$display_status_semimonthly='style="display:none;"';
				$display_status_monthly1st='style="display:none;"';
				$display_status_monthly15th='style="display:none;"';
				$display_status_quarterly='style="display:none;"';
			}
			else if($Selected_df=='1')
			{
				$display_status_onetime='style="display:block;"';
				$display_status_semimonthly='style="display:none;"';
				$display_status_monthly1st='style="display:none;"';
				$display_status_monthly15th='style="display:none;"';
				$display_status_quarterly='style="display:none;"';
			}
			else if($Selected_df=='2')
			{
				$display_status_onetime='style="display:none;"';
				$display_status_semimonthly='style="display:block;"';
				$display_status_monthly1st='style="display:none;"';
				$display_status_monthly15th='style="display:none;"';
				$display_status_quarterly='style="display:none;"';
			}
			else if($Selected_df=='3')
			{
				$display_status_onetime='style="display:none;"';
				$display_status_semimonthly='style="display:none;"';
				$display_status_monthly1st='style="display:block;"';
				$display_status_monthly15th='style="display:none;"';
				$display_status_quarterly='style="display:none;"';
			}
			else if($Selected=='4')
			{
				$display_status_onetime='style="display:none;"';
				$display_status_semimonthly='style="display:none;"';
				$display_status_monthly1st='style="display:none;"';
				$display_status_monthly15th='style="display:block;"';
				$display_status_quarterly='style="display:none;"';
			}
			else if($Selected_df=='5')
			{
				$display_status_onetime='style="display:none;"';
				$display_status_semimonthly='style="display:none;"';
				$display_status_monthly1st='style="display:none;"';
				$display_status_monthly15th='style="display:none;"';
				$display_status_quarterly='style="display:block;"';
			}
			
			for ($i= 0;$i<count($donation_frq_Options); $i++)
			{
				$SelectedIndex_df='';
				if ($Selected_df == $i)
				{
					$SelectedIndex_df='selected';
				}
				$Donation_Frequency_Options.= "<option value=$i $SelectedIndex_df> $donation_frq_Options[$i] </option>";
			}
			

			//Selecting month of donation
			$months= array('', 'Jan', 'Feb', 'Mar', 'Aprl', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
			$Selected_month = $Info['month'];
			
			for ($i=1; $i<count($months); $i++)
			{
				$SelectedIndex_month='';
				if ($Selected_month == $i)
				{
					$SelectedIndex_month='selected';
				}
				$Monthly_Options.= "<option value=$i $SelectedIndex_month> $months[$i] </option>";
			}
		}
		else
		{
				$ID=0;
				$display_status_ck='style="display:none;"';
				$display_status_s='style="display:none;"';

				$display_status_onetime='style="display:none;"';
				$display_status_semimonthly='style="display:none;"';
				$display_status_monthly1st='style="display:none;"';
				$display_status_monthly15th='style="display:none;"';
				$display_status_quarterly='style="display:none;"';

				$PaymentOptions.='<option value="0" SELECTED>Choose One</option>											<option value="1">Checking Account</option>
								<option value="2">Savings Account</option>';

				$routing_number='';
				$account_number='';
			
				$Donation_Frequency_Options.='<option value="0" SELECTED>Choose One</option>
 											  <option value="1">One-Time</option>
											  <option value="2">Semi-Monthly - 1st and 15th</option>
											  <option value="3">Monthly on the 1st</option>
											  <option value="4">Monthly on the 15th</option>
											  <option value="5">Quarterly on the 1st</option>';
				
				$Monthly_Options.='<option value="01">Jan</option>
									<option value="02">Feb</option>
									<option value="03">Mar</option>
									<option value="04">Apr</option>
									<option value="05">May</option>
									<option value="06">Jun</option>
									<option value="07">Jul</option>
									<option value="08">Aug</option>
									<option value="09">Sep</option>
									<option value="10">Oct</option>
									<option value="11">Nov</option>
									<option value="12">Dec</option>';

		}
			
		$template->assign_vars(array('PAYMENT_ID'=>$ID,
									 'PAYMENT_OPTIONS'=>$PaymentOptions,
									 'DISPLAY_STATUS'=>'style="display:none;"',
									 'DISPLAY_STATUS_CK'=>$display_status_ck,
									 'DISPLAY_STATUS_S'=>$display_status_s,

						    		 'DISPLAY_STATUS_ONETIME'=>$display_status_onetime,
									 'DISPLAY_STATUS_SEMI_MONTHLY'=>$display_status_semimonthly,
									 'DISPLAY_STATUS_MONTHLY_1ST'=>$display_status_monthly1st,
									 'DISPLAY_STATUS_MONTHLY_15TH'=>$display_status_monthly15th,
									 'DISPLAY_STATUS_QUARTERLY'=>$display_status_quarterly,
									 
									 'ROUTING_NUMBER'=> $routing_number,
									 'ACCOUNT_NUMBER'=>$account_number,
										 
									 'COMMON_FUND'=> number_format($common_fund, 2, '.', ''),
									 'SEED_FUND'=> number_format($seed_fund, 2, '.', ''),
										 
									 'DONATION_FREQ_OPTIONS'=>$Donation_Frequency_Options, 
									 'MONTH_OPTIONS'=> $Monthly_Options,
									 'SPL_INSTRUCTIONS' => $spl_instructions)); 


	///// May 19th 2009

	$continue= isset($HTTP_POST_VARS['btContinue']) ? $_POST['btContinue'] : '';
	//page 1 continue
	if($continue == 'CONTINUE')
	{		
		//$donor_id=$userdata['user_id'].'-'.$_GET['g'].'-'.$userdata['username'];
		$date=getdate();		$donor_id=$userdata['username'].'-'.$date['year'].$date['mon'].$date['mday'].$date['hours'].$date['minutes'].$date['seconds'];
		
		if($_POST['donationFrequency']=='1')
		{
			$day=$_POST['date2'];
			$month=$_POST['month2'];
			$year=$_POST['year2'];
		}

		if($_POST['donationFrequency']=='2')
		{
			$day=$_POST['date3'];
			$month=$_POST['month3'];
			$year=$_POST['year3'];
		}
		
		if($_POST['donationFrequency']=='3')
		{
			$day=$_POST['date4'];
			$month=$_POST['month4'];
			$year=$_POST['year4'];
		}
		
		if($_POST['donationFrequency']=='4')
		{
			$day=$_POST['date5'];
			$month=$_POST['month5'];
			$year=$_POST['year5'];
		}
		
		if($_POST['donationFrequency']=='5')
		{
			$day=$_POST['date6'];
			$month=$_POST['month6'];
			$year=$_POST['year6'];
		}
		
		$total_date=$month."/".$day."/".$year;
		
		if($_POST['YesNo']=='No' || $_POST['YesNo']=='Yes')
		{
			$D_Firstname = $_POST['FirstName'];
			$D_Lastname = $_POST['LastName'];
			$D_Address = $_POST['Address'];
			$D_City = $_POST['City'];
			$D_State = $_POST['State'];
			$D_Zip = $_POST['Zip'];
			$D_phone_number = $_POST['phonenumber'];
		}
	/*	else if($_POST['YesNo']=='Yes')
		{
			$D_Firstname = $userdata['users_fname'];
			$D_Lastname = $userdata['users_lname'];
			$D_Address = $userdata['user_street_address'];
			$D_City = $userdata['user_city'];
			$D_State = $userdata['user_state'];
			$D_Zip = $userdata['user_postalcode'];
			$D_phone_number = $userdata['user_vanco_phonenumber'];
		}   */
		
		if($_POST['update']=='1')
		{
			//	echo 'You are asking me to update your profile';
			$sql = "UPDATE " . USERS_TABLE . " SET 
			users_fname= '" . str_replace("\'", "''", $_POST['FirstName']) . "',
			users_lname= '" . str_replace("\'", "''", $_POST['LastName']) . "',
			user_street_address= '" . str_replace("\'", "''", $_POST['Address']) . "',
			user_city=	 '" . str_replace("\'", "''", $_POST['City']) . "',
			user_state= '" . str_replace("\'", "''", $_POST['State']) . "',
			user_postalcode= '" . str_replace("\'", "''", $_POST['Zip']) . "',
			user_vanco_phonenumber= '" . str_replace("\'", "''", $D_phone_number) . "'
			WHERE  user_id = ". $userdata['user_id'];

			if ( !($result = $db->sql_query($sql, BEGIN_TRANSACTION)) )
			{
				message_die(GENERAL_ERROR, 'Could not update data into users table', '', __LINE__, __FILE__, $sql);
			}
		}

		$template->assign_vars(array(
			'RT_DONOR_REF_ID' => $donor_id,
			'DONOR_FIRSTNAME'=>$D_Firstname ,
			'DONOR_LASTNAME'=>$D_Lastname ,
			'DONOR_ADDRESS'=>$D_Address,
			'DONOR_CITY'=>$D_City,
			'DONOR_STATE'=>$D_State,
			'DONOR_ZIP'=>$D_Zip,
			'DONOR_PHONE_NUMBER'=>$D_phone_number));

		$donation_from='';
		if($_POST['paymentMethodType']=='2')
		{
			$donation_from.='<tr>
								<td>
									<font size="2"><input type="radio" id="c_account_type" name="account_type" disabled>Checking Account</font>
								</td>
							</tr>
							<tr>
								<td>
									<font size="2"><input type="radio" id="s_account_type" name="account_type" checked="checked">Savings Account</font>
								</td>
							</tr>';
			$paymentMethod = 'Savings Account';
			$paymentMethod_code = 'S';

			$routing_number = $_POST['s_routing_number'];
			$account_number = $_POST['s_account_number'];
		
		}
		else if($_POST['paymentMethodType']=='1')
		{
			$donation_from.='<tr>
								<td>
									<font size="2"><input type="radio" id="c_account_type" name="account_type" checked="checked">Checking Account</font>
								</td>
							</tr>
							<tr>
								<td>
									<font size="2"><input type="radio" id="s_account_type" name="account_type" disabled>Savings Account</font>
								</td>
							</tr>';
			
			$paymentMethod = 'Checking Account';
			$paymentMethod_code = 'C';
			
			$routing_number = $_POST['c_routing_number'];
			$account_number = $_POST['c_account_number'];
		}		
		
		$commonfund = isset($_POST['commonfund']) ? $_POST['commonfund'] : $_GET['commonfund'] ;
		$seedfund = isset($_POST['seedfund']) ? $_POST['seedfund'] : $_GET['seedfund'] ;

		if($_POST['donationFrequency']=='1')
		{
			$sel1=' checked="checked"';
			$sel2=' disabled';
			$sel3=' disabled';
			$sel4=' disabled';
			$sel5=' disabled';
			$donationFrequency = 'One-Time';
			$donationFrequencyCode='O';
		}
		else if($_POST['donationFrequency']=='2')
		{
			$sel1=' disabled';
			$sel2='checked="checked"';
			$sel3=' disabled';
			$sel4=' disabled';
			$sel5=' disabled';
			
			$donationFrequency = 'Semi-Monthly - 1st and 15th';
			$donationFrequencyCode='M';
		}
		else if($_POST['donationFrequency']=='3')
		{
			$sel1=' disabled';
			$sel2=' disabled';
			$sel3=' checked="checked"';
			$sel4=' disabled';
			$sel5=' disabled';
			
			$donationFrequency = 'Monthly on the 1st';
			$donationFrequencyCode='M';
		}
		else if($_POST['donationFrequency']=='4')
		{
			$sel1=' disabled';
			$sel2=' disabled';
			$sel3=' disabled';
			$sel4='checked="checked"';
			$sel5=' disabled';
			
			$donationFrequency = 'Monthly on the 15th';
			$donationFrequencyCode='M';
		}
		else if($_POST['donationFrequency']=='5')
		{
			$sel1=' disabled';
			$sel2=' disabled';
			$sel3=' disabled';
			$sel4=' disabled';
			$sel5='checked="checked"';
						
			$donationFrequency = 'Quarterly on the 1st';
			$donationFrequencyCode='Q';
		}
		
		$donation_start_date = $total_date; //$_POST['donation_start_date'];
		$specialinstructions =  isset($_POST['specialinstructions']) ? $_POST['specialinstructions'] : 'Null';
		
		$template->assign_vars(array(
			'DONATION_FROM' => $donation_from,
			'PAYMENT_METHOD_TYPE'=>$paymentMethod_code,
			'PAYMENT_METHOD'=>$paymentMethod,
			'ROUTING_NUMBER' => $routing_number,
			'ACCOUNT_NUMBER' => $account_number,
			'COMMON_FUND' => number_format($commonfund, 2, '.', ''),
			'SEED_FUND' => number_format($seedfund, 2, '.', ''),
			'TOTAL_FUND' => number_format(($commonfund+$seedfund), 2, '.', ''),
			'DONATION_FREQUENCY'=>$donationFrequency,
			'DONATION_FREQUENCY_CODE'=>$donationFrequencyCode,
			'SEL1' => $sel1,
			'SEL2' => $sel2,
			'SEL3' => $sel3,
			'SEL4' => $sel4,
			'SEL5' => $sel5,
			'DONATION_START_DATE'=>$donation_start_date,
			'SPECIAL_INSTRUCTIONS'=>$specialinstructions,
			'PAYMENT_ID'=>$_GET['paymentId']));		
		
		if($_GET['paymentId']=='0')
		{
			//print_r($_POST); $
			//// Insert data into a temporory table 
			$sql = "INSERT INTO phpbb_Vanco_temp (user_id, rt_donor_ref_id, firstname, lastname, address, city, state, zipcode, account_type, routing_number, account_number, common_fund, seed_fund, donation_frequency, date, month, year, special_instructions) VALUES (".$userdata['user_id'].", '".  $donor_id."', '".  $_POST['FirstName']."', '".  $_POST['LastName']."', '".  $_POST['Address']."', '".  $_POST['City']."', '".  $_POST['State']."', '".  $_POST['Zip']."', '".  $_POST['paymentMethodType']."', '".$routing_number."', '".$account_number."', ".$commonfund.", ".$seedfund.", '".$_POST['donationFrequency']."', '".$day."', '".$month."', '".$year."', '".$specialinstructions."')";

			if(!($result = $db->sql_query($sql, BEGIN_TRANSACTION)) )
			{
				message_die(GENERAL_ERROR, 'Could not insert data into phpbb_Vanco_temp table', '', __LINE__, __FILE__, $sql);
			}
		}
		else if($_GET['paymentId']>0)
		{
			//// Insert data into a temporory table 
			$sql = "UPDATE phpbb_Vanco_temp SET account_type='".$_POST['paymentMethodType']."', routing_number='".$routing_number."', account_number='".$account_number."', common_fund=".$commonfund.", seed_fund=".$seedfund.", donation_frequency='".$_POST['donationFrequency']."', date=".$day.", month=".$month.", year=".$year.", special_instructions='".$specialinstructions."' where id=".$_GET['paymentId'];

			if(!($result = $db->sql_query($sql, BEGIN_TRANSACTION)) )
			{
				message_die(GENERAL_ERROR, 'Could not insert data into phpbb_Vanco_temp table', '', __LINE__, __FILE__, $sql);
			}
		}

		////// Getting the current data and redirect the page to first page
		$sql="SELECT max(id) as MaxId FROM phpbb_Vanco_temp WHERE user_id=".$userdata['user_id'];

		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not query user info from temp table', '', __LINE__, __FILE__, $sql);
		}
		
		$row = $db->sql_fetchrow($result);

		$maxId=$row['MaxId'];
		$template->assign_vars(array(
			'DONATION_TEMP_ID' => $maxId));

		$template->set_filenames(array('body'=>'Vancotest_page2.tpl'));
		$template->pparse('body');
	}
	else if($HTTP_POST_VARS['submit'] == 'SUBMIT')//if Review page submits
	{
		
		/////////////  OLD code from vanco_testing2.php page
		$ReqBody="<VancoWS><Auth><RequestType>Login</RequestType><RequestID>Test</RequestID><RequestTime>".date('Y-m-d H:i:s')."</RequestTime><Version>2</Version></Auth><Request><RequestVars><UserID>ES8377WS</UserID><Password>w9QD2FKf</Password></RequestVars></Request></VancoWS>"; 

		//--- Open Connection --- 
		$socket = fsockopen("ssl://www.vancoservices.com", 443, $errno, $errstr, 15);

		if (!$socket) 
		{ 
				echo 'Fail<br>'; 
				$Result['errno']=$errno; 
				$Result['errstr']=$errstr; 
				return $Result; 
		} 
		else 
		{ 
		   //--- Create Header --- 
			$ReqHeader  = "POST /cgi-bin/ws2.vps HTTP/1.1\n"; 
			$ReqHeader .= "Host: " . $_SERVER['HTTP_HOST'] . "\n"; 
			$ReqHeader .= "User-Agent: " . $_SERVER['HTTP_USER_AGENT'] . "\n"; 
			$ReqHeader .= "Content-Type: application/x-www-form-urlencoded\n"; 
			$ReqHeader .= "Content-length: " . strlen($ReqBody) . "\n"; 
			$ReqHeader .= "Connection: close\n\n"; 
			$ReqHeader .= $ReqBody . "\n\n"; 

			// --- Send XML --- 
			fwrite($socket, $ReqHeader); 

			// --- Retrieve XML --- 
			while (!feof($socket)) 
			{ 
				$_return .= fgets($socket, 4096); 
			} 

			fclose($socket); 

		   //echo $_return; 

			$distinctivetitle=preg_match("/<SessionID>(.*?)<\/SessionID>/", $_return, $matchtitle);
			$S_ID="$matchtitle[1]"; 
		}  

		/// part2 starts here....
		$common_str.='<VancoWS><Auth><RequestType>EFTAddCompleteTransaction</RequestType><RequestID>12345</RequestID><RequestTime>'.date("Y-m-d H:i:s").'</RequestTime><SessionID>'.$S_ID.'</SessionID><Version>2</Version></Auth><Request><RequestVars><ClientID>ES8377</ClientID><CustomerName>'.$_POST['donor_name'].'</CustomerName><CustomerAddress1>'.$_POST['donor_address'].'</CustomerAddress1><CustomerAddress2>Null</CustomerAddress2><CustomerCity>'.$_POST['donor_city'].'</CustomerCity><CustomerState>'.$_POST['donor_state'].'</CustomerState><CustomerZip>'.$_POST['donor_zip'].'</CustomerZip><CustomerPhone>'.$_POST['donor_phone'].'</CustomerPhone><AccountType>'.$_POST['paymentMethodType'].'</AccountType>';

		if($_POST['paymentMethodType']=='S')
		{				
			$common_str.='<AccountNumber>'.$_POST['account_number'].'</AccountNumber><RoutingNumber>'.$_POST['routing_number'].'</RoutingNumber>';
		}
		if($_POST['paymentMethodType']=='C')
		{
			$common_str.='<AccountNumber>'.$_POST['account_number'].'</AccountNumber><RoutingNumber>'.$_POST['routing_number'].'</RoutingNumber>';
		}		

		if($_POST['seed_fund'] > 0 && $_POST['common_fund'] == 0)
		{
			$common_str.='<Funds><Fund><FundID>0002</FundID><FundAmount>'.$_POST['seed_fund'].'</FundAmount></Fund></Funds>';
		}
		else if($_POST['seed_fund'] == 0 && $_POST['common_fund'] > 0)
		{
			$common_str.='<Funds><Fund><FundID>0003</FundID><FundAmount>'.$_POST['common_fund'].'</FundAmount></Fund></Funds>';
		}
		else if($_POST['seed_fund'] > 0 && $_POST['common_fund'] > 0)
		{
			$common_str.='<Funds><Fund><FundID>0002</FundID><FundAmount>'.$_POST['seed_fund'].'</FundAmount></Fund><Fund><FundID>0003</FundID><FundAmount>'.$_POST['common_fund'].'</FundAmount></Fund></Funds>';
		}

		$common_str.='<StartDate>'.$_POST['donation_start_date'].'</StartDate><FrequencyCode>'.$_POST['donationFrequency'].'</FrequencyCode><TransactionTypeCode>WEB</TransactionTypeCode></RequestVars></Request></VancoWS>';

		////////////////// Part 2
		$ReqBody_EFT=$common_str; 
		
		//--- Open Connection --- 
		$socket_EFT = fsockopen("ssl://www.vancoservices.com", 443, $errno, $errstr, 15); 

		if (!$socket_EFT) 
		{ 
			echo 'Fail<br>'; 
			$Result['errno']=$errno; 
			$Result['errstr']=$errstr; 
			return $Result; 
		}
		else
		{ 
			//--- Create Header --- 
			$ReqHeader_EFT  = "POST /cgi-bin/ws2.vps HTTP/1.1\n"; 
			$ReqHeader_EFT .= "Host: " . $_SERVER['HTTP_HOST'] . "\n"; 
			$ReqHeader_EFT .= "User-Agent: " . $_SERVER['HTTP_USER_AGENT'] . "\n"; 
			$ReqHeader_EFT .= "Content-Type: application/x-www-form-urlencoded\n"; 
			$ReqHeader_EFT .= "Content-length: " . strlen($ReqBody_EFT) . "\n"; 
			$ReqHeader_EFT .= "Connection: close\n\n"; 
			$ReqHeader_EFT .= $ReqBody_EFT . "\n\n"; 

			// --- Send XML --- 
			fwrite($socket_EFT, $ReqHeader_EFT); 

			// --- Retrieve XML --- 
			while (!feof($socket_EFT)) 
			{ 
				$_return_EFT .= fgets($socket_EFT, 4096); 
			} 

			//print_r($_return_EFT);
			fclose($socket_EFT); 
			
			$ErrorCode=preg_match("/<ErrorCode>(.*?)<\/ErrorCode>/", $_return_EFT, $ErrorCodeValue);			

			$ErrorCode_value="$ErrorCodeValue[1]"; 
					

			if(!($ErrorCode_value>0) || !(isset($ErrorCode_value)))
			{
				$CustomerReference=preg_match("/<CustomerRef>(.*?)<\/CustomerRef>/", $_return_EFT, $CustRefTitle);
				$PaymentMethodReference=preg_match("/<PaymentMethodRef>(.*?)<\/PaymentMethodRef>/", $_return_EFT, $PaymentRefTitle);
				$TransReference=preg_match("/<TransactionRef>(.*?)<\/TransactionRef>/", $_return_EFT, $TransRefTitle);
				
				$CustomerRef_value="$CustRefTitle[1]"; 
				$PaymentMethodRef_value="$PaymentRefTitle[1]"; 
				$TransRef_value="$TransRefTitle[1]"; 
				
				//Getting Groupname from phpbb_groups table
				$sql_g="SELECT group_name FROM phpbb_groups where group_id=".$_POST['g'];

				if ( !($result_g = $db->sql_query($sql_g)))
				{
					message_die(GENERAL_ERROR, 'Could not get Groups Information', '', __LINE__, __FILE__, $sql_g);
				}

				$row_g = $db->sql_fetchrow($result_g);

				$group_name=$row_g['group_name'];
				
				$sql= "INSERT INTO phpbb_VancoInfo ( `User_ID` ,`user_name` ,`Group_ID` ,`group_name` ,`rt_donor_ref_id`, `Customer_ref`, `PaymentMethod_ref`, `Transaction_ref`, `Payment_type`, `Donation_frequency`, `Donation_date`, `User_active_status`) ". VALUES . " ( '" . $userdata['user_id'] . "', '" . $userdata['username'] . "', '" . str_replace("\'", "''",$_POST['g']) . "', '" . $group_name . "', '" . str_replace("\'", "''",$_POST['rt_donor_ref_id']) . "', '" . str_replace("\'", "''",$CustomerRef_value) . "', '" . str_replace("\'", "''",$PaymentMethodRef_value) . "', '" . str_replace("\'", "''",$TransRef_value) . "', '" . str_replace("\'", "''",$_POST['paymentMethod']) . "' , '" . str_replace("\'", "''",$_POST['donationFreq']) . "', '" . time() . "', 'active')";

				if ( !($result = $db->sql_query($sql)) )
				{
					message_die(GENERAL_ERROR, 'Could not insert the Data into the table', '', __LINE__, __FILE__, $sql);
				}
				
				$var=$_POST["account_number"];
				$replace='XXXXXXXXX';

				$mail_msg='<html>
								<table cellspacing="0" cellpadding="0" border="0" width="500px"> 
									<tr>
										<td align="center"><h3>RT Donations - Payment details</h3></td>
									</tr>
									<tr>
										<td>
											<table cellspacing="5" cellpadding="0" border="0" width="500px"> 
												<tr>
													<td align="right" style="padding-right:10px">Donor Ref ID:</td>
													<td><b>'.$_POST["rt_donor_ref_id"].'</b></td>
												</tr>
												<tr>
													<td align="right" style="padding-right:10px">Donor Last Name:</td>
													<td><b>'.$userdata["users_lname"].'</b></td>
												</tr>
												<tr>
													<td align="right" style="padding-right:10px">First Name:</td>
													<td><b>'.$userdata["users_fname"].'</b></td>
												</tr>
												<tr>
													<td colspan="2">&nbsp;</td>
												</tr>
												<tr>
													<td align="right" style="padding-right:10px">Account Type:</td>
													<td><b>'.$_POST["paymentMethod"].'</b></td>
												</tr>
												<tr>
													<td align="right" style="padding-right:10px">Rounting Number:</td>
													<td><b>'.$_POST["routing_number"].'</b></td>
												</tr>
												<tr>
													<td align="right" style="padding-right:10px">Account Number:</td>
													<td><b>'.substr_replace($var, $replace, 0, -(strlen($var)-(strlen($var)-4))).'</b></td>
												</tr>
												<tr>
													<td colspan="2">&nbsp;</td>
												</tr>
												<tr>
													<td align="right" style="padding-right:10px">Donation Frequency:</td>
													<td><b>'.$_POST["donationFreq"].'</b></td>
												</tr>
												<tr>
													<td align="right" style="padding-right:10px">Donation Start Date:</td>
													<td><b>'.$_POST["donation_start_date"].'</b></td>
												</tr>
												<tr>
													<td align="right" style="padding-right:10px">Common Fund Amount:</td>
													<td><b>$'.$_POST["common_fund"].'</b></td>
												</tr>
												<tr>
													<td align="right" style="padding-right:10px">Seed Fund Amount:</td>
													<td><b>$'.$_POST["seed_fund"].'</b></td>
												</tr>
												<tr>
													<td align="right" style="padding-right:10px">Total Fund Amount:</td>
													<td><b>'.$_POST["total_fund"].'</b></td>
												</tr>
												<tr>
													<td colspan="2">&nbsp;</td>
												</tr>
												<tr>
													<td align="right" style="padding-right:10px">Customer Reference Number:</td>
													<td><b>'.$CustomerRef_value.'</b></td>
												</tr>
												<tr>
													<td align="right" style="padding-right:10px">Payment Method Reference Number:</td>
													<td><b>'.$PaymentMethodRef_value.'</b></td>
												</tr>
												<tr>
													<td align="right" style="padding-right:10px">Transaction Reference Number:</td>
													<td><b>'.$TransRef_value.'</b></td>
												</tr>
											</table>			
										</td>
									</tr>
								</table>
							</body></html>';

				$EmailId = $userdata['user_email'];
				$mail_to = $board_config['board_email'];
				$subject="RT Donation - Payment details";				

				$headers = 'MIME-Version: 1.0     ;' . "\n";
				$headers .= 'Content-type: text/html  ; charset=iso-8859-1    ;' . "\n";
				$headers .= 'From:'.$EmailId; //girit@inprofy.net
								
				mail($mail_to, $subject, $mail_msg, $headers);
				mail($EmailId, $subject, $mail_msg, $headers);
				mail('Darin@relationaltithe.com', $subject, $mail_msg, $headers);
				//Email part ends here....

				$msg="You have successfully made your donation.";
				
				$msg.='<font size=2><br><br>If you want to make another donation, please <a href='.append_sid("Vancotest_page.$phpEx?g=".$_POST['g']).'><b>Click</b></a> here.</font>';
				
				$msg.='<font size=2><br><br>Please <a href='.append_sid("index.$phpEx").'><b>Click</b></a> here to go to RT Home page.</font>';
				
				$template->assign_vars(array('STATUS_MESSAGE' => $msg));
				
				$template->set_filenames(array('body'=>'Vancotest_confirmation.tpl'));
				include("./includes/rightcontrol.php");
				$template->pparse('body');
			}
			else 
			{
				////// Getting the current data and redirect the page to first page
				$sql="SELECT max(id) as MaxId FROM phpbb_Vanco_temp WHERE user_id=".$userdata['user_id'];

				if ( !($result = $db->sql_query($sql)) )
				{
					message_die(GENERAL_ERROR, 'Could not query user info from temp table', '', __LINE__, __FILE__, $sql);
				}
				
				$row = $db->sql_fetchrow($result);

				$maxId=$row['MaxId'];
		
				///////////
				$ErrorField=preg_match("/<ErrorDescription>(.*?)<\/ErrorDescription>/", $_return_EFT, $ErrorFieldName);
				$ErrorField_value="$ErrorFieldName[1]"; 
				
				$fname='';
				if($ErrorCode_value=='495' || $ErrorCode_value=='496')
				{
					$field_name=explode(' ', $ErrorField_value);
					$fname=$field_name[0];
				}
				else
				{
					$field_name='';
					$fname=$field_name[0];
				}
				
				//print_r($ErrorCodes_array);

				$err_msg=$ErrorCodes_array[$ErrorCode_value];
				
				/////// Vancotest_page.php redirects if any error gets
				redirect(append_sid("Vancotest_page.$phpEx?g=".$_POST['g']."&donation_Id=".$maxId."&field_name=".$fname."&error_id=".$ErrorCode_value, true));
			}
		}
	}
	else 
	{
		$template->set_filenames(array('body' => 'Vancotest_page1.tpl'));
		include("./includes/rightcontrol.php");
		$template->pparse('body');
	}
}
else
{
	redirect(append_sid("login.$phpEx?redirect=Vancotest_page.$phpEx&g=".$g_id, true));
}

include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
?>