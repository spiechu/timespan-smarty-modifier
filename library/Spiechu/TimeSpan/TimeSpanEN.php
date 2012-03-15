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

class TimeSpanEN extends AbstractTimeSpan {

    private $_units = array(
        -1 => array('s' => 'just now'),
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

    protected function getUnit($howMany, $unitSymbol) {
        if ($howMany > 1)
            $howMany = 2;
        return $this->_units[$howMany][$unitSymbol];
    }

    protected function getSuffix() {
        return 'ago';
    }

}