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
     * @var AbstractTimeUnit greater part of interval
     */
    protected $_timeUnit1 = null;

    /**
     * @var AbstractTimeUnit lesser part of interval
     */
    protected $_timeUnit2 = null;
    protected $_localizedTimeUnitClassName = 'Spiechu\TimeSpan\TimeUnit\TimeUnitEN';

    /**
     * Sets language with two letters language code.
     * 
     * @param string $lang
     * @return TimeSpan fluent interface
     */
    public function setLanguage($lang) {
        $className = 'Spiechu\TimeSpan\TimeUnit\TimeUnit' . strtoupper($lang);
        if (class_exists($className)) {
            $this->_localizedTimeUnitClassName = $className;
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
        $this->resolveIntervals();
        $prefix = ($this->_timeUnit1->isApproximated()) ? $this->_timeUnit1->getPrefix() . ' ' : '';
        $suffix = ($this->_showSuffix) ? ' ' . $this->_timeUnit1->getSuffix() : '';
        $half = ($this->_timeUnit1->isSpecialUnit() == false && $this->_timeUnit1->isHalved() && $this->_timeUnit1->getUnitCount() > 0) ? $this->_timeUnit1->getHalf() . ' ' : '';
        
        $timeString = '';
        if ($this->_timeUnit1->getUnitCount() > 1) {
            $timeString = $this->_timeUnit1->getUnitCount() . ' ' . $half . $this->_timeUnit1->getUnit();
        } elseif ($this->_timeUnit1->getUnitCount() >= 0) {

            // in case we don't have to show number of units
            $timeString = $this->_timeUnit1->getUnit() . ' ' . $half;
        } else {

            // in case of 'just now' -1 offset we don't need 'ago' suffix
            $timeString = $this->_timeUnit1->getUnit();
            $suffix = '';
        }

        if ($this->_timeUnit2 !== null && $this->_timeUnit1->isHalved() == false && $this->_timeUnit1->isTrulyFullUnit()) {
            $timeString .= ' ' . $this->_timeUnit1->getConjunctionWord() . ' ';
            $prefix = ($this->_timeUnit1->isApproximated() || $this->_timeUnit2->isApproximated()) ? $this->_timeUnit1->getPrefix() . ' ' : '';

            if ($this->_timeUnit2->getUnitCount() > 1) {
                $timeString .= $this->_timeUnit2->getUnitCount() . ' ' . $this->_timeUnit2->getUnit();
            } else {
                $timeString .= $this->_timeUnit2->getUnit();
            }
        }
        return $prefix . $timeString . $suffix;
    }

    /**
     * Sets 2 greatest time intervals.
     * 
     * @throws TimeSpanException 
     */
    protected function resolveIntervals() {
        $this->_timeUnit1 = null;
        $this->_timeUnit2 = null;
        foreach ($this->getInterval() as $i) {
            if (count($i) > 0 && $this->_timeUnit1 == null) {
                $this->_timeUnit1 = new $this->_localizedTimeUnitClassName();
                $this->_timeUnit1
                        ->setTrulyFullUnit($i['almost'] ? false : true)
                        ->setHalved($i['half'])
                        ->setUnitCount($i['counter'])
                        ->setUnitType($i['unit']);
                continue;
            }
            if ($this->_timeUnit1 != null) {
                if (count($i) == 0) {
                    break;
                } else {
                    $this->_timeUnit2 = new $this->_localizedTimeUnitClassName();
                    $this->_timeUnit2
                            ->setTrulyFullUnit($i['almost'] ? false : true)
                            ->setHalved($i['half'])
                            ->setUnitCount($i['counter'])
                            ->setUnitType($i['unit']);
                    break;
                }
            }
        }

        if ($this->_timeUnit1 == null) {
            throw new TimeSpanException('Unknown interval');
        }
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
            return $array;
        }
        return array();
    }

    protected function countSeconds(DateInterval $di) {
        if ($this->isJustNow($di)) {
            $array['counter'] = -1;
            $array['half'] = false;
            $array['unit'] = 's';
            $array['almost'] = false;
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
            return $array;
        }
        return array();
    }

    /**
     * Fills array needed to construct timespan string.
     * 
     * @param string $unitSymbol 's,i,h,d,m,y'
     * @return array 
     */
    protected function fillHalfUnitArray($unitSymbol) {
        return array('counter' => 0,
            'half' => true,
            'unit' => $unitSymbol,
            'almost' => false);
    }

    /**
     * Fills array needed to construct timespan string.
     * 
     * @param string $unitSymbol 's,i,h,d,m,y'
     * @return array 
     */
    protected function fillFullUnitArray($unitSymbol) {
        return array('counter' => 1,
            'half' => false,
            'unit' => $unitSymbol,
            'almost' => true);
    }

    /**
     * @param DateInterval $di
     * @return bool
     */
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