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
     * @var bool marks if current time unit is special
     */
    protected $_isSpecialUnit = false;
    
    /**
     * @var array associated array with standard time unit inflection
     * @see TimeUnitEN
     */
    protected $_units;
    
    /**
     * @var array associated array with special time units
     * @see TimeUnitPL
     */
    protected $_specialUnits;
    
    /**
     * @var bool false when time unit was approximated
     */
    protected $_trulyFullUnit = true;
    
    /**
     * @var bool true when we need to add 'and a half' to current time unit count
     */
    protected $_isHalved = false;

    /**
     * @var int number of time units
     */
    protected $_unitCount;

    /**
     * @var string time unit type
     */
    protected $_unitType;

    /**
     * Returns true when time unit has been taken from $_specialUnits array.
     * 
     * @return bool 
     */
    public function isSpecialUnit() {
        return $this->_isSpecialUnit;
    }

    /**
     * Set if time unit has been approximated.
     * 
     * @param bool $tfu
     * @return AbstractTimeUnit fluent interface 
     */
    public function setTrulyFullUnit($tfu) {
        $this->_trulyFullUnit = $tfu;
        return $this;
    }

    /**
     * Returns true when unit has not been approximated in any way.
     *
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
     * Do we need to add 'and a half' to current time unit?
     *
     * @return bool
     */
    public function isHalved() {
        return $this->_isHalved;
    }

    /**
     * Set number of time units.
     * 
     * @param int $uc
     * @return AbstractTimeUnit fluent interface
     */
    public function setUnitCount($uc) {
        $this->_unitCount = $uc;
        return $this;
    }

    /**
     * Get number of time units.
     * 
     * @return int 
     */
    public function getUnitCount() {
        return $this->_unitCount;
    }

    /**
     * Returns true when time unit count was approximated or 'and a half' should be added.
     * 
     * @return bool
     */
    public function isApproximated() {
        return ($this->_isHalved || (! $this->_trulyFullUnit));
    }

    /**
     * Time unit type setter.
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
     * Returns time unit type.
     *
     * @return string
     */
    public function getUnitType() {
        return $this->_unitType;
    }

    /**
     * Returns proper time unit string according to number of time units.
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
     * Returns translated 'and a half' string.
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