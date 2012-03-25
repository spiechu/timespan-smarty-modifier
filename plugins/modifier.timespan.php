<?php

/*
 * This file is part of the TimeSpan package.
 *
 * (c) Dawid Spiechowicz <spiechu@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Spiechu\TimeSpan\TimeSpan;

/**
 * @param \DateTime|int $startDateTime \DateTime or timestamp to compute date interval
 * @param string $lang language of message; if can't find proper language - falls back on english
 * @param bool $suffix show suffix?
 * @return string 
 */
function smarty_modifier_timespan($startDateTime, $lang = 'EN', $suffix = true) {
    $timeSpan = new TimeSpan();
    $timeSpan->setStartDate($startDateTime)
             ->setLanguage($lang)
             ->showSuffix($suffix);
    return $timeSpan->getTimeSpan();
}