<?php
/**
 * Copyright © EzLaunch, Inc. All rights reserved.
 */
namespace EzLaunch\Quote\Api\Data;

/**
 * Interface CartItemInterface
 * @api
 */
interface CartItemInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of array, makes typos less likely
     */
    const KEY_ITEM_ID = 'item_id';

    const KEY_SKU = 'sku';

    const KEY_NAME = 'name';

    const KEY_PRODUCT_ID = 'product_id';

    const KEY_QUOTE_ID = 'quote_id';

    const KEY_CUSTOMER_ID = 'customer_id';
    const KEY_CUSTOMER_FIRSTNAME = 'customer_firstname';
    const KEY_CUSTOMER_LASTNAME = 'customer_lastname';
    const KEY_CUSTOMER_NOTE = 'customer_note';

    const KEY_ORDER_ID = 'order_id';
    const KEY_ORDER_ITEM_ID = 'order_item_id';
    const KEY_ORDER_STATUS = 'order_status';


    /**#@-*/

    /**
     * Returns the item ID.
     *
     * @return int|null Item ID. Otherwise, null.
     */
    public function getItemId();

    /**
     * Sets the item ID.
     *
     * @param int $itemId
     * @return $this
     */
    public function setItemId($itemId);

    /**
     * Returns the product SKU.
     *
     * @return string|null Product SKU. Otherwise, null.
     */
    public function getSku();

    /**
     * Sets the product SKU.
     *
     * @param string $sku
     * @return $this
     */
    public function setSku($sku);

    /**
     * Returns the product name.
     *
     * @return string|null Product name. Otherwise, null.
     */
    public function getName();

    /**
     * Sets the product name.
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Returns Quote id.
     *
     * @return int
     */
    public function getQuoteId();

    /**
     * Sets Quote id.
     *
     * @param int $quoteId
     * @return $this
     */
    public function setQuoteId($quoteId);

    /**
     * Returns the customer ID.
     *
     * @return int|null
     */
    public function getCustomerId();

    /**
     * Sets the customer ID.
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * Returns the product ID.
     *
     * @return int|null
     */
    public function getProductId();

    /**
     * Sets the product ID.
     *
     * @param int $productId
     * @return $this
     */
    public function setProductId($productId);

    /**
     * Returns the Customer first name.
     *
     * @return string|null Customer first name. Otherwise, null.
     */
    public function getCustomerFirstName();

    /**
     * Sets the Customer first name.
     *
     * @param string $name
     * @return $this
     */
    public function setCustomerFirstName($name);

    /**
     * Returns the Customer last name.
     *
     * @return string|null Customer last name. Otherwise, null.
     */
    public function getCustomerLastName();

    /**
     * Sets the Customer last name.
     *
     * @param string $name
     * @return $this
     */
    public function setCustomerLastName($name);

    /**
     * Returns the Customer note.
     *
     * @return string|null Customer note. Otherwise, null.
     */
    public function getCustomerNote();

    /**
     * Sets the Customer note.
     *
     * @param string $note
     * @return $this
     */
    public function setCustomerNote($note);

    /**
     * Returns the order ID.
     *
     * @return int|null
     */
    public function getOrderId();

    /**
     * Sets the order ID.
     *
     * @param int $orderId
     * @return $this
     */
    public function setOrderId($orderId);

    /**
     * Returns the order item ID.
     *
     * @return int|null
     */
    public function getOrderItemId();

    /**
     * Sets the order item ID.
     *
     * @param int $orderItemId
     * @return $this
     */
    public function setOrderItemId($orderItemId);

    /**
     * Returns the order status.
     *
     * @return string|null order status. Otherwise, null.
     */
    public function getOrderStatus();

    /**
     * Sets the order status.
     *
     * @param string $status
     * @return $this
     */
    public function setOrderStatus($status);
}
