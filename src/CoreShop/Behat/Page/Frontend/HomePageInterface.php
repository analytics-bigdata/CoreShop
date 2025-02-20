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

namespace CoreShop\Behat\Page\Frontend;

interface HomePageInterface extends FrontendPageInterface
{
    public function getContent(): string;

    public function hasLogoutButton(): bool;

    public function logOut();

    public function getActiveCurrency(): string;

    public function getAvailableCurrencies(): array;

    public function switchCurrency(string $currencyCode): void;

    public function getActiveLocale(): string;

    public function getAvailableLocales(): array;

    public function switchLocale(string $localeCode): void;

    public function getLatestProductsNames(): array;
}
