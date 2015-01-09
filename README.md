# UnitPay PHP-SDK
Php sdk for [UnitPay ](https://unitpay.ru) 
 
Documentation https://unitpay.ru/doc

## Examples ##
These are just some quick examples. Check out the samples in [`/sample`](https://github.com/unitpay/php-sdk/blob/master/sample).

Payment through the UnitPay form
```php
<?php
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
?>
```

- Payment through the UnitPay API [`/sample/initPaymentApi.php`](https://github.com/unitpay/php-sdk/blob/master/sample/initPaymentApi.php)
- Handler sample [`/sample/handler.php`](https://github.com/unitpay/php-sdk/blob/master/sample/handler.php)