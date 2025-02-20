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

namespace CoreShop\Bundle\CoreBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\AdminController;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Product\Cloner\ProductClonerInterface;
use CoreShop\Component\Core\Product\Cloner\ProductQuantityPriceRulesCloner;
use CoreShop\Component\Core\Product\Cloner\ProductUnitDefinitionsCloner;
use CoreShop\Component\Core\Repository\ProductRepositoryInterface;
use CoreShop\Component\Resource\Model\AbstractObject;
use Pimcore\Model\DataObject;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\Attribute\SubscribedService;

class ProductVariantUnitSolidifierController extends AdminController
{
    public const STATUS_ERROR_NO_VARIANTS = 'error_no_variants';

    public const STATUS_ERROR_NO_UNIT_DEFINITIONS = 'error_nno_unit_definitions';

    public const DISPATCH_STRATEGY_ONLY_UNIT_DEFINITIONS = 'strategy_only_unit_definitions';

    public const DISPATCH_STRATEGY_UNIT_DEFINITIONS_AND_QPR = 'strategy_only_unit_definitions_and_qpr';

    public function checkStatusAction(Request $request, int $objectId): Response
    {
        /** @var DataObject\Concrete $object */
        $object = $this->getProductRepository()->find($objectId);

        if (!$object instanceof ProductInterface) {
            return new JsonResponse([
                'success' => false,
                'message' => sprintf('%s is not a valid product', $objectId),
            ]);
        }

        $strategy = null;
        $errorStatus = false;

        $variants = $object->getChildren([AbstractObject::OBJECT_TYPE_VARIANT], true);

        if (count($variants) === 0) {
            $errorStatus = self::STATUS_ERROR_NO_VARIANTS;
        } elseif ($object->hasUnitDefinitions() === false) {
            $errorStatus = self::STATUS_ERROR_NO_UNIT_DEFINITIONS;
        }

        if ($errorStatus === false) {
            $strategy = self::DISPATCH_STRATEGY_ONLY_UNIT_DEFINITIONS;
            if (count($object->getQuantityPriceRules()) > 0) {
                $strategy = self::DISPATCH_STRATEGY_UNIT_DEFINITIONS_AND_QPR;
            }
        }

        return new JsonResponse([
            'success' => true,
            'errorStatus' => $errorStatus,
            'strategy' => $strategy,
        ]);
    }

    public function applyAction(Request $request, int $objectId): Response
    {
        $success = true;
        $message = null;

        /** @var DataObject\Concrete $object */
        $object = $this->getProductRepository()->find($objectId);

        if (!$object instanceof ProductInterface) {
            return new JsonResponse([
                'success' => false,
                'message' => sprintf('%s is not a valid product', $objectId),
            ]);
        }

        $dispatchedVariants = [];

        foreach ($object->getChildren([AbstractObject::OBJECT_TYPE_VARIANT], true) as $variant) {
            if (!$variant instanceof ProductInterface) {
                continue;
            }

            try {
                $this->getUnitDefinitionsCloner()->clone($variant, $object, false);
            } catch (\Throwable $e) {
                $success = false;
                $message = sprintf(
                    'error while cloning unit definition from product %d to variant %d. Error was: %s',
                    $object->getId(),
                    $variant->getId(),
                    $e->getMessage(),
                );

                break;
            }

            try {
                $this->getQuantityPriceRulesCloner()->clone($variant, $object, false);
            } catch (\Throwable $e) {
                $success = false;
                $message = sprintf(
                    'error while cloning quantity price rules from product %d to variant %d. Error was: %s',
                    $object->getId(),
                    $variant->getId(),
                    $e->getMessage(),
                );

                break;
            }

            try {
                $variant->save();
            } catch (\Throwable $e) {
                $success = false;
                $message = sprintf('error while saving variant %d. Error was: %s', $variant->getId(), $e->getMessage());

                break;
            }

            $dispatchedVariants[] = $variant->getId();
        }

        return new JsonResponse([
            'success' => $success,
            'message' => $message,
            'affectedVariants' => $dispatchedVariants,
        ]);
    }

    protected function getProductRepository(): ProductRepositoryInterface
    {
        return $this->container->get('coreshop.repository.product');
    }

    protected function getQuantityPriceRulesCloner(): ProductClonerInterface
    {
        return $this->container->get(ProductQuantityPriceRulesCloner::class);
    }

    protected function getUnitDefinitionsCloner(): ProductClonerInterface
    {
        return $this->container->get(ProductUnitDefinitionsCloner::class);
    }

    public static function getSubscribedServices(): array
    {
        return parent::getSubscribedServices() + [
            new SubscribedService('coreshop.repository.product', ProductRepositoryInterface::class),
            ProductQuantityPriceRulesCloner::class,
            ProductUnitDefinitionsCloner::class,
        ];
    }
}
