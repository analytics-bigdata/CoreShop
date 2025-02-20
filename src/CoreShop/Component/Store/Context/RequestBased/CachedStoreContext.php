<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Store\Context\RequestBased;

use CoreShop\Component\Store\Context\StoreContextInterface;
use CoreShop\Component\Store\Model\StoreInterface;

final class CachedStoreContext implements StoreContextInterface
{
    private ?StoreInterface $cachedStore = null;

    public function __construct(
        private StoreContextInterface $requestBasedStoreContext,
    ) {
    }

    public function getStore(): StoreInterface
    {
        if (null === $this->cachedStore) {
            $this->cachedStore = $this->requestBasedStoreContext->getStore();
        }

        return $this->cachedStore;
    }
}
