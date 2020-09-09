<?php /** @noinspection PhpUnhandledExceptionInspection */

use PHPUnit\Framework\TestCase;
use UnitPay\UnitPay;


/**
 * Class APITest
 *
 * @author  Alexander Gorenkov <g.a.androidjc2@ya.ru> <Tg:@alex_brin>
 * @version 1.0.0
 * @since   3.0.0
 */
class UnitPayAPITest extends TestCase
{
    public function testApiUnsupportedMethod(): void
    {
        $sdk = new UnitPay($this->getPublicKey(), $this->getSecretKey());
        $this->expectException(UnexpectedValueException::class);
        $sdk->getAPI()->request('unsupported');
    }

    public function testApiSuccess(): void
    {
        $sdk = new UnitPay($this->getPublicKey(), $this->getSecretKey());

        $sdk->getAPI()->addSupportedMethod('getPartner');
        $response = $sdk->getAPI()->request('getPartner', [
            'login'     => $this->getPartnerLogin(),
            'secretKey' => $this->getPersonalKey(),
        ]);

        self::assertIsObject($response->result);
        self::assertIsString($response->result->email);
        self::assertEquals(
            $this->getPartnerLogin(),
            $response->result->email
        );
    }

    private function getPublicKey(): string
    {
        return $_ENV['PUBLIC_KEY'] ?? $_SERVER['PUBLIC_KEY'] ?? "";
    }

    private function getSecretKey(): string
    {
        return $_ENV['SECRET_KEY'] ?? $_SERVER['SECRET_KEY'] ?? "";
    }

    public function getPersonalKey(): string
    {
        return $_ENV['PERSONAL_KEY'] ?? $_SERVER['PERSONAL_KEY'] ?? "";
    }

    public function getPartnerLogin(): string
    {
        return $_ENV['PARTNER_LOGIN'] ?? $_SERVER['PARTNER_LOGIN'] ?? "";
    }
}