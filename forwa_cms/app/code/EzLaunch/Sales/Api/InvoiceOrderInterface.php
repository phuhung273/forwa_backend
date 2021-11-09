<?php
/**
 * Copyright © EzLaunch, Inc. All rights reserved.
 */

namespace EzLaunch\Sales\Api;

/**
 * Class InvoiceOrderInterface
 *
 */
interface InvoiceOrderInterface
{
    /**
     * Create invoice, create noti to customer
     * 
     * @param int $orderId
     * @param string $productName
     * @return int
     */
    public function createInvoice($orderId, $productName);
}
