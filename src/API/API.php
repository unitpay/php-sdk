<?php /** @noinspection PhpUnused */

namespace UnitPay\API;

use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Class API
 *
 * @package UnitPay
 *
 * @author  Alexander Gorenkov <g.a.androidjc2@ya.ru> <Tg:@alex_brin>
 * @version 1.0.0
 * @since   3.0.0
 */
class API
{
    /**
     * @var API
     */
    private static $instance;

    /**
     * @var string
     */
    private $apiUrl;

    /**
     * @var string
     */
    private $secretKey;

    /**
     * @var string|null
     */
    private $personalSecret;

    private static $supportedMethod = [
        'initPayment', 'getPayment', 'refundPayment',
        'listSubscriptions', 'getSubscription', 'closeSubscription',
        'massPayment', 'massPaymentStatus', 'getBinInfo',
        'getPartner', 'getCommissions',
    ];

    private $requiredUnitpayMethodsParams = [
        'initPayment'        => ['account', 'sum', 'desc', 'paymentType', 'projectId'],
        'getPayment'         => ['paymentId'],
        'refundPayment'      => ['paymentId'],
        'massPayment'        => ['login', 'purse', 'transactionId', 'sum', 'paymentType'],
        'massPaymentStatus'  => ['login', 'transactionId'],
        'getBinInfo'         => ['login', 'bin'],
        'listSubscriptions ' => ['projectId'],
        'getSubscription'    => ['subscriptionId'],
        'closeSubscription'  => ['subscriptionId'],
        'getPartner'         => ['login'],
        'getCommissions'     => ['projectId', 'login'],
    ];

    public function __construct(string $apiUrl, string $secretKey, ?string $personalSecret = null)
    {

        $this->apiUrl         = $apiUrl;
        $this->secretKey      = $secretKey;
        $this->personalSecret = $personalSecret;

        self::$instance = $this;
    }

    /**
     * @param  string  $newSupportedMethod
     *
     * @return $this
     * @since 3.0.0
     *
     */
    public function addSupportedMethod(string $newSupportedMethod): self
    {
        self::$supportedMethod[] = $newSupportedMethod;

        return $this;
    }

    /**
     * Call API
     *
     * @param  string  $method
     * @param  array  $params
     *
     * @return object
     *
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     *
     * @see https://help.unitpay.money/category/180-api
     * @see https://help.unitpay.money/category/181-api
     * @see https://help.unitpay.money/category/182-api
     * @see https://help.unitpay.money/category/183-api
     *
     * @since 1.0.0
     */
    public function request(string $method, $params = [])
    {
        if (!in_array($method, self::$supportedMethod, true)) {
            throw new UnexpectedValueException('Method is not supported');
        }

        if (isset($this->requiredUnitpayMethodsParams[$method])) {
            foreach ($this->requiredUnitpayMethodsParams[$method] as $rParam) {
                if (!isset($params[$rParam])) {
                    throw new InvalidArgumentException(sprintf('Param `%s` is empty', $rParam));
                }
            }
        }

        if (!isset($params['secretKey'])) {
            $params['secretKey'] = $this->secretKey;
        }

        if (empty($params['secretKey'])) {
            throw new InvalidArgumentException('SecretKey is null');
        }

        $requestUrl = sprintf("%s?%s", $this->apiUrl, http_build_query([
                'method' => $method,
                'params' => $params,
            ], "", '&', PHP_QUERY_RFC3986)
        );

        $response = json_decode(file_get_contents($requestUrl), false);
        if (!is_object($response)) {
            throw new InvalidArgumentException('Temporary server error. Please try again later.');
        }

        return $response;
    }

    /**
     * @return string
     */
    public function getSecretKey(): string
    {
        return $this->secretKey;
    }

    /**
     * @return string|null
     */
    public function getPersonalSecret(): ?string
    {
        return $this->personalSecret;
    }

    public static function getInstance(): API
    {
        return self::$instance;
    }
}