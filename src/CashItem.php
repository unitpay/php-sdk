<?php

namespace UnitPay;

/**
 * Class CashItem
 *
 * @package     UnitPay
 *
 * @author      UnitPay <support@unitpay.ru>
 * @version     3.0.0
 * @since       1.0.0
 */
class CashItem
{
    public const NDS_NONE = 'none';
    public const NDS_0 = 'vat0';
    public const NDS_10 = 'vat10';
    public const NDS_20 = 'vat20';

    /** Товар */
    public const TYPE_COMMODITY = 'commodity';
    /** Подакцизный товар */
    public const TYPE_EXCISE = 'excise';
    /** Работа */
    public const TYPE_JOB = 'job';
    /** Услуга */
    public const TYPE_SERVICE = 'service';
    /** Ставка */
    public const TYPE_GAMBLING_BET = 'gambling_bet';
    /** Выигрыш */
    public const TYPE_GAMBLING_PRIZE = 'gambling_prize';
    /** Лотерейный билет */
    public const TYPE_LOTTERY = 'lottery';
    /** Выигрыш лотереи */
    public const TYPE_LOTTERY_PRIZE = 'lottery_prize';
    /** Результаты интеллектуальной деятельности */
    public const TYPE_INTELLECTUAL_ACTIVITY = 'intellectual_activity';
    /** Платёж */
    public const TYPE_PAYMENT = 'payment';
    /** Агентское вознаграждение */
    public const TYPE_AGENT_COMMISSION = 'agent_commission';
    /** Составной предмет расчёта */
    public const TYPE_COMPOSITE = 'composite';
    /** Иной предмет расчёта */
    public const TYPE_ANOTHER = 'another';
    /**  */
    public const TYPE_PROPERTY_RIGHT = 'property_right';
    /**  */
    public const TYPE_NON_OPERATING_GAIN = 'non-operating_gain';
    /**  */
    public const TYPE_INSURANCE_PREMIUM = 'insurance_premium';
    /** Налог с продажи */
    public const TYPE_SALES_TAX = 'sales_tax';
    /** Курортный сбор */
    public const TYPE_RESORT_FEE = 'resort_fee';

    /** 100% предоплата */
    public const PAYMENT_METHOD_PREPAYMENT_FULL = 'full_prepayment';
    /** Частичная предоплата */
    public const PAYMENT_METHOD_PREPAYMENT = 'prepayment';
    /** Аванс */
    public const PAYMENT_METHOD_ADVANCE = 'advance';
    /** Полный расчёт */
    public const PAYMENT_METHOD_PAYMENT_FULL = 'full_payment';

    public const ALLOWED_PAYMENT_METHODS = [
        self::PAYMENT_METHOD_PREPAYMENT_FULL,
        self::PAYMENT_METHOD_PREPAYMENT,
        self::PAYMENT_METHOD_ADVANCE,
        self::PAYMENT_METHOD_PAYMENT_FULL,
    ];

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $count;

    /**
     * @var float
     */
    private $price;

    /**
     * @var string
     */
    private $nds;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $paymentMethod;

    public function __construct(
        $name,
        $count,
        $price,
        $nds = self::NDS_NONE,
        $type = self::TYPE_COMMODITY,
        $paymentMethod = self::PAYMENT_METHOD_PREPAYMENT_FULL
    ) {
        $this->name          = $name;
        $this->count         = $count;
        $this->price         = $price;
        $this->nds           = $nds;
        $this->type          = $type;
        $this->paymentMethod = $paymentMethod;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getCount(): int {
        return $this->count;
    }

    /**
     * @return string
     */
    public function getNds(): string {
        return $this->nds;
    }

    /**
     * @return string
     */
    public function getPaymentMethod(): string {
        return $this->paymentMethod;
    }

    /**
     * @return float
     */
    public function getPrice(): float {
        return $this->price;
    }

    /**
     * @return string
     */
    public function getType(): string {
        return $this->type;
    }
}