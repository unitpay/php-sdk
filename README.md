# UnitPay PHP-SDK
Php sdk for [UnitPay](https://unitpay.ru) 
 
Documentation https://unitpay.ru/doc

## Installation

The preferred way to install this extension is through [composer](https://getcomposer.org/download/).

Either run

```bash
$ php composer.phar require unitpay/php-sdk
```

or add

```
"unitpay/php-sdk": "^1.0"
```

to the ```require``` section of your `composer.json` file.


## Examples

These are just some quick examples. Check out the samples in [`/examples`](https://github.com/unitpay/php-sdk/blob/master/examples).

### Payment integration using UnitPay Form
```php
<?php
require_once('vendor/autoload.php');

// Project Data
$secretKey  = '9e977d0c0e1bc8f5cc9775a8cc8744f1';
$publicId   = '15155-ae12d';

// My item Info
$itemName = 'Iphone 6 Skin Cover';

// My Order Data
$orderId        = 'a183f94-1434-1e44';
$orderSum       = 900;
$orderDesc      = 'Payment for item "' . $itemName . '"';
$orderCurrency  = 'RUB';

$unitPay = new unitpay\UnitPay($secretKey);

$unitPay
    ->setBackUrl('http://domain.com')
    ->setCustomerEmail('customer@domain.com')
    ->setCustomerPhone('79001235555')
    ->setCashItems(array(
       new unitpay\CashItem($itemName, 1, $orderSum) 
    ));

$redirectUrl = $unitPay->form(
    $publicId,
    $orderSum,
    $orderId,
    $orderDesc,
    $orderCurrency
);

header("Location: " . $redirectUrl);
```

### Payment integration using UnitPay Api

```php
<?php

header('Content-Type: text/html; charset=UTF-8');

/**
 * API integration
 *
 * @link https://unitpay.ru/doc#initPayment
 */

require_once('vendor/autoload.php');

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

$unitPay = new unitpay\UnitPay($secretKey);

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
    'account'     => $orderId,
    'desc'        => $orderDesc,
    'sum'         => $orderSum,
    'paymentType' => 'yandex',
    'currency'    => $orderCurrency,
    'projectId'   => $projectId
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
    $receiptUrl = $response->result->receiptUrl;
    // Payment ID in Unitpay (you can save it)
    $paymentId = $response->result->paymentId;
    // Invoice Id in Payment Gate (you can save it)
    $invoiceId = $response->result->invoiceId;
    // User redirect
    header("Location: " . $receiptUrl);

// If error during api request
} elseif (isset($response->error->message)) {
    $error = $response->error->message;
    print 'Error: '.$error;
}
```

### Handler sample

```php
<?php

/**
 *  Demo handler for your projects
 *
 * @link https://unitpay.ru/doc#confirmPayment
 */
include ('vendor/autoload.php');

// Project Data
$projectId  = 1;
$secretKey  = '9e977d0c0e1bc8f5cc9775a8cc8744f1';

// My item Info
$itemName = 'Iphone 6 Skin Cover';

// My Order Data
$orderId        = 'a183f94-1434-1e44';
$orderSum       = 900;
$orderDesc      = 'Payment for item "' . $itemName . '"';
$orderCurrency  = 'RUB';

$unitPay = new unitpay\UnitPay($secretKey);

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
            echo $unitPay->getSuccessHandlerResponse('Check Success. Ready to pay.');
            break;
        // Method Pay means that the money received
        case 'pay':
            // Please complete order
            echo $unitPay->getSuccessHandlerResponse('Pay Success');
            break;
        // Method Error means that an error has occurred.
        case 'error':
            // Please log error text.
            echo $unitPay->getSuccessHandlerResponse('Error logged');
            break;
    }
// Oops! Something went wrong.
} catch (Exception $e) {
    echo $unitPay->getErrorHandlerResponse($e->getMessage());
}
```

## Contributing

Please feel free to contribute to this project! Pull requests and feature requests welcome!
