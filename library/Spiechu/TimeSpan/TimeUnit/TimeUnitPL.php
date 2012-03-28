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

class TimeUnitPL extends AbstractTimeUnit {

    protected $_units = array(
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
    protected $_specialUnits = array(
        'poltora' => array('s' => 'półtorej sekundy', // currently not used
            'i' => 'półtorej minuty',
            'h' => 'półtorej godziny',
            'd' => 'półtora dnia',
            'm' => 'półtora miesiąca',
            'y' => 'półtora roku')
    );
    
    /**
     * @return bool 
     */
    public function isSpecialUnit() {
        $this->getUnitString();
        return $this->_isSpecialUnit;
    }

    public function getUnitString() {
        $howMany = $this->_unitCount;
        dontKillMeForThis:
        if ($howMany > 21) {
            $howMany = substr($howMany, -1);
            if ($howMany <= 1) {
                $howMany = 5;
            } else {

                // we've got only one last digit now
                goto dontKillMeForThis;
            }
        } elseif ($howMany >= 5) {
            $howMany = 5;
        } elseif ($howMany >= 2) {
            $howMany = 2;
        } elseif ($howMany == 1 && $this->_isHalved) {
            $this->_isSpecialUnit = true;
            return $this->_specialUnits['poltora'][$this->_unitType];
        }
        return $this->_units[$howMany][$this->_unitType];
    }

    public function getPrefix() {
        return 'około';
    }

    public function getHalf() {
        return 'i pół';
    }

    public function getConjunctionWord() {
        return 'i';
    }

    public function getSuffix() {
        return 'temu';
    }

}