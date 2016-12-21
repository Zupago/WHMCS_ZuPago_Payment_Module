<?php

function zupago_config(){

    $configarray = array(
     "FriendlyName" => array("Type" => "System", "Value"=>"ZuPago HyBrid (HD) Wallet"),
		 "zupago_id" => array("FriendlyName" => "ZuPago Account", "Type" => "text", "Size" => "20", "Description" => "example: ZU-123456"),
		 "zupago_id_btc" => array("FriendlyName" => "ZuPago Bitcoin Account", "Type" => "text", "Size" => "20", "Description" => "example: ZB-123456"),
		 "zupago_pass" => array("FriendlyName" => "API Key", "Type" => "text", "Size" => "20", "Description" => "Renerate & Enable your APi Key in your ZP account"),
    );
		return $configarray;

}

function zupago_link($params) {

	global $_LANG;

	# Gateway Specific Variables
	$zupago_id = $params['zupago_id'];
	$zupago_id_btc = $params['zupago_id_btc'];
	$zupago_pass = $params['zupago_pass'];
	# Invoice Variables
	$invoiceid = $params['invoiceid'];
	$description = $params["description"];
  $amount = $params['amount']; # Format: ##.##
  $currency = $params['currency']; # Currency Code
 
	# System Variables
	$companyname = $params['companyname'];
	$systemurl = $params['systemurl'];
	$currency = $params['currency'];

	# Enter your code submit to the gateway...

	$code = '<form action="https://zupago.pe/api" method="post">
<input type="hidden" name="SUGGESTED_MEMO" value="'.$description.'">

<input type="hidden" name="PAYMENT_REF" value="'.$invoiceid.'" />
<input type="hidden" name="PAYMENT_AMOUNT" value="'.$amount.'" />
<input type="hidden" name="ZUPAYEE_ACC" value="'.$zupago_id.'" />
<input type="hidden" name="ZUPAYEE_ACC_BTC" value="'.$zupago_id_btc.'" />
<input type="hidden" name="ZUPAYEE_ACC_KEY" value="'.$zupago_pass.'" />
<input type="hidden" name="CURRENCY_TYPE" value="'.$currency.'" />
<input type="hidden" name="ZUPAYEE_NAME" value="'.$companyname.'" />
<input type="hidden" name="SUCCESS_URL" value="'.$systemurl.'/viewinvoice.php?id='.$invoiceid.'" />
<input type="hidden" name="SUCCESS_URL_METHOD" value="LINK" />
<input type="hidden" name="CANCEL_URL" value="'.$systemurl.'/viewinvoice.php?id='.$invoiceid.'" />
<input type="hidden" name="CANCEL_URL_METHOD" value="LINK" />
<input type="hidden" name="STATUS_URL" value="'.$systemurl.'/modules/gateways/callback/zupago.php" />
<input type="submit" value="'.$_LANG['invoicespaynow'].'" />
</form>';

	return $code;

}
?>