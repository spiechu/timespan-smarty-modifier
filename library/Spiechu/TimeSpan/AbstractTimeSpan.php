<?php

/*
 * This file is part of the TimeSpan package.
 *
 * (c) Dawid Spiechowicz <spiechu@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spiechu\TimeSpan;

abstract class AbstractTimeSpan {

    /**
     * @var int seconds to show 'just now' instead of exact units 
     */
    protected $_justNow = 10;

    /**
     * @var bool show 'ago' suffix in return string 
     */
    protected $_showSuffix = true;

    /**
     * @var DateTime|null DateTime from which we measure DateInterval from now
     */
    protected $_startDate = null;

    /**
     * @var float percentage tolerance to mark half unit true
     */
    protected $_halfTolerance = 15.0;

    /**
     * @var float percentage tolerance to mark almost full unit true
     */
    protected $_almostFullTolerance = 15.0;

    /**
     * Returns proper time unit string according to number of units.
     * 
     * @param int $howMany -1 means 'just now' unit
     * @param string $unitSymbol it can be s,i,h,d,m,y
     * @return string
     */
    abstract protected function getUnit($howMany, $unitSymbol);

    /**
     * Returns translated 'almost' prefix.
     * 
     * @return string
     */
    abstract protected function getPrefix();

    /**
     * Returns translated 'and half' string.
     * 
     * @return string
     */
    abstract protected function getHalf();

    /**
     * Returns translated 'ago' suffix.
     * 
     * @return string
     */
    abstract protected function getSuffix();

    /**
     * Show 'ago' suffix?
     * 
     * @param bool $suffix 
     * @return AbstractTimeSpan fluent interface
     */
    public function showSuffix($suffix) {
        $this->_showSuffix = $suffix;
        return $this;
    }

    /**
     * Start date setter to compute date interval.
     * 
     * @param \DateTime $startDate
     * @return AbstractTimeSpan fluent interface
     */
    public function setStartDate(\DateTime $startDate) {
        $this->_startDate = $startDate;
        return $this;
    }

    /**
     * Returns translated string.
     * 
     * @return string
     */
    public function getTimeSpan() {
        $interval = $this->getInterval();
        $timeUnit = $this->getUnit($interval['counter'], $interval['unit']);

        $prefix = ($interval['almost'] || $interval['half'] || $interval['approx']) ? $this->getPrefix() . ' ' : '';
        $half = ($interval['half'] && $interval['counter'] > 0) ? $this->getHalf() . ' ' : '';
        $suffix = ($this->_showSuffix) ? ' ' . $this->getSuffix() : '';

        if ($interval['counter'] > 1) {
            return $prefix . $interval['counter'] . ' ' . $half . $timeUnit . $suffix;
        } elseif ($interval['counter'] >= 0) {

            // in case we don't have to show number of units
            return $prefix . $timeUnit . ' ' . $half . $suffix;
        } else {

            // in case of 'just now' -1 offset we don't need 'ago' suffix
            return $timeUnit;
        }
    }

    /**
     * Returns unit type and unit count array.
     * 
     * @return array 
     * @throws Spiechu\TimeSpan\TimeSpanException
     */
    protected function getInterval() {
        $curDate = new \DateTime('now');
        $diff = $curDate->diff($this->_startDate);
        $approx = false;

        // counting years
        if ($diff->y > 0) {
            $unit = 'y';
            $counter = $diff->y;
            $half = $this->isHalfUnit($diff->m, 12);
            $almostFull = $this->almostFullUnit($diff->m, 12);
            if ($almostFull) {
                ++$counter;
            }

            // counting months
        } elseif ($diff->m > 0) {
            $almostFull = $this->almostFullUnit($diff->m, 12);
            if ($almostFull) {
                $half = false;
                $unit = 'y';
                $counter = 1;
            } elseif ($this->isHalfUnit($diff->m, 12)) {
                $half = false;
                $unit = 'y';
                $counter = 0;
                $approx = true;
            } else {
                $unit = 'm';
                $counter = $diff->m;
                $half = $this->isHalfUnit($diff->d, 30);
                $almostFull = $this->almostFullUnit($diff->d, 30);
                if ($almostFull) {
                    ++$counter;
                }
            }

            // counting days
        } elseif ($diff->d > 0) {
            $almostFull = $this->almostFullUnit($diff->d, 30);
            if ($almostFull) {
                $half = false;
                $unit = 'm';
                $counter = 1;
            } elseif ($this->isHalfUnit($diff->d, 30)) {
                $half = false;
                $unit = 'm';
                $counter = 0;
                $approx = true;
            } else {
                $unit = 'd';
                $counter = $diff->d;
                $half = $this->isHalfUnit($diff->h, 24);
                $almostFull = $this->almostFullUnit($diff->h, 24);
                if ($almostFull) {
                    ++$counter;
                }
            }

            // counting hours
        } elseif ($diff->h > 0) {
            $almostFull = $this->almostFullUnit($diff->h, 24);
            if ($almostFull) {
                $half = false;
                $unit = 'd';
                $counter = 1;
            } elseif ($this->isHalfUnit($diff->h, 24)) {
                $half = false;
                $unit = 'd';
                $counter = 0;
                $approx = true;
            } else {
                $unit = 'h';
                $counter = $diff->h;
                $half = $this->isHalfUnit($diff->i, 60);
                $almostFull = $this->almostFullUnit($diff->i, 60);
                if ($almostFull) {
                    ++$counter;
                }
            }

            // counting minutes
        } elseif ($diff->i > 0) {
            $almostFull = $this->almostFullUnit($diff->i, 60);
            if ($almostFull) {
                $half = false;
                $unit = 'h';
                $counter = 1;
            } elseif ($this->isHalfUnit($diff->i, 60)) {
                $half = false;
                $unit = 'h';
                $counter = 0;
                $approx = true;
            } else {
                $unit = 'i';
                $counter = $diff->i;
                $half = $this->isHalfUnit($diff->s, 60);
                $almostFull = $this->almostFullUnit($diff->s, 60);
                if ($almostFull) {
                    ++$counter;
                }
            }

            // counting seconds
        } elseif ($diff->s > 0) {
            $almostFull = $this->almostFullUnit($diff->s, 60);
            if ($almostFull) {
                $half = false;
                $unit = 'i';
                $counter = 1;
            } else {
                $half = $this->isHalfUnit($diff->s, 60);
                if ($half) {
                    $unit = 'i';
                    $counter = 0;
                    $approx = true;
                } else {
                    $unit = 's';
                    $counter = ($diff->s > $this->_justNow) ? $diff->s : -1;
                }
            }

            // in case of bad interval (less than 1 second)
        } else {
            throw new TimeSpanException('Invalid DateInterval');
        }
        
        if ($approx === false) {
            $approx = ($half || $almostFull);
        }

        return array('counter' => $counter,
            'unit' => $unit,
            'half' => $half,
            'almost' => $almostFull,
            'approx' => $approx);
    }

    /**
     * Return true if actual unit is within half unit scope including tolerance.
     * @param int $actualUnit
     * @param int $fullUnit
     * @return bool 
     */
    protected function isHalfUnit($actualUnit, $fullUnit) {
        $halfUnit = floatval($fullUnit) / 2;
        $percentageUnit = ceil($halfUnit * ($this->_halfTolerance / 100.0));
        return ($actualUnit <= ($halfUnit + $percentageUnit) && $actualUnit >= ($halfUnit - $percentageUnit)) ? true : false;
    }

    /**
     * Return true if actual unit is near full unit scope including tolerance.
     * @param int $actualUnit
     * @param int $fullUnit
     * @return bool
     */
    protected function almostFullUnit($actualUnit, $fullUnit) {
        return ($actualUnit >= $fullUnit - ceil($fullUnit * ($this->_almostFullTolerance / 100))) ? true : false;
    }

}