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

use \Spiechu\TimeSpan\TimeSpanException;

abstract class AbstractTimeUnit {

    /**
     * @var bool marks if current unit is special
     */
    protected $_isSpecialUnit = false;
    protected $_units = array();
    protected $_specialUnits = array();
    protected $_trulyFullUnit = true;
    protected $_isHalved = false;

    /**
     * @var int
     */
    protected $_unitCount;

    /**
     * @var string 
     */
    protected $_unitType;

    /**
     * Returns true when unit has been taken from $_specialUnits array.
     * 
     * @return bool 
     */
    public function isSpecialUnit() {
        return $this->_isSpecialUnit;
    }

    /**
     * Returns true when unit has not been approximated in any way.
     * 
     * @param bool $tfu
     * @return AbstractTimeUnit fluent interface 
     */
    public function setTrulyFullUnit($tfu) {
        $this->_trulyFullUnit = $tfu;
        return $this;
    }

    /**
     * @return bool
     */
    public function isTrulyFullUnit() {
        return $this->_trulyFullUnit;
    }

    /**
     * Should half unit need to be added apart from $_unitCount.
     * 
     * @param bool $h
     * @return AbstractTimeUnit fluent interface
     */
    public function setHalved($h) {
        $this->_isHalved = $h;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHalved() {
        return $this->_isHalved;
    }

    /**
     * Set number of units.
     * 
     * @param int $uc
     * @return AbstractTimeUnit fluent interface
     */
    public function setUnitCount($uc) {
        $this->_unitCount = $uc;
        return $this;
    }

    /**
     * Get number of units.
     * 
     * @return int 
     */
    public function getUnitCount() {
        return $this->_unitCount;
    }

    /**
     * Returns true when unit count was approximated 
     * or 'and half' should be added.
     * 
     * @return bool
     */
    public function isApproximated() {
        return ($this->_isHalved || (!$this->_trulyFullUnit));
    }

    /**
     * Unit type setter.
     * 
     * @param string $ut
     * @return AbstractTimeUnit fluent interface
     * @throws TimeSpanException when $ut is not one of 's', 'i', 'h', 'd', 'm', 'y'
     */
    public function setUnitType($ut) {
        if (!in_array($ut, array('s', 'i', 'h', 'd', 'm', 'y')))
            throw new TimeSpanException("Unknown unit type: {$ut}");
        $this->_unitType = $ut;
        return $this;
    }

    /**
     * @return string
     */
    public function getUnitType() {
        return $this->_unitType;
    }

    /**
     * Returns proper time unit string according to number of units.
     * 
     * @return string
     */
    abstract public function getUnitString();

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