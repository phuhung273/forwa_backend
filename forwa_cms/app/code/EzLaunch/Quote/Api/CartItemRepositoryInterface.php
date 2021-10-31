<?php
/**
 * Copyright © EzLaunch, Inc. All rights reserved.
 */
namespace EzLaunch\Quote\Api;

use Magento\Quote\Api\CartItemRepositoryInterface as MagentoCartItemRepositoryInterface;

/**
 * Interface CartItemRepositoryInterface
 * @api
 * @since 100.0.2
 */
interface CartItemRepositoryInterface extends MagentoCartItemRepositoryInterface
{
    /**
     * Add item to cart. Create cart if not exist.
     *
     * @param \Magento\Quote\Api\Data\CartItemInterface $cartItem The item.
     * @param string $message The message.
     * @param int $customerId The customer ID.
     * @return int Order Id
     * @throws \Magento\Framework\Exception\CouldNotSaveException The specified item could not be saved to the cart.
     * @throws \Magento\Framework\Exception\NoSuchEntityException If customer with the specified ID does not exist.
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addToCartCreateOrder(\Magento\Quote\Api\Data\CartItemInterface $cartItem, $message, $customerId);

    /**
     * Lists quote contains specific product.
     *
     * @param int $productId The product ID.
     * @return \EzLaunch\Quote\Api\Data\CartItemSearchResultsInterface
     */
    public function getListContain($productId);

    /**
     * Lists quote of specific customer contains specific product.
     *
     * @param int $customerId The customer ID.
     * @param int $productId The product ID.
     * @return \EzLaunch\Quote\Api\Data\CartItemInterface[]
     */
    public function getListOfCustomerContain($customerId, $productId);
}
