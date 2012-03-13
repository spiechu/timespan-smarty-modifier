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

abstract class TimeSpan {

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
     * Returns proper time unit string according to number of units.
     * 
     * @param int $howMany -1 means 'just now' unit
     * @param string $unitSymbol it can be s,i,h,d,m,y
     * @return string
     */
    abstract protected function getUnit($howMany, $unitSymbol);

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
     * @return TimeSpan fluent interface
     */
    public function showSuffix($suffix) {
        $this->_showSuffix = $suffix;
        return $this;
    }

    /**
     * Start date setter to compute date interval.
     * 
     * @param \DateTime $startDate
     * @return TimeSpan fluent interface
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

        $suffix = ($this->_showSuffix) ? ' ' . $this->getSuffix() : '';

        if ($interval['counter'] > 1) {
            return $interval['counter'] . ' ' . $timeUnit . $suffix;
        } elseif ($interval['counter'] == 1) {
            // in case we don't have to show number of units
            return $timeUnit . $suffix;
        } else {
            // in case of -1 'just now' offset
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
        if ($diff->y > 0) {
            $unit = 'y';
            $counter = $diff->y;
        } elseif ($diff->m > 0) {
            $unit = 'm';
            $counter = $diff->m;
        } elseif ($diff->d > 0) {
            $unit = 'd';
            $counter = $diff->d;
        } elseif ($diff->h > 0) {
            $unit = 'h';
            $counter = $diff->h;
        } elseif ($diff->i > 0) {
            $unit = 'i';
            $counter = $diff->i;
        } elseif ($diff->s > 0) {
            $unit = 's';
            $counter = ($diff->s > $this->_justNow) ? $diff->s : -1;
        } else {
            throw new TimeSpanException('Invalid DateInterval');
        }

        return array('counter' => $counter, 'unit' => $unit);
    }

}