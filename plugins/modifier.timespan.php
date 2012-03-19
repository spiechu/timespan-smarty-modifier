<?php

/*
* This file is part of the TimeSpan package.
*
* (c) Dawid Spiechowicz <spiechu@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

use \DateTime,
    Spiechu\TimeSpan\AbstractTimeSpan,
    Spiechu\TimeSpan\TimeSpanEN,
    Spiechu\TimeSpan\TimeSpanException
    

/**
 * @param \DateTime|int $startDateTime \DateTime or timestamp to compute date interval
 * @param string $lang language of message; if can't find proper language - falls back on english
 * @param bool $suffix show suffix?
 * @return string 
 * @throws Spiechu\TimeSpan\TimeSpanException when can't set up date
 */
function smarty_modifier_timespan($startDateTime, $lang = 'EN', $suffix = true) {
    $className = 'Spiechu\TimeSpan\TimeSpan' . strtoupper($lang);
    if (class_exists($className)) {
        $timeSpan = new $className();
        
        // double check if class extends abstract TimeSpan class
        if (!($timeSpan instanceof AbstractTimeSpan)) {
            $timeSpan = new TimeSpanEN();
        }
    } else {
        
        // if unknown language or class doesn't extend AbstractTimeSpan, fall back to english
        $timeSpan = new TimeSpanEN();
    }

    if ($startDateTime instanceof DateTime) {
        $date = $startDateTime;
        
    // if it's int, assume it's timestamp
    } elseif (is_int($startDateTime)) {
        $date = new DateTime();
        $date->setTimestamp($startDateTime);
    } else {
        throw new TimeSpanException('Unknown startDateTime: ' . $startDateTime);
    }

    $timeSpan->setStartDate($date)->showSuffix($suffix);
    return $timeSpan->getTimeSpan();
}