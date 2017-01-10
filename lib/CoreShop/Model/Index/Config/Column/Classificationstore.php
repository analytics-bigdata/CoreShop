<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Index\Config\Column;

use CoreShop\Model\Index\Config\Column as BaseColumn;

/**
 * Class Classificationstore
 * @package CoreShop\Model\Index\Config\Column
 */
class Classificationstore extends BaseColumn
{
    /**
     * @var int
     */
    public $keyConfigId;

    /**
     * @var int
     */
    public $groupConfigId;

    /**
     * @return int
     */
    public function getKeyConfigId()
    {
        return $this->keyConfigId;
    }

    /**
     * @param int $keyConfigId
     */
    public function setKeyConfigId($keyConfigId)
    {
        $this->keyConfigId = $keyConfigId;
    }

    /**
     * @return int
     */
    public function getGroupConfigId()
    {
        return $this->groupConfigId;
    }

    /**
     * @param int $groupConfigId
     */
    public function setGroupConfigId($groupConfigId)
    {
        $this->groupConfigId = $groupConfigId;
    }
}
