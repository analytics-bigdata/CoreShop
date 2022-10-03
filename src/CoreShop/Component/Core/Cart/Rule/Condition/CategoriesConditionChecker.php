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

namespace CoreShop\Component\Core\Cart\Rule\Condition;

use CoreShop\Component\Core\Repository\CategoryRepositoryInterface;
use CoreShop\Component\Core\Rule\Condition\CategoriesConditionCheckerTrait;
use CoreShop\Component\Order\Cart\Rule\Condition\AbstractConditionChecker;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;

final class CategoriesConditionChecker extends AbstractConditionChecker
{
    use CategoriesConditionCheckerTrait {
        CategoriesConditionCheckerTrait::__construct as private __traitConstruct;
    }

    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
    ) {
        $this->__traitConstruct($categoryRepository);
    }

    public function isCartRuleValid(
        OrderInterface $cart,
        CartPriceRuleInterface $cartPriceRule,
        ?CartPriceRuleVoucherCodeInterface $voucher,
        array $configuration,
    ): bool {
        $categoryIdsToCheck = $this->getCategoriesToCheck(
            $configuration['categories'],
            $cart->getStore(),
            $configuration['recursive'] ?: false,
        );

        foreach ($cart->getItems() as $item) {
            $product = $item->getProduct();

            if ($product instanceof ProductInterface) {
                if (!is_array($product->getCategories())) {
                    continue;
                }

                foreach ($product->getCategories() as $category) {
                    if ($category instanceof ResourceInterface) {
                        if (in_array($category->getId(), $categoryIdsToCheck)) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }
}
