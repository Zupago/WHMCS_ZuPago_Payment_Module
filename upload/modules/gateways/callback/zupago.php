<?php
include("../../../init.php");
include("../../../includes/functions.php");
include("../../../includes/gatewayfunctions.php");
include("../../../includes/invoicefunctions.php");

$gatewaymodule = "zupago";
$GATEWAY = getGatewayVariables($gatewaymodule);
if (!$GATEWAY["type"]) die("Module Not Activated"); 

function processPOST($apikey, $amount, $zupayee, $zupayee_btc, $cur){

		$_POST['whmcs_apikey_compare']='CAME: '.$_POST['ZUPAYEE_ACC_KEY'].'; WE HAVE: '.$apikey;
		$_POST['whmcs_amount_compare']='CAME: '.$_POST['PAYMENT_AMOUNT'].'; WE HAVE: '.$amount;
		$_POST['whmcs_zupayee_compare']='CAME: '.$_POST['ZUPAYEE_ACC'].'; WE HAVE: '.$uzpayee;
		$_POST['whmcs_zupayee_btc_compare']='CAME: '.$_POST['ZUPAYEE_ACC_BTC'].'; WE HAVE: '.$zupayee_btc;
		$_POST['whmcs_currency_compare']='CAME: '.$_POST['CURRENCY_TYPE'].'; WE HAVE: '.$cur;

}
$apikey=$GATEWAY["ZUPAYEE_ACC_KEY"];

if($apikey=$_POST['ZUPAYEE_ACC_KEY']){ // proccessing payment if only apikey is valid

	$invoiceid = (int)$_POST["PAYMENT_REF"];
	$invoiceid = checkCbInvoiceID($invoiceid,$GATEWAY["name"]); # Checks invoice ID is a valid invoice number or ends processing
	checkCbTransID($_POST['tokan']); # Checks transaction number isn't already in the database and ends processing if it does

	
	$qry=mysql_query("SELECT tblinvoices.total AS total, tblcurrencies.code AS currency_code, tblcurrencies.id AS currency_id FROM tblinvoices, tblclients, tblcurrencies WHERE tblinvoices.paymentmethod='$gatewaymodule' AND tblinvoices.id=$invoiceid AND tblinvoices.userid=tblclients.id AND tblclients.currency=tblcurrencies.id");

	if(!$qry){ logTransaction($GATEWAY["name"], array_merge($_POST, array('SQL query'=>$qry)), "SQL query error"); die(); }
	if(mysql_num_rows($qry)!=1){ logTransaction($GATEWAY["name"], $_POST, "SQL returned invalid data"); die(); }
	$data=mysql_fetch_array($qry);
print_r(	$data);

	$order_amount=$data['total'];

	if(!empty($GATEWAY['convertto'])) if($data['currency_id']!=$GATEWAY['convertto']){	// need to convert to another currency
		$data['total'] = convertCurrency($data['total'],$data['currency_id'],$GATEWAY['convertto']);
		$_POST['PAYMENT_AMOUNT']=$_POST['PAYMENT_AMOUNT'];
		$qry0=mysql_query("SELECT code FROM tblcurrencies WHERE id=".$GATEWAY['convertto']);
		$data0=mysql_fetch_array($qry0);
		$data['currency_code']=$data0['code'];
	}
	

	if($_POST['PAYMENT_AMOUNT']==$data['total'] && $_POST['ZUPAYEE_ACC']==$GATEWAY['zupago_id'] && $_POST['ZUPAYEE_ACC_BTC']==$GATEWAY['zupago_id_btc'] && $_POST['CURRENCY_TYPE']==$data['currency_code']){

		addInvoicePayment($invoiceid,$_POST['tokan'],$order_amount,$fee,$gatewaymodule); # Apply Payment to Invoice: invoiceid, transactionid, amount paid, fees, modulename
		processPOST($apikey, $data['total'], $GATEWAY['zupago_id'], $GATEWAY['zupago_id_btc'], $data['currency_code']);
		logTransaction($GATEWAY["name"],$_POST,"Successful"); # Save to Gateway Log: name, data array, status

   }else{ // you can also save invalid payments for debug purposes

		 processPOST($apikey, $data['total'], $GATEWAY['zupago_id'], $GATEWAY['zupago_id_btc'], $data['currency_code']);
     logTransaction($GATEWAY["name"],$_POST,"Fake Data");

   }

}else{
		
	processPOST($apikey, 'not defined', $GATEWAY['zupago_id'], $GATEWAY['zupago_id_btc'], $data['currency_code']);
	logTransaction($GATEWAY["name"],$_POST,"Unsuccessful"); # Save to Gateway Log: name, data array, status

}
?>