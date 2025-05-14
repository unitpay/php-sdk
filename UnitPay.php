<?php

/**
 * Value object for paid goods
 */
final class CashItem
{
    const NDS_NONE = 'none';
    const NDS_0 = 'vat0';
    const NDS_10 = 'vat10';
    const NDS_20 = 'vat20';

    /** Товар */
    const PAYMENT_OBJECT_COMMODITY = 'commodity';
    /** Подакцизный товар */
    const PAYMENT_OBJECT_EXCISE = 'excise';
    /** Работа */
    const PAYMENT_OBJECT_JOB = 'job';
    /** Услуга */
    const PAYMENT_OBJECT_SERVICE = 'service';
    /** Ставка */
    const PAYMENT_OBJECT_GAMBLING_BET = 'gambling_bet';
    /** Выигрыш */
    const PAYMENT_OBJECT_GAMBLING_PRIZE = 'gambling_prize';
    /** Лотерейный билет */
    const PAYMENT_OBJECT_LOTTERY = 'lottery';
    /** Выигрыш лотереи */
    const PAYMENT_OBJECT_LOTTERY_PRIZE = 'lottery_prize';
    /** Результаты интеллектуальной деятельности */
    const PAYMENT_OBJECT_INTELLECTUAL_ACTIVITY = 'intellectual_activity';
    /** Платёж */
    const PAYMENT_OBJECT_PAYMENT = 'payment';
    /** Агентское вознаграждение */
    const PAYMENT_OBJECT_AGENT_COMMISSION = 'agent_commission';
    /** Составной предмет расчёта */
    const PAYMENT_OBJECT_COMPOSITE = 'composite';
    /** Иной предмет расчёта */
    const PAYMENT_OBJECT_ANOTHER = 'another';
    /**  */
    const PAYMENT_OBJECT_PROPERTY_RIGHT = 'property_right';
    /**  */
    const PAYMENT_OBJECT_NON_OPERATING_GAIN = 'non-operating_gain';
    /**  */
    const PAYMENT_OBJECT_INSURANCE_PREMIUM = 'insurance_premium';
    /** Налог с продажи */
    const PAYMENT_OBJECT_SALES_TAX = 'sales_tax';
    /** Курортный сбор */
    const PAYMENT_OBJECT_RESORT_FEE = 'resort_fee';

    /** 100% предоплата */
    const PAYMENT_METHOD_PREPAYMENT_FULL = 'full_prepayment';
    /** Частичная предоплата */
    const PAYMENT_METHOD_PREPAYMENT = 'prepayment';
    /** Аванс */
    const PAYMENT_METHOD_ADVANCE = 'advance';
    /** Полный расчёт */
    const PAYMENT_METHOD_PAYMENT_FULL = 'full_payment';

    private $name;
    private $count;
    private $price;
    private $nds;
    private $type;
    private $paymentMethod;

    /**
     * @param string $name
     * @param int $count
     * @param float $price
     * @param string $nds
     * @param string $type
     * @param string $paymentMethod
     */
    public function __construct(
        $name,
        $count,
        $price,
        $nds = self::NDS_NONE,
        $type = self::PAYMENT_OBJECT_COMMODITY,
        $paymentMethod = self::PAYMENT_METHOD_PREPAYMENT_FULL
    )
    {
        $this->name = $name;
        $this->count = $count;
        $this->price = $price;
        $this->nds = $nds;
        $this->type = $type;
        $this->paymentMethod = $paymentMethod;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return string
     */
    public function getNds()
    {
        return $this->nds;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }
}

/**
 * Payment method Unitpay process
 */
class UnitPay
{
    private $supportedUnitpayMethods = ['initPayment', 'getPayment'];
    private $requiredUnitpayMethodsParams = [
        'initPayment' => ['desc', 'account', 'sum'],
        'getPayment'  => ['paymentId'],
    ];
    private $supportedPartnerMethods = ['check', 'pay', 'error'];
    private $supportedUnitpayIp = [
        '31.186.100.49',
        '51.250.20.9',
        '52.29.152.23',
        '52.19.56.234',
        '127.0.0.1' // for debug
    ];

    private $secretKey;
    private $params = [];
    private $apiUrl;
    private $formUrl;

    public function __construct($domain, $secretKey = null)
    {
        $this->secretKey = $secretKey;
        $this->apiUrl = "https://$domain/api";
        $this->formUrl = "https://$domain/pay/";
    }

    /**
     * Create SHA-256 digital signature
     * @param array $params
     * @param string|null $method
     * @return string
     */
    public function getSignature(array $params, $method = null)
    {
        unset($params['signature']);
        ksort($params);
        $params[] = $this->secretKey;

        if ($method !== null) {
            array_unshift($params, $method);
        }

        return hash('sha256', implode('{up}', $params));
    }

    /**
     * Return IP address
     * @return string
     */
    protected function getIp()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Get URL for pay through the form
     * @param string $publicKey
     * @param string|float|int $sum
     * @param string $account
     * @param string $desc
     * @param string $currency
     * @param string $locale
     * @return string
     */
    public function form($publicKey, $sum, $account, $desc, $currency = 'RUB', $locale = 'ru')
    {
        $vitalParams = [
            'account'  => $account,
            'currency' => $currency,
            'desc'     => $desc,
            'sum'      => $sum
        ];
        $this->params = array_merge($this->params, $vitalParams);
        if ($this->secretKey) {
            $this->params['signature'] = $this->getSignature($vitalParams);
        }
        $this->params['locale'] = $locale;
        return $this->formUrl . $publicKey . '?' . http_build_query($this->params);
    }

    /**
     * Set customer email
     * @param string $email
     * @return $this
     */
    public function setCustomerEmail($email)
    {
        $this->params['customerEmail'] = $email;
        return $this;
    }

    /**
     * Set customer phone number
     * @param string $phone
     * @return $this
     */
    public function setCustomerPhone($phone)
    {
        $this->params['customerPhone'] = $phone;
        return $this;
    }

    /**
     * Set list of paid goods
     * @param CashItem[] $items
     * @return $this
     */
    public function setCashItems(array $items)
    {
        $this->params['cashItems'] = base64_encode(
            json_encode(
                /** @var CashItem $item */
                array_map(static function ($item) {
                    return [
                        'name'          => $item->getName(),
                        'count'         => $item->getCount(),
                        'price'         => $item->getPrice(),
                        'nds'           => $item->getNds(),
                        'type'          => $item->getType(),
                        'paymentMethod' => $item->getPaymentMethod(),
                    ];
                }, $items)));

        return $this;
    }

    /**
     * Set callback URL
     * @param string $backUrl
     * @return $this
     */
    public function setBackUrl($backUrl)
    {
        $this->params['backUrl'] = $backUrl;
        return $this;
    }

    /**
     * Call API
     * @param string $method
     * @param array $params
     * @return object
     *
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     */
    public function api($method, array $params = [])
    {
        if (!in_array($method, $this->supportedUnitpayMethods, true)) {
            throw new UnexpectedValueException('Method is not supported');
        }

        if (isset($this->requiredUnitpayMethodsParams[$method])) {
            foreach ($this->requiredUnitpayMethodsParams[$method] as $rParam) {
                if (!isset($params[$rParam])) {
                    throw new InvalidArgumentException('Param ' . $rParam . ' is null');
                }
            }
        }

        $params['secretKey'] = $this->secretKey;
        if (empty($params['secretKey'])) {
            throw new InvalidArgumentException('SecretKey is null');
        }

        $requestUrl = $this->apiUrl . '?' . http_build_query([
                'method' => $method,
                'params' => $params,
            ], null, '&', PHP_QUERY_RFC3986);

        $response = json_decode(file_get_contents($requestUrl));
        if (!is_object($response)) {
            throw new InvalidArgumentException('Temporary server error. Please try again later.');
        }

        return $response;
    }

    /**
     * Check request on handler from Unitpay
     * @return bool
     *
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     */
    public function checkHandlerRequest()
    {
        $ip = $this->getIp();
        if (!isset($_GET['method'])) {
            throw new InvalidArgumentException('Method is null');
        }

        if (!isset($_GET['params'])) {
            throw new InvalidArgumentException('Params is null');
        }

        list($method, $params) = [$_GET['method'], $_GET['params']];

        if (!in_array($method, $this->supportedPartnerMethods, true)) {
            throw new UnexpectedValueException('Method is not supported');
        }

        if (!isset($params['signature']) || $params['signature'] !== $this->getSignature($params, $method)) {
            throw new InvalidArgumentException('Wrong signature');
        }

        /**
         * IP address check
         * @link https://help.unitpay.ru/book-of-reference/ip-addresses
         */
        if (!in_array($ip, $this->supportedUnitpayIp, true)) {
            throw new InvalidArgumentException('IP address Error');
        }
        return true;
    }

    /**
     * Response for Unitpay if handle success
     * @param string $message
     * @return string
     */
    public function getSuccessHandlerResponse($message)
    {
        return json_encode(['result' => ['message' => $message]]);
    }

    /**
     * Response for Unitpay if handle error
     * @param string $message
     * @return string
     */
    public function getErrorHandlerResponse($message)
    {
        return json_encode(['error' => ['message' => $message]]);
    }
}
