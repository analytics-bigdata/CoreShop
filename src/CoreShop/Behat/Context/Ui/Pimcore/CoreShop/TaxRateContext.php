<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Behat\Context\Ui\Pimcore\CoreShop;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Page\Pimcore\CoreShop\TaxRatePageInterface;
use CoreShop\Behat\Page\Pimcore\PWAPageInterface;
use Webmozart\Assert\Assert;

final class TaxRateContext implements Context
{
    private PWAPageInterface $pwaPage;
    private TaxRatePageInterface $taxRatePage;

    public function __construct(
        PWAPageInterface $pwaPage,
        TaxRatePageInterface $taxRatePage
    )
    {
        $this->pwaPage = $pwaPage;
        $this->taxRatePage = $taxRatePage;
    }

    /**
     * @When tax-rates tab is open
     */
    public function taxRatesTabIsOpen(): void
    {
        Assert::true($this->taxRatePage->isActiveOpen());
    }
}