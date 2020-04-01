<?php
/**
* @package Mail
*/

/**
* SendMail PHP Class
*
* Provides basic sendmail functionality for HTML forms and web applications.
*
* Methods:
*
* 1. open
* 2. close
* 3. query
* 4. count_rows
* 5. free_result
*
* Properties:
*
* 1. mSubject //mail subject
* 2. mSender //from - sender name
* 3. mRecipient //to
* 4. mHeaders //header information
* 5. mBody //mail text
* 6. mRedirect //URL for redirect after message is sent
* 7. mBCC //Blind carbon-copy recipient
* 8. mCC //Carbon-copy recipient
* 9. mAttachments //Any file attachments
* 10.mReplyTo //Reply-to address
*
* @author Isaac N. Hopper
* @copyright (c) 2011 - Isaac N. Hopper
* @version 1.0
* @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License v3
*/

class sendmail 
{
	//Variables
	public $mSubject, $mSender, $mReplyTo, $mCC, $mBCC, $mRecipient, $mBody, $mRedirect, $mAttachments, $err_msg;
	public $mHeaders;

	//Cunstructor
	public function sendmail($mSubject, $mSender, $mReplyTo, $mRecipient, $mCC, $mBCC, $mBody, $mRedirect) {
		//Assign properties to the object
		$this->mSubject = trim(strip_tags($mSubject));
		$this->mSender = trim(strip_tags($mSender));
		$this->mReplyTo = trim(strip_tags($mReplyTo));
		$this->mRecipient = trim(strip_tags($mRecipient));
		$this->mBody = trim(strip_tags($mBody));
			//Wrap the text for visual appeal
			$this->mBody = stripslashes(wordwrap($this-> mBody, 70));
		$this->mRedirect = trim(strip_tags($mRedirect));
		$this->mCC = trim(strip_tags($mCC));
		$this->mBCC = trim(strip_tags($mBCC));

		//Generate header content
		$this->mHeaders = 	"From: $this->mSender\r\n" . 
							"CC: $this->mCC\r\n" . 
							"BCC: $this->mBCC\r\n" . 
							"Return-Path:$this->mReplyTo\r\n";
		$this->mHeaders .= 	"Content-type: text/plain\r\n" .
							"Reply-To: $this->mReplyTo\r\n" .
    						"X-Mailer: PHP/" . phpversion();
		//Send the email
		$result = mail($this->mRecipient, $this->mSubject, $this->mBody, $this->mHeaders);

		//Redirect after send

		//return the results.
		return $result;
	}


}

?>