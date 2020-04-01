<?php
$key = '';

$parameters = array();
$parameters['clientid'] = '';
$parameters['iframeid'] = '';
$vanco = new VancoURLcredentials( $key );
$url = $vanco->getCredentials( $parameters );
//print( $url );

class VancoURLcredentials {
const COMPRESSION_LEVEL = 6;
const VANCO_PAD = "A";
const AES_PAD = " ";
const OPENSSL = 1;
const PHPSECLIB = 2;
const PHPSECLIB_DIR = "./PHPSecLib/";
const LIBRARY = self::OPENSSL;

protected $key = "";
protected $library;

public function __construct( $key, $library = self::LIBRARY ) {
$this->key = $key;
$this->library = $library;
}

public function getCredentials( $parameters ) {
$output = $this->json( $parameters );
$output = $this->append( $output );
$output = $this->compress( $output );
$output = $this->pad( $output );
$output = $this->encrypt( $output );
$output = $this->url_safe( $output );
return $output;
}

protected function json( $input ) {
if ( !is_array( $input ) ) {
$input = array( $input );
}
$json = json_encode( $input );
return str_replace( "\"", "'", $json );
}

protected function append( $input ) {
return $input . self::VANCO_PAD;
}

protected function compress( $input ) {
return gzcompress( $input, self::COMPRESSION_LEVEL );
}

protected function pad( $input ) {
$round_up_length = strlen( $input );
$round_up_length = 16*ceil( $round_up_length/16 );
return str_pad( $input, $round_up_length, self::AES_PAD );
}

protected function encrypt( $input ) {
switch ( $this->library ) {
case ( self::PHPSECLIB ) :
include_once( self::PHPSECLIB_DIR . '/Crypt/AES.php');
$aes = new Crypt_AES( CRYPT_AES_MODE_ECB );
$aes->setKey( $this->key );
$aes->disablePadding();
$return = $aes->encrypt( $input );
$return = $this->base64( $return );
break;
case ( self::OPENSSL ) :
$hexkey = bin2hex( $this->key );
$data = unpack( "C*", $input );
$hex_data = "";
foreach ($data as $byte) {
$hex_data .= sprintf( "\\x%02x", $byte );
}
$command = "echo -ne '$hex_data' "
. "| openssl enc "
. "-aes-256-ecb "
. "-K {$hexkey} "
. "-iv 0 "
. "-nopad "
. "-base64 -A"
;
$return = array();
exec( $command, $return );
$return = implode( $return, "\n" );
break;
}
return $return;
}

protected function base64( $input ) {

return base64_encode( $input );

}

protected function url_safe( $input ) {
return str_replace(array('+','/'),array('-','_',''),$input);
}

}
?>