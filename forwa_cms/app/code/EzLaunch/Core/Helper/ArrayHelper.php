<?php

/**
 * Copyright © EzLaunch, Inc. All rights reserved.
 */

namespace EzLaunch\Core\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;

class ArrayHelper extends AbstractHelper{

    const DEFAULT_ID = 1;
    
    /**
     * Get first non-default id in list of id
     *
     * @param  array $ids
     * @return int|null
     */
    public function getFirstNonDefaultIdOrNull(array $ids) : ?int {
        foreach ($ids as $id) { 
            if($id != self::DEFAULT_ID){
                return $id;
            }
        }

        return null;
    }
    
}