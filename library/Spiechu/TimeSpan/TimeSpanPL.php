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

class TimeSpanPL extends AbstractTimeSpan {

    private $_units = array(
        -1 => array('s' => 'przed chwilą'),
        0 => array('i' => 'pół minuty',
            'h' => 'pół godziny',
            'd' => 'pół doby',
            'm' => 'pół miesiąca',
            'y' => 'pół roku'),
        1 => array('s' => 'sekundę',
            'i' => 'minutę',
            'h' => 'godzinę',
            'd' => 'dobę',
            'm' => 'miesiąc',
            'y' => 'rok'),
        2 => array('s' => 'sekundy',
            'i' => 'minuty',
            'h' => 'godziny',
            'd' => 'dni',
            'm' => 'miesiące',
            'y' => 'lata'),
        5 => array('s' => 'sekund',
            'i' => 'minut',
            'h' => 'godzin',
            'd' => 'dni',
            'm' => 'miesięcy',
            'y' => 'lat')
    );
    
    private $_specialUnits = array(
        'poltora' => array('s' => 'półtorej sekundy', // currently not used
            'i' => 'półtorej minuty',
            'h' => 'półtorej godziny',
            'd' => 'półtora dnia',
            'm' => 'półtora miesiąca',
            'y' => 'półtora roku')
    );

    protected function getUnit($howMany, $unitSymbol, $half) {
        if ($howMany > 21) {
            $howMany = substr($howMany, -1);
            if ($howMany <= 1) {
                $howMany = 5;
            } else {

                // we've got only one last digit now
                return $this->getUnit($howMany, $unitSymbol, $half);
            }
        } elseif ($howMany >= 5) {
            $howMany = 5;
        } elseif ($howMany >= 2) {
            $howMany = 2;
        } elseif ($howMany == 1 && $half == true) {
            return $this->_specialUnits['poltora'][$unitSymbol];
        }
        return $this->_units[$howMany][$unitSymbol];
    }

    protected function getPrefix() {
        return 'około';
    }

    protected function getHalf() {
        return 'i pół';
    }
    
    protected function getConjunctionWord() {
        return 'i';
    }

    protected function getSuffix() {
        return 'temu';
    }

}