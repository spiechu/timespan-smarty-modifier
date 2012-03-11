<?php

/**
 * @param DateTime|int $startDateTime DateTime or timestamp to compute date interval
 * @param string $lang language of message
 * @param bool $suffix show suffix?
 * @return string 
 */
function smarty_modifier_timespan($startDateTime, $lang = 'EN', $suffix = true) {
    if ($startDateTime instanceof DateTime) {
        $date = $startDateTime;
    } else {
        $date = new DateTime();
        $date->setTimestamp($startDateTime);
    }

    $className = 'TimeSpan' . strtoupper($lang);
    if (class_exists($className)) {
        $timeSpan = new $className();
        $timeSpan->setStartDate($date)->showSuffix($suffix);
        return $timeSpan->getTimeSpan();
    } else {
        $timeSpan = new TimeSpanEN();
        $timeSpan->setStartDate($date)->showSuffix($suffix);
        return $timeSpan->getTimeSpan();
    }
}

abstract class TimeSpan {

    /**
     * @var int number of seconds to show 'just now' instead of exact units 
     */
    protected $_justNow = 10;

    /**
     * @var bool show suffix in timespan string 
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
     * Returns translated 'ago' string.
     * 
     * @return string
     */
    abstract protected function getSuffix();

    /**
     * @param bool $suffix 
     */
    public function showSuffix($suffix) {
        $this->_showSuffix = $suffix;
        return $this;
    }

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

    protected function getInterval() {
        $curDate = new DateTime('now');
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
        }
        else
            throw new InvalidArgumentException('Invalid DateInterval');

        return array('counter' => $counter, 'unit' => $unit);
    }

}

class TimeSpanEN extends TimeSpan {

    protected function getUnit($howMany, $unitSymbol) {
        if ($howMany > 1)
            $howMany = 2;

        $units = array(
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
        return $units[$howMany][$unitSymbol];
    }

    protected function getSuffix() {
        return 'ago';
    }

}

class TimeSpanPL extends TimeSpan {

    protected function getUnit($howMany, $unitSymbol) {
        switch ($howMany) {
            case ($howMany > 21):
                $howMany = substr($howMany, -1);
                if ($howMany == 1) {
                    $howMany = 5;
                } else {
                    // we've got only last one digit now
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

        $units = array(
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
        return $units[$howMany][$unitSymbol];
    }

    protected function getSuffix() {
        return 'temu';
    }

}