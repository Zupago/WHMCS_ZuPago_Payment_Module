<?php

function zupago_config() {

    $configarray = array(
        "FriendlyName" => array("Type" => "System", "Value" => "ZuPago HyBrid (HD) Wallet"),
        "zupago_id" => array("FriendlyName" => "ZuPago Account", "Type" => "text", "Size" => "20", "Description" => "example: ZU-123456"),
        "zupago_id_btc" => array("FriendlyName" => "ZuPago Bitcoin Account", "Type" => "text", "Size" => "20", "Description" => "example: ZB-123456"),
        "zupago_id_bcc" => array("FriendlyName" => "ZuPago Bitcoin-Cash Account", "Type" => "text", "Size" => "20", "Description" => "example: ZBC-123456"),
        "zupago_pass" => array("FriendlyName" => "API Key", "Type" => "text", "Size" => "20", "Description" => "Renerate & Enable your APi Key in your ZP account"),
        'testMode' => array(
            'FriendlyName' => 'Test Mode',
            'Type' => 'yesno',
            'Description' => 'Tick to enable test mode',
        ),
    );
    return $configarray;
}

function zupago_link($params) {

    global $_LANG;

    # Gateway Specific Variables
    $zupago_id = $params['zupago_id'];
    $zupago_id_btc = $params['zupago_id_btc'];
    $zupago_id_bcc = $params['zupago_id_bcc'];
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
    if ($_SERVER['REQUEST_SCHEME'] == 'https') {
        $http = explode('://', $systemurl);
        if ($http[0] == 'http') {
            $systemurl = 'https://' . $http[1];
        }
    } else if ($_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
        $http = explode('://', $systemurl);
        if ($http[0] == 'http') {
            $systemurl = 'https://' . $http[1];
        }
    } else if ($_SERVER['HTTPS'] == 'https') {
        $http = explode('://', $systemurl);
        if ($http[0] == 'http') {
            $systemurl = 'https://' . $http[1];
        }
    }
    if ($params['testMode'] == 'on') {
        $url = 'https://testnet.zupago.pe/api';
    } else {
        $url = 'https://zupago.pe/api';
    }



    $code = '<form action="' . $url . '" method="post">
<input type="hidden" name="SUGGESTED_MEMO" value="' . $description . '">

<input type="hidden" name="PAYMENT_REF" value="' . $invoiceid . '" />
<input type="hidden" name="PAYMENT_AMOUNT" value="' . $amount . '" />
<input type="hidden" name="ZUPAYEE_ACC" value="' . $zupago_id . '" />
<input type="hidden" name="ZUPAYEE_ACC_BTC" value="' . $zupago_id_btc . '" />
<input type="hidden" name="ZUPAYEE_ACC_BCC" value="' . $zupago_id_bcc . '" />
<input type="hidden" name="ZUPAYEE_ACC_KEY" value="' . $zupago_pass . '" />
<input type="hidden" name="CURRENCY_TYPE" value="' . $currency . '" />
<input type="hidden" name="ZUPAYEE_NAME" value="' . $companyname . '" />
<input type="hidden" name="SUCCESS_URL" value="' . $systemurl . '/modules/gateways/callback/zupago.php?page=success" />
<input type="hidden" name="CANCEL_URL" value="' . $systemurl . '/modules/gateways/callback/zupago.php?page=cancel" />
<input type="submit" value="' . $_LANG['invoicespaynow'] . '" />
</form>';

    return $code;
}

?>
