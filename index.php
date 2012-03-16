<?php

/*
* This file is part of the TimeSpan package.
*
* (c) Dawid Spiechowicz <spiechu@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);

require_once 'SplClassLoader.php';
$classLoader = new SplClassLoader('Spiechu\TimeSpan' , 'library');
$classLoader->register();

require_once 'Smarty/libs/Smarty.class.php';
$smarty = new Smarty();

$smarty->setTemplateDir('templates');
$smarty->setCompileDir('temp_c');
$smarty->addPluginsDir('plugins');

$dates = array(
    new DateTime('5 second ago'),
    new DateTime('10 second ago'),
    new DateTime('15 second ago'),
    new DateTime('20 second ago'),
    new DateTime('22 second ago'),
    new DateTime('13 second ago'),
    new DateTime('15 second ago'),
    new DateTime('30 second ago'),
    new DateTime('34 second ago'),
    new DateTime('40 second ago'),
    new DateTime('50 second ago'),
    new DateTime('55 second ago'),
    
    new DateTime('1 minute ago'),
    new DateTime('10 minute ago'),
    new DateTime('20 minute ago'),
    new DateTime('25 minute ago'),
    new DateTime('28 minute ago'),
    new DateTime('45 minute ago'),
    new DateTime('43 minute ago'),
    new DateTime('55 minute ago'),
    
    new DateTime('1 hour ago'),
    new DateTime('8 hour ago'),
    new DateTime('11 hour ago'),
    new DateTime('15 hour ago'),
    new DateTime('20 hour ago'),
    new DateTime('23 hour ago'),
    
    new DateTime('1 day ago'),
    new DateTime('5 day ago'),
    new DateTime('14 day ago'),
    new DateTime('24 day ago'),
    new DateTime('28 day ago'),
    
    new DateTime('1 month ago'),
    new DateTime('3 month ago'),
    new DateTime('5 month ago'),
    new DateTime('7 month ago'),
    new DateTime('9 month ago'),
    new DateTime('11 month ago'),
    
    new DateTime('1 year ago'),
    new DateTime('2 year ago'),
    new DateTime('5 year ago'),
    new DateTime('10 year ago'),
    new DateTime('25 year ago')
    );

$smarty->display('header.tpl');

$smarty->assign('dates', $dates);

$smarty->assign('lang', 'en');
$smarty->display('template.tpl');

$smarty->assign('lang', 'pl');
$smarty->display('template.tpl');