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
    protected $_unitCount;
    protected $_unitType;

    /**
     * @return bool 
     */
    public function isSpecialUnit() {
        return $this->_isSpecialUnit;
    }

    public function setTrulyFullUnit($tfu) {
        $this->_trulyFullUnit = $tfu;
        return $this;
    }

    public function isTrulyFullUnit() {
        return $this->_trulyFullUnit;
    }

    public function setHalved($h) {
        $this->_isHalved = $h;
        return $this;
    }

    public function isHalved() {
        return $this->_isHalved;
    }

    public function setUnitCount($uc) {
        $this->_unitCount = $uc;
        return $this;
    }

    public function getUnitCount() {
        return $this->_unitCount;
    }
    
    public function isApproximated() {
        return ($this->_isHalved || (! $this->_trulyFullUnit));
    }

    public function setUnitType($ut) {
        if (!in_array($ut, array('s', 'i', 'h', 'd', 'm', 'y')))
            throw new TimeSpanException("Unknown unit type: {$ut}");
        $this->_unitType = $ut;
        return $this;
    }

    public function getUnitType() {
        return $this->_unitType;
    }

    /**
     * Returns proper time unit string according to number of units.
     * 
     * @return string
     */
    abstract public function getUnit();

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