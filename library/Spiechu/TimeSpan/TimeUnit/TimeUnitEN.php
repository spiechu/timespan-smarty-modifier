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

class TimeUnitEN extends AbstractTimeUnit {

    protected $_units = array(
        -1 => array('s' => 'just now'),
        0 => array('i' => 'a half minute',
            'h' => 'a half hour',
            'd' => 'a half day',
            'm' => 'a half month',
            'y' => 'a half year'),
        1 => array('s' => 'a second',
            'i' => 'a minute',
            'h' => 'an hour',
            'd' => 'a day',
            'm' => 'a month',
            'y' => 'a year'),
        2 => array('s' => 'seconds',
            'i' => 'minutes',
            'h' => 'hours',
            'd' => 'days',
            'm' => 'months',
            'y' => 'years')
    );

    public function getUnitString() {
        $howMany = $this->_unitCount;
        if ($howMany > 1) {
            $howMany = 2;
        }
        return $this->_units[$howMany][$this->_unitType];
    }

    public function getPrefix() {
        return 'about';
    }

    public function getHalf() {
        return 'and a half';
    }

    public function getConjunctionWord() {
        return 'and';
    }

    public function getSuffix() {
        return 'ago';
    }

}