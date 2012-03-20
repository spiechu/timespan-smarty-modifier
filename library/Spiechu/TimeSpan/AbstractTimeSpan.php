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

use \DateTime,
    \DateInterval,
    Spiechu\TimeSpan\TimeSpanException;

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
     * @param bool $half
     * @return string
     */
    abstract protected function getUnit($howMany, $unitSymbol, $half);

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
    public function setStartDate(DateTime $startDate) {
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
        $timeUnit = $this->getUnit($interval['counter'], $interval['unit'], $interval['half']);

        $prefix = ($interval['approx']) ? $this->getPrefix() . ' ' : '';
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
        $currentDate = new DateTime('now');
        $dateInterval = $currentDate->diff($this->_startDate);

        $interval = $this->countYears($dateInterval);
        if (count($interval) > 0)
            return $interval;

        $interval = $this->countMonths($dateInterval);
        if (count($interval) > 0)
            return $interval;

        $interval = $this->countDays($dateInterval);
        if (count($interval) > 0)
            return $interval;

        $interval = $this->countHours($dateInterval);
        if (count($interval) > 0)
            return $interval;

        $interval = $this->countMinutes($dateInterval);
        if (count($interval) > 0)
            return $interval;

        $interval = $this->countSeconds($dateInterval);
        if (count($interval) > 0)
            return $interval;

        throw new TimeSpanException('Invalid DateInterval');
    }

    protected function countYears(DateInterval $di) {
        if ($di->y > 0) {
            $array['counter'] = $di->y;
            $array['unit'] = 'y';
            $array['half'] = $this->isHalfUnit($di->m, 12);
            $array['almost'] = $this->almostFullUnit($di->m, 12);
            if ($array['almost']) {
                ++$array['counter'];
            }
            $array['approx'] = ($array['half'] || $array['almost']);
            return $array;
        }
        return array();
    }

    protected function countMonths(DateInterval $di) {
        if ($di->m > 0) {
            $array['almost'] = $this->almostFullUnit($di->m, 12);

            // is it almost a year?
            if ($array['almost']) {
                $array['counter'] = 1;
                $array['half'] = false;
                $array['unit'] = 'y';
                $array['approx'] = true;
                return $array;
            }

            // is it a half year?
            if ($this->isHalfUnit($di->m, 12)) {
                $array['counter'] = 0;
                $array['half'] = false;
                $array['unit'] = 'y';
                $array['approx'] = true;
                return $array;
            }

            $array['counter'] = $di->m;
            $array['half'] = $this->isHalfUnit($di->d, 30);
            $array['unit'] = 'm';

            // is it almost a month?
            $array['almost'] = $this->almostFullUnit($di->d, 30);
            if ($array['almost']) {
                ++$array['counter'];
            }
            $array['approx'] = ($array['half'] || $array['almost']);
            return $array;
        }
        return array();
    }

    protected function countDays(DateInterval $di) {
        if ($di->d > 0) {
            $array['almost'] = $this->almostFullUnit($di->d, 30);

            // is it almost a month?
            if ($array['almost']) {
                $array['counter'] = 1;
                $array['half'] = false;
                $array['unit'] = 'm';
                $array['approx'] = true;
                return $array;
            }

            // is it a half month?
            if ($this->isHalfUnit($di->d, 30)) {
                $array['counter'] = 0;
                $array['half'] = false;
                $array['unit'] = 'm';
                $array['approx'] = true;
                return $array;
            }

            $array['counter'] = $di->d;
            $array['half'] = $this->isHalfUnit($di->h, 24);
            $array['unit'] = 'd';

            // is it almost a day?
            $array['almost'] = $this->almostFullUnit($di->h, 24);
            if ($array['almost']) {
                ++$array['counter'];
            }
            $array['approx'] = ($array['half'] || $array['almost']);
            return $array;
        }
        return array();
    }

    protected function countHours(DateInterval $di) {
        if ($di->h > 0) {
            $array['almost'] = $this->almostFullUnit($di->h, 24);

            // is it almost a day?
            if ($array['almost']) {
                $array['counter'] = 1;
                $array['half'] = false;
                $array['unit'] = 'd';
                $array['approx'] = true;
                return $array;
            }

            // is it a half of a day?
            if ($this->isHalfUnit($di->h, 24)) {
                $array['counter'] = 0;
                $array['half'] = false;
                $array['unit'] = 'd';
                $array['approx'] = true;
                return $array;
            }

            $array['counter'] = $di->h;
            $array['half'] = $this->isHalfUnit($di->i, 60);
            $array['unit'] = 'h';

            // is it almost a hour?
            $array['almost'] = $this->almostFullUnit($di->i, 60);
            if ($array['almost']) {
                ++$array['counter'];
            }
            $array['approx'] = ($array['half'] || $array['almost']);
            return $array;
        }
        return array();
    }

    protected function countMinutes(DateInterval $di) {
        if ($di->i > 0) {
            $array['almost'] = $this->almostFullUnit($di->i, 60);

            // is it almost an hour?
            if ($array['almost']) {
                $array['counter'] = 1;
                $array['half'] = false;
                $array['unit'] = 'h';
                $array['approx'] = true;
                return $array;
            }

            // is it a half of an hour?
            if ($this->isHalfUnit($di->i, 60)) {
                $array['counter'] = 0;
                $array['half'] = false;
                $array['unit'] = 'h';
                $array['approx'] = true;
                return $array;
            }

            $array['counter'] = $di->i;
            $array['half'] = $this->isHalfUnit($di->s, 60);
            $array['unit'] = 'i';

            // is it almost a hour?
            $array['almost'] = $this->almostFullUnit($di->s, 60);
            if ($array['almost']) {
                ++$array['counter'];
            }
            $array['approx'] = ($array['half'] || $array['almost']);
            return $array;
        }
        return array();
    }

    protected function countSeconds(DateInterval $di) {
        if ($di->s >= 0) {
            $array['almost'] = $this->almostFullUnit($di->s, 60);

            // is it almost a minute?
            if ($array['almost']) {
                $array['counter'] = 1;
                $array['half'] = false;
                $array['unit'] = 'i';
                $array['approx'] = true;
                return $array;
            }

            // is it a half of a minute?
            if ($this->isHalfUnit($di->s, 60)) {
                $array['counter'] = 0;
                $array['half'] = true;
                $array['unit'] = 'i';
                $array['approx'] = true;
                return $array;
            }

            $array['counter'] = ($di->s > $this->_justNow) ? $di->s : -1;
            $array['half'] = false;
            $array['unit'] = 's';
            $array['approx'] = false;
            return $array;
        }
        return array();
    }

    /**
     * Return true if actual unit is within half unit scope including tolerance.
     * @param int $actualUnit
     * @param int $fullUnit
     * @return bool 
     */
    protected function isHalfUnit($actualUnit, $fullUnit) {
        $halfUnit = floatval($fullUnit) / 2.0;
        $percentageUnit = ceil($halfUnit * (($this->_halfTolerance / 2.0) / 100.0));
        return ($actualUnit <= ($halfUnit + $percentageUnit) && $actualUnit >= ($halfUnit - $percentageUnit)) ? true : false;
    }

    /**
     * Return true if actual unit is near full unit scope including tolerance.
     * @param int $actualUnit
     * @param int $fullUnit
     * @return bool
     */
    protected function almostFullUnit($actualUnit, $fullUnit) {
        return ($actualUnit >= $fullUnit - ceil($fullUnit * ($this->_almostFullTolerance / 100.0))) ? true : false;
    }

}