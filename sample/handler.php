<?php

/**
 *  Demo handler for your projects
 *
 * @link https://unitpay.ru/doc#confirmPayment
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

try {
    // Validate request (check ip address, signature and etc)
    $unitPay->checkHandlerRequest();

    list($method, $params) = array($_GET['method'], $_GET['params']);

    // Very important! Validate request with your order data, before complete order
    if (
        $params['orderSum'] != $orderSum ||
        $params['orderCurrency'] != $orderCurrency ||
        $params['account'] != $orderId ||
        $params['projectId'] != $projectId
    ) {
        // logging data and throw exception
        throw new InvalidArgumentException('Order validation Error!');
    }

    switch ($method) {
        // Just check order (check server status, check order in DB and etc)
        case 'check':
            print $unitPay->getSuccessHandlerResponse('Check Success. Ready to pay.');
            break;
        // Method Pay means that the money received
        case 'pay':
            // Please complete order
            print $unitPay->getSuccessHandlerResponse('Pay Success');
            break;
        // Method Error means that an error has occurred.
        case 'error':
            // Please log error text.
            print $unitPay->getSuccessHandlerResponse('Error logged');
            break;
        // Method Refund means that the money returned to the client
        case 'refund':
            // Please cancel the order
            print $unitPay->getSuccessHandlerResponse('Order canceled');
            break;
    }
// Oops! Something went wrong.
} catch (Exception $e) {
    print $unitPay->getErrorHandlerResponse($e->getMessage());
}
