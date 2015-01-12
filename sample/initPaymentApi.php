<?php

header('Content-Type: text/html; charset=UTF-8');

/**
 * API integration
 *
 * @link https://unitpay.ru/doc#initPayment
 */

include ('../UnitPay.php');

// Project Data
$projectId  = 1;
$secretKey  = '9e977d0c0e1bc8f5cc9775a8cc8744f1';

// My item Info
$itemName = 'Iphone 6 Skin Cover';

// My Order Data
$orderId        = 'a183f94-1434-1e44';
$orderSum       = 900;
$orderDesc      = 'Payment for item "'.$itemName.'"';
$orderCurrency  = 'RUB';

$unitPay = new UnitPay($secretKey);

/**
 * Base params: account, desc, sum, currency, projectId, paymentType
 * Additional params:
 *  Qiwi, Mc:
 *      phone
 * alfaClick:
 *      clientId
 *
 * @link https://unitpay.ru/doc#initPayment
 * @link https://unitpay.ru/doc#paymentTypes
 */
$response = $unitPay->api('initPayment', [
    'account' => $orderId,
    'desc' => $orderDesc,
    'sum' => $orderSum,
    'paymentType' => 'yandex',
    'currency' => $orderCurrency,
    'projectId' => $projectId
]);

// If need user redirect on Payment Gate
if (isset($response->result->type)
    && $response->result->type == 'redirect') {
    // Url on PaymentGate
    $redirectUrl = $response->result->redirectUrl;
    // Payment ID in Unitpay (you can save it)
    $paymentId = $response->result->paymentId;
    // User redirect
    header("Location: " . $redirectUrl);

// If without redirect (invoice)
} elseif (isset($response->result->type)
    && $response->result->type == 'invoice') {
    // Url on receipt page in Unitpay
    $statusUrl = $response->result->statusUrl;
    // Payment ID in Unitpay (you can save it)
    $paymentId = $response->result->paymentId;
    // Invoice Id in Payment Gate (you can save it)
    $invoiceId = $response->result->invoiceId;
    // User redirect
    header("Location: " . $statusUrl);

// If error during api request
} elseif (isset($response->error->message)) {
    $error = $response->error->message;
    print 'Error: '.$error;
}
