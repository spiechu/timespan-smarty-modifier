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

class TimeSpanPL extends TimeSpan {

    private $_units = array(
        -1 => array('s' => 'przed chwilą'),
        1 => array('s' => 'sekundę',
            'i' => 'minutę',
            'h' => 'godzinę',
            'd' => 'dzień',
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

    protected function getUnit($howMany, $unitSymbol) {
        switch ($howMany) {
            case ($howMany > 21):
                $howMany = substr($howMany, -1);
                if ($howMany == 1) {
                    $howMany = 5;
                } else {

                    // we've got only one last digit now
                    return $this->getUnit($howMany, $unitSymbol);
                }
                break;
            case ($howMany >= 5):
                $howMany = 5;
                break;
            case ($howMany >= 2):
                $howMany = 2;
                break;
        }
        return $this->_units[$howMany][$unitSymbol];
    }

    protected function getSuffix() {
        return 'temu';
    }

}