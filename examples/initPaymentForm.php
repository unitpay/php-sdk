<?php

/**
 * Payment form
 *
 * @link http://help.unitpay.ru/article/31-creating-payment-from-payment-form
 */

require_once('./orderInfo.php');
require_once('../vendor/autoload.php');

$unitPay = new unitpay\UnitPay($secretKey);

$redirectUrl = $unitPay->form(
    $publicId,
    $orderSum,
    $orderId,
    $orderDesc,
    $orderCurrency
);

header("Location: " . $redirectUrl);
