<?php

use UnitPay\UnitPay;

/**
 * Payment info
 *
 * @link http://help.unitpay.ru/article/58-get-payment
 * @link http://help.unitpay.money/article/58-get-payment
 */

require_once('./orderInfo.php');
require_once('../vendor/autoload.php');

$unitPay = new UnitPay($domain, $secretKey);

$response = $unitPay->api('getPayment', [
    'paymentId' => 3403575,
]);

// If need user redirect on Payment Gate
if (isset($response->result)) {
    // Payment Info
    $paymentInfo = $response->result;
    var_dump($paymentInfo);
// If error during api request
} elseif (isset($response->error->message)) {
    $error = $response->error->message;
    print 'Error: '.$error;
}
