<?php

/*
 * This file is part of the TimeSpan package.
 *
 * (c) Dawid Spiechowicz <spiechu@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spiechu\TimeSpan\TimeUnit;

abstract class AbstractTimeUnit {

    /**
     * @var bool marks if current unit is special
     */
    protected $_isSpecialUnit = false;
    protected $_units = array();
    protected $_specialUnits = array();

    /**
     * @return bool 
     */
    public function isSpecialUnit() {
        return $this->_isSpecialUnit;
    }

    /**
     * Returns proper time unit string according to number of units.
     * 
     * @param int $howMany -1 means 'just now' unit
     * @param string $unitSymbol it can be s,i,h,d,m,y
     * @param bool $half
     * @return string
     */
    abstract public function getUnit($howMany, $unitSymbol, $half);

    /**
     * Returns translated 'almost' prefix.
     * 
     * @return string
     */
    abstract public function getPrefix();

    /**
     * Returns translated 'and half' string.
     * 
     * @return string
     */
    abstract public function getHalf();

    /**
     * Returns translated 'and' string.
     * 
     * @return string
     */
    abstract public function getConjunctionWord();

    /**
     * Returns translated 'ago' suffix.
     * 
     * @return string
     */
    abstract public function getSuffix();
}