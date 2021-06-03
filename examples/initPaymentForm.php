<?php

/**
 * Payment form
 *
 * @link https://help.unitpay.ru/article/31-creating-payment-from-payment-form
 * @link https://help.unitpay.money/article/31-creating-payment-from-payment-form
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
