<?php

/**
 * Payment form
 *
 * @link https://unitpay.ru/doc#payForm
 */

include ('../UnitPay.php');

// Project Data
$secretKey  = '9e977d0c0e1bc8f5cc9775a8cc8744f1';
$publicId   = '15155-ae12d';

// My item Info
$itemName = 'Iphone 6 Skin Cover';

// My Order Data
$orderId        = 'a183f94-1434-1e44';
$orderSum       = 900;
$orderDesc      = 'Payment for item "'.$itemName.'"';
$orderCurrency  = 'RUB';

$unitPay = new UnitPay($secretKey);

$redirectUrl = $unitPay->form(
    $publicId,
    $orderSum,
    $orderId,
    $orderDesc,
    $orderCurrency
);

header("Location: " . $redirectUrl);
