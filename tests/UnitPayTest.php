<?php

use PHPUnit\Framework\TestCase;
use UnitPay\Exceptions\UnsupportedDomain;
use UnitPay\Exceptions\WrongIpAddress;
use UnitPay\UnitPay;

//use UnitPay\Tests\TestCase;

/**
 * Class TestUnitPay
 *
 * @package Unit
 *
 * @author  Alexander Gorenkov <g.a.androidjc2@ya.ru> <Tg:@alex_brin>
 * @version 1.0.0
 * @since   3.0.0
 */
class UnitPayTest extends TestCase
{
    private const PRIVATE_KEY = 'private';
    private const PUBLIC_KEY = 'public';

    public function testSuccessCreateInstance(): void
    {
        new UnitPay(self::PUBLIC_KEY, self::PRIVATE_KEY, UnitPay::DOMAIN_MONEY);
        self::assertTrue(true);
    }

    public function testFailCreateInstance(): void
    {
        $this->expectException(UnsupportedDomain::class);
        new UnitPay(self::PUBLIC_KEY, self::PRIVATE_KEY, null, "unsupported");
    }

    public function testSuccessGetSignatureEquals(): void
    {
        $sdk = new UnitPay(self::PUBLIC_KEY, self::PRIVATE_KEY);

        $vitalParams = [];

        $signature  = $sdk->getSignature($vitalParams);
        $signature2 = hash('sha256', implode('{up}', [
            self::PRIVATE_KEY,
        ]));
        self::assertEquals($signature2, $signature);

        $signature  = $sdk->getSignature($vitalParams, 'pay');
        $signature2 = hash('sha256', implode('{up}', [
            'pay',
            self::PRIVATE_KEY,
        ]));
        self::assertEquals($signature2, $signature);
    }

    public function testSuccessFormEqualsWithSecret(): void
    {
        $sdk = new UnitPay(self::PUBLIC_KEY, self::PRIVATE_KEY);

        $url = $sdk->form(100.0, "custom-id", "description");

        $vitalParams = [
            'account'  => 'custom-id',
            'currency' => 'RUB',
            'desc'     => 'description',
            'sum'      => 100,
        ];

        $vitalParams['signature'] = $sdk->getSignature($vitalParams);
        $vitalParams['locale']    = 'ru';

        self::assertEquals(
            sprintf("https://unitpay.money/pay/public?%s", http_build_query($vitalParams)),
            $url
        );
    }

    public function testSuccessFormEqualsWithoutSecret(): void
    {
        $account     = 'custom-id';
        $desc        = 'description';
        $sum         = 100;
        $vitalParams = [
            'account'  => $account,
            'currency' => 'RUB',
            'desc'     => $desc,
            'sum'      => $sum,
            'locale'   => 'ru',
        ];

        $url = sprintf("https://unitpay.money/pay/public?".http_build_query($vitalParams));

        self::assertEquals($url, (new UnitPay(self::PUBLIC_KEY, ""))->form($sum, $account, $desc));
        self::assertEquals($url, (new UnitPay(self::PUBLIC_KEY, "0"))->form($sum, $account, $desc));
        self::assertEquals($url, (new UnitPay(self::PUBLIC_KEY, null))->form($sum, $account, $desc));
    }

    public function testRealIPGetter(): void
    {
        $sdk                    = new UnitPay(self::PUBLIC_KEY, self::PRIVATE_KEY);
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';


        self::assertEquals('127.0.0.1', $sdk->getRealIP());

        $_SERVER['HTTP_X_FORWARDED_FOR'] = '127.0.0.2';
        self::assertEquals('127.0.0.2', $sdk->getRealIP());
    }

    public function testHandlerFailEmptyMethod(): void
    {
        $sdk = new UnitPay(self::PUBLIC_KEY, self::PRIVATE_KEY);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Empty method");
        /** @noinspection PhpUnhandledExceptionInspection */
        $sdk->handleRequest();
    }

    public function testHandlerFailEmptyParams(): void
    {
        $sdk = new UnitPay(self::PUBLIC_KEY, self::PRIVATE_KEY);

        $_GET['method'] = 'pay';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Empty params");
        /** @noinspection PhpUnhandledExceptionInspection */
        $sdk->handleRequest();
    }

    public function testHandlerFailUnsupportedMethod(): void
    {
        $sdk = new UnitPay(self::PUBLIC_KEY, self::PRIVATE_KEY);

        $_GET['method'] = 'unsupported';
        $_GET['params'] = [
            'sum' => 100,
        ];

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage("Method is not supported");
        /** @noinspection PhpUnhandledExceptionInspection */
        $sdk->handleRequest();
    }

    public function testHandlerFailWrongSignature(): void
    {
        $sdk = new UnitPay(self::PUBLIC_KEY, self::PRIVATE_KEY);

        $_GET['method'] = 'pay';
        $_GET['params'] = [
            'sum' => 100,
        ];

        $_GET['params']['signature'] = "wrong";
//        $_GET['params']['signature'] = $sdk->getSignature($_GET['params'], $_GET['method']);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Wrong signature");
        /** @noinspection PhpUnhandledExceptionInspection */
        $sdk->handleRequest();
    }

    public function testHandlerFailWrongIpAddress(): void
    {
        $sdk = new UnitPay(self::PUBLIC_KEY, self::PRIVATE_KEY);

        $_SERVER['REMOTE_ADDR'] = '127.0.0.2';

        $_GET['method'] = 'pay';
        $_GET['params'] = [
            'sum' => 100,
        ];

        $_GET['params']['signature'] = $sdk->getSignature($_GET['params'], $_GET['method']);

        $this->expectException(WrongIpAddress::class);
        $this->expectExceptionMessage("IP address Error");
        $sdk->handleRequest();
    }

    public function testHandlerSuccess(): void
    {
        $sdk = new UnitPay(self::PUBLIC_KEY, self::PRIVATE_KEY);

        $_SERVER['HTTP_X_FORWARDED_FOR'] = '127.0.0.1';
        $_SERVER['REMOTE_ADDR']          = '127.0.0.1';

        $_GET['method'] = 'pay';
        $_GET['params'] = [
            'sum' => 100,
        ];

        $_GET['params']['signature'] = $sdk->getSignature($_GET['params'], $_GET['method']);

        /** @noinspection PhpUnhandledExceptionInspection */
        self::assertTrue($sdk->handleRequest());
    }
}