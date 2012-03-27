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
    \array_push,
    Spiechu\TimeSpan\TimeUnit\AbstractTimeUnit,
    Spiechu\TimeSpan\TimeUnit\TimeUnitEN,
    Spiechu\TimeSpan\TimeSpanException;

class TimeSpan {

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
     * @var AbstractTimeUnit 
     */
    protected $_timeUnit;

    /**
     * Sets language with two letters language code.
     * 
     * @param string $lang
     * @return TimeSpan fluent interface
     */
    public function setLanguage($lang) {
        $className = 'Spiechu\TimeSpan\TimeUnit\TimeUnit' . strtoupper($lang);
        if (class_exists($className)) {
            $this->_timeUnit = new $className();

            // double check if class extends AbstractTimeUnit class
            if (!($this->_timeUnit instanceof AbstractTimeUnit)) {
                $this->_timeUnit = new TimeUnitEN();
            }
        } else {

            // if unknown language or class doesn't extend AbstractTimeUnit, fall back to english
            $this->_timeUnit = new TimeUnitEN();
        }
        return $this;
    }

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
     * @param DateTime|int $startDateTime
     * @return TimeSpan fluent interface
     * @throws TimeSpanException when $startDateTime can't be resolved
     */
    public function setStartDate($startDateTime) {
        if ($startDateTime instanceof DateTime) {
            $this->_startDate = $startDateTime;

            // if it's int, assume it's timestamp
        } elseif (is_int($startDateTime)) {
            $this->_startDate = new DateTime();
            $this->_startDate->setTimestamp($startDateTime);
        } else {
            throw new TimeSpanException('Unknown startDateTime: ' . $startDateTime);
        }
        return $this;
    }

    /**
     * Returns translated string.
     * 
     * @return string
     */
    public function getTimeSpan() {
        $intervals = $this->resolveIntervals();
        $interval1 = $intervals[0];
        $interval2 = $intervals[1];

        $timeUnit1 = $this->_timeUnit->getUnit($interval1['counter'], $interval1['unit'], $interval1['half']);

        $prefix = ($interval1['approx']) ? $this->_timeUnit->getPrefix() . ' ' : '';
        $suffix = ($this->_showSuffix) ? ' ' . $this->_timeUnit->getSuffix() : '';
        $half = ($this->_timeUnit->isSpecialUnit() == false && $interval1['half'] && $interval1['counter'] > 0) ? $this->_timeUnit->getHalf() . ' ' : '';

        $timeString = '';
        if ($interval1['counter'] > 1) {
            $timeString = $interval1['counter'] . ' ' . $half . $timeUnit1;
        } elseif ($interval1['counter'] >= 0) {

            // in case we don't have to show number of units
            $timeString = $timeUnit1 . ' ' . $half;
        } else {

            // in case of 'just now' -1 offset we don't need 'ago' suffix
            $timeString = $timeUnit1;
            $suffix = '';
        }

        if ($interval2 !== null && $interval1['half'] == false && $interval1['almost'] == false) {
            $timeString .= ' ' . $this->_timeUnit->getConjunctionWord() . ' ';
            $timeUnit2 = $this->_timeUnit->getUnit($interval2['counter'], $interval2['unit'], $interval2['half']);
            $prefix = ($interval1['approx'] || $interval2['approx']) ? $this->_timeUnit->getPrefix() . ' ' : '';

            if ($interval2['counter'] > 1) {
                $timeString .= $interval2['counter'] . ' ' . $timeUnit2;
            } else {
                $timeString .= $timeUnit2;
            }
        }
        return $prefix . $timeString . $suffix;
    }

    /**
     * Gets 2 greatest time intervals.
     * 
     * @return array
     * @throws TimeSpanException 
     */
    protected function resolveIntervals() {
        $interval1 = null;
        $interval2 = null;
        foreach ($this->getInterval() as $i) {
            if (count($i) > 0 && $interval1 == null) {
                $interval1 = $i;
                continue;
            }
            if ($interval1 != null) {
                if (count($i) == 0) {
                    break;
                } else {
                    $interval2 = $i;
                    break;
                }
            }
        }

        if ($interval1 == null) {
            throw new TimeSpanException('Unknown interval');
        }

        return array(0 => $interval1, 1 => $interval2);
    }

    /**
     * Returns unit type and unit count array.
     * 
     * @return array 
     */
    protected function getInterval() {
        $currentDate = new DateTime('now');
        $dateInterval = $currentDate->diff($this->_startDate);

        $interval = array();
        array_push($interval, $this->countYears($dateInterval), $this->countMonths($dateInterval), $this->countDays($dateInterval), $this->countHours($dateInterval), $this->countMinutes($dateInterval), $this->countSeconds($dateInterval));

        return $interval;
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
                return $this->fillFullUnitArray('y');
            }

            // is it a half year?
            if ($this->isHalfUnit($di->m, 12)) {
                return $this->fillHalfUnitArray('y');
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
                return $this->fillFullUnitArray('m');
            }

            // is it a half month?
            if ($this->isHalfUnit($di->d, 30)) {
                return $this->fillHalfUnitArray('m');
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
                return $this->fillFullUnitArray('d');
            }

            // is it a half of a day?
            if ($this->isHalfUnit($di->h, 24)) {
                return $this->fillHalfUnitArray('d');
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
                return $this->fillFullUnitArray('h');
            }

            // is it a half of an hour?
            if ($this->isHalfUnit($di->i, 60)) {
                return $this->fillHalfUnitArray('h');
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
        if ($this->isJustNow($di)) {
            $array['counter'] = -1;
            $array['half'] = false;
            $array['unit'] = 's';
            $array['approx'] = false;
            return $array;
        }

        if ($di->s > 0) {
            $array['almost'] = $this->almostFullUnit($di->s, 60);

            // is it almost a minute?
            if ($array['almost']) {
                return $this->fillFullUnitArray('i');
            }

            // is it a half of a minute?
            if ($this->isHalfUnit($di->s, 60)) {
                return $this->fillHalfUnitArray('i');
            }

            $array['counter'] = $di->s;
            $array['half'] = false;
            $array['unit'] = 's';
            $array['approx'] = false;
            return $array;
        }
        return array();
    }

    protected function fillHalfUnitArray($unitSymbol) {
        return array('counter' => 0,
            'half' => true,
            'unit' => $unitSymbol,
            'approx' => true);
    }

    protected function fillFullUnitArray($unitSymbol) {
        return array('counter' => 1,
            'half' => false,
            'unit' => $unitSymbol,
            'approx' => true);
    }

    protected function isJustNow(DateInterval $di) {
        return ($di->s <= $this->_justNow
                && $di->i == 0
                && $di->h == 0
                && $di->d == 0
                && $di->y == 0);
    }

    /**
     * Return true if actual unit is within half unit scope including tolerance.
     * 
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
     * 
     * @param int $actualUnit
     * @param int $fullUnit
     * @return bool
     */
    protected function almostFullUnit($actualUnit, $fullUnit) {
        return ($actualUnit >= $fullUnit - ceil($fullUnit * ($this->_almostFullTolerance / 100.0))) ? true : false;
    }

}