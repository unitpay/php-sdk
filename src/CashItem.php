<?php
/**
 * UnitPay Payment Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category        UnitPay
 * @package         unitpay/unitpay
 * @version         1.0.0
 * @author          UnitPay
 * @copyright       Copyright (c) 2015 UnitPay
 * @license         http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * EXTENSION INFORMATION
 *
 * UNITPAY API       https://unitpay.ru/doc
 *
 */

namespace unitpay;

/**
 * Value object for paid goods
 */
class CashItem
{
    private $name;

    private $count;

    private $price;

    /**
     * @param string $name
     * @param int $count
     * @param float $price
     */
    public function __construct($name, $count, $price)
    {
        $this->name  = $name;
        $this->count = $count;
        $this->price = $price;
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
}
