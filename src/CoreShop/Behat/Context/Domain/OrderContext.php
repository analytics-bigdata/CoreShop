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

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Bundle\WorkflowBundle\Manager\StateMachineManager;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Order\OrderInvoiceTransitions;
use CoreShop\Component\Order\OrderPaymentTransitions;
use CoreShop\Component\Order\OrderShipmentTransitions;
use CoreShop\Component\Order\OrderTransitions;
use Webmozart\Assert\Assert;

final class OrderContext implements Context
{
    public function __construct(
        private StateMachineManager $stateMachineManager,
    ) {
    }

    /**
     * @Then /^there should be one product in (my order)$/
     */
    public function thereShouldBeOneProductInTheOrder(OrderInterface $order): void
    {
        Assert::eq(
            count($order->getItems()),
            1,
            sprintf(
                'There should be only one product in the order, but found %d',
                count($order->getItems()),
            ),
        );
    }

    /**
     * @Then /^(the order) total should be "([^"]+)" including tax$/
     */
    public function orderTotalShouldBeIncludingTax(OrderInterface $order, $total): void
    {
        Assert::eq(
            $total,
            $order->getTotal(true),
            sprintf(
                'Order total is expected to be %s, but it is %s',
                $total,
                $order->getTotal(true),
            ),
        );
    }

    /**
     * @Then /^(the order) total should be "([^"]+)" excluding tax$/
     */
    public function orderTotalShouldBeExcludingTax(OrderInterface $order, $total): void
    {
        Assert::eq(
            $total,
            $order->getTotal(false),
            sprintf(
                'Order total is expected to be %s, but it is %s',
                $total,
                $order->getTotal(false),
            ),
        );
    }

    /**
     * @Then /^(the order) subtotal should be "([^"]+)" including tax$/
     */
    public function orderSubtotalShouldBeIncludingTax(OrderInterface $order, $total): void
    {
        Assert::eq(
            $total,
            $order->getSubtotal(true),
            sprintf(
                'Order subtotal is expected to be %s, but it is %s',
                $total,
                $order->getSubtotal(true),
            ),
        );
    }

    /**
     * @Then /^(the order) subtotal should be "([^"]+)" excluding tax$/
     */
    public function orderSubtotalShouldBeExcludingTax(OrderInterface $order, $total): void
    {
        Assert::eq(
            $total,
            $order->getSubtotal(false),
            sprintf(
                'Order subtotal is expected to be %s, but it is %s',
                $total,
                $order->getSubtotal(false),
            ),
        );
    }

    /**
     * @Then /^(the order) should weigh ([^"]+)kg$/
     */
    public function orderShouldWeigh(OrderInterface $order, $kg): void
    {
        Assert::eq(
            $kg,
            $order->getWeight(),
            sprintf(
                'Order is expected to weigh %skg, but it weighs %skg',
                $kg,
                $order->getWeight(),
            ),
        );
    }

    /**
     * @Then /^(the order) shipping should be "([^"]+)" including tax$/
     */
    public function orderShippingShouldBeIncludingTax(OrderInterface $order, $shipping): void
    {
        Assert::eq(
            $shipping,
            $order->getShipping(true),
            sprintf(
                'Order shipping is expected to be %s, but it is %s',
                $shipping,
                $order->getShipping(true),
            ),
        );
    }

    /**
     * @Then /^(the order) shipping should be "([^"]+)" excluding tax$/
     */
    public function orderShippingShouldBeExcludingTax(OrderInterface $order, $shipping): void
    {
        Assert::eq(
            $shipping,
            $order->getShipping(false),
            sprintf(
                'Order shipping is expected to be %s, but it is %s',
                $shipping,
                $order->getShipping(false),
            ),
        );
    }

    /**
     * @Then /^(the order) shipping tax rate should be "([^"]+)"$/
     */
    public function orderShippingTaxShouldBe(OrderInterface $order, $shippingTaxRate): void
    {
        Assert::eq(
            $shippingTaxRate,
            $order->getShippingTaxRate(),
            sprintf(
                'Order shipping tax rate is expected to be %s, but it is %s',
                $shippingTaxRate,
                $order->getShippingTaxRate(),
            ),
        );
    }

    /**
     * @Then /^(the order) state should be "([^"]+)"$/
     */
    public function orderStateShouldBeState(OrderInterface $order, $state): void
    {
        Assert::eq(
            $order->getOrderState(),
            $state,
            sprintf(
                'Expected order state to be "%s", but order is in state "%s"',
                $state,
                $order->getOrderState(),
            ),
        );
    }

    /**
     * @Then /^(the order) payment state should be "([^"]+)"$/
     */
    public function orderPaymentStateShouldBeState(OrderInterface $order, $state): void
    {
        Assert::eq(
            $order->getPaymentState(),
            $state,
            sprintf(
                'Expected payment state to be "%s", but order is in state "%s"',
                $state,
                $order->getPaymentState(),
            ),
        );
    }

    /**
     * @Then /^(the order) shipping state should be "([^"]+)"$/
     */
    public function orderShippingStateShouldBeState(OrderInterface $order, $state): void
    {
        Assert::eq(
            $order->getShippingState(),
            $state,
            sprintf(
                'Expected shipping state to be "%s", but order is in state "%s"',
                $state,
                $order->getShippingState(),
            ),
        );
    }

    /**
     * @Then /^(the order) invoice state should be "([^"]+)"$/
     */
    public function orderInvoiceStateShouldBeState(OrderInterface $order, $state): void
    {
        Assert::eq(
            $order->getInvoiceState(),
            $state,
            sprintf(
                'Expected invoice state to be "%s", but order is in state "%s"',
                $state,
                $order->getInvoiceState(),
            ),
        );
    }

    /**
     * @Then /^I should not be able to apply transition "([^"]+)" to (my order)$/
     */
    public function iShouldNotBeAbleToApplyTransition($transition, OrderInterface $order): void
    {
        $workflow = $this->stateMachineManager->get($order, OrderTransitions::IDENTIFIER);

        Assert::false($workflow->can($order, $transition));
    }

    /**
     * @Then /^I should be able to apply transition "([^"]+)" to (my order)$/
     */
    public function iShouldBeAbleToApplyTransition($transition, OrderInterface $order): void
    {
        $workflow = $this->stateMachineManager->get($order, OrderTransitions::IDENTIFIER);

        Assert::true($workflow->can($order, $transition));
    }

    /**
     * @Then /^I should not be able to apply payment transition "([^"]+)" to (my order)$/
     */
    public function iShouldNotBeAbleToApplyPaymentTransition($transition, OrderInterface $order): void
    {
        $workflow = $this->stateMachineManager->get($order, OrderPaymentTransitions::IDENTIFIER);

        Assert::false($workflow->can($order, $transition));
    }

    /**
     * @Then /^I should be able to apply payment transition "([^"]+)" to (my order)$/
     */
    public function iShouldBeAbleToApplyPaymentTransition($transition, OrderInterface $order): void
    {
        $workflow = $this->stateMachineManager->get($order, OrderPaymentTransitions::IDENTIFIER);

        Assert::true($workflow->can($order, $transition));
    }

    /**
     * @Then /^I should not be able to apply shipping transition "([^"]+)" to (my order)$/
     */
    public function iShouldNotBeAbleToApplyShippingTransition($transition, OrderInterface $order): void
    {
        $workflow = $this->stateMachineManager->get($order, OrderShipmentTransitions::IDENTIFIER);

        Assert::false($workflow->can($order, $transition));
    }

    /**
     * @Then /^I should be able to apply shipping transition "([^"]+)" to (my order)$/
     */
    public function iShouldBeAbleToApplyShippingTransition($transition, OrderInterface $order): void
    {
        $workflow = $this->stateMachineManager->get($order, OrderShipmentTransitions::IDENTIFIER);

        Assert::true($workflow->can($order, $transition));
    }

    /**
     * @Then /^I should not be able to apply invoice transition "([^"]+)" to (my order)$/
     */
    public function iShouldNotBeAbleToApplyInvoiceTransition($transition, OrderInterface $order): void
    {
        $workflow = $this->stateMachineManager->get($order, OrderInvoiceTransitions::IDENTIFIER);

        Assert::false($workflow->can($order, $transition));
    }

    /**
     * @Then /^I should be able to apply invoice transition "([^"]+)" to (my order)$/
     */
    public function iShouldBeAbleToApplyInvoiceTransition($transition, OrderInterface $order): void
    {
        $workflow = $this->stateMachineManager->get($order, OrderInvoiceTransitions::IDENTIFIER);

        Assert::true($workflow->can($order, $transition));
    }
}
