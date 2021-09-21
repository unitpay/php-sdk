<?php

/**
 * Payment form
 *
 * @link https://help.unitpay.ru/payments/create-payment-easy
 */

require_once('./orderInfo.php');
require_once('../UnitPay.php');

$unitPay = new UnitPay($domain, $secretKey);

$redirectUrl = $unitPay->form(
    $publicId,
    $orderSum,
    $orderId,
    $orderDesc,
    $orderCurrency
);

header("Location: " . $redirectUrl);
