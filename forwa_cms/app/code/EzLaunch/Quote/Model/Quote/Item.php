<?php
/**
 * Copyright © EzLaunch, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace EzLaunch\Quote\Model\Quote;

use EzLaunch\Quote\Api\Data\CartItemInterface;

/**
 * Sales Quote Item Model
 *
 */

class Item extends \Magento\Framework\Model\AbstractModel implements CartItemInterface
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'ezlaunch_sales_quote_item';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'item';

    /**
     * Quote model object
     *
     * @var \Magento\Quote\Model\Quote
     */
    protected $_quote;

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Magento\Quote\Model\ResourceModel\Quote\Item::class);
    }

    /**
     * Declare quote model object
     *
     * @param  \Magento\Quote\Model\Quote $quote
     * @return $this
     */
    public function setQuote(\Magento\Quote\Model\Quote $quote)
    {
        $this->_quote = $quote;
        $this->setQuoteId($quote->getId());
        return $this;
    }

    /**
     * Retrieve quote model object
     *
     * @codeCoverageIgnore
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        return $this->_quote;
    }

    /**
     * @inheritdoc
     * @codeCoverageIgnoreStart
     */
    public function getItemId()
    {
        return $this->getData(self::KEY_ITEM_ID);
    }

    /**
     * @inheritdoc
     */
    public function setItemId($itemID)
    {
        return $this->setData(self::KEY_ITEM_ID, $itemID);
    }

    /**
     * @inheritdoc
     */
    public function getSku()
    {
        return $this->getData(self::KEY_SKU);
    }

    /**
     * @inheritdoc
     */
    public function setSku($sku)
    {
        return $this->setData(self::KEY_SKU, $sku);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->getData(self::KEY_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        return $this->setData(self::KEY_NAME, $name);
    }

    /**
     * @inheritdoc
     */
    public function getQuoteId()
    {
        return $this->getData(self::KEY_QUOTE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setQuoteId($quoteId)
    {
        return $this->setData(self::KEY_QUOTE_ID, $quoteId);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerId()
    {
        return $this->getData(self::KEY_CUSTOMER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::KEY_CUSTOMER_ID, $customerId);
    }

    /**
     * @inheritdoc
     */
    public function getProductId()
    {
        return $this->getData(self::KEY_PRODUCT_ID);
    }

    /**
     * @inheritdoc
     */
    public function setProductId($productId)
    {
        return $this->setData(self::KEY_PRODUCT_ID, $productId);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerFirstName()
    {
        return $this->getData(self::KEY_CUSTOMER_FIRSTNAME);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerFirstName($name)
    {
        return $this->setData(self::KEY_CUSTOMER_FIRSTNAME, $name);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerLastName()
    {
        return $this->getData(self::KEY_CUSTOMER_LASTNAME);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerLastName($name)
    {
        return $this->setData(self::KEY_CUSTOMER_LASTNAME, $name);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerNote()
    {
        return $this->getData(self::KEY_CUSTOMER_NOTE);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerNote($note)
    {
        return $this->setData(self::KEY_CUSTOMER_NOTE, $note);
    }

    /**
     * @inheritdoc
     */
    public function getOrderId()
    {
        return $this->getData(self::KEY_ORDER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::KEY_ORDER_ID, $orderId);
    }

    /**
     * @inheritdoc
     */
    public function getOrderItemId()
    {
        return $this->getData(self::KEY_ORDER_ITEM_ID);
    }

    /**
     * @inheritdoc
     */
    public function setOrderItemId($orderItemId)
    {
        return $this->setData(self::KEY_ORDER_ITEM_ID, $orderItemId);
    }

    /**
     * @inheritdoc
     */
    public function getOrderStatus()
    {
        return $this->getData(self::KEY_ORDER_STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setOrderStatus($status)
    {
        return $this->setData(self::KEY_ORDER_STATUS, $status);
    }
}
