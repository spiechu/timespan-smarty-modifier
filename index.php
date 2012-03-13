<?php

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
    new DateTime('1 second ago'),
    new DateTime('1 minute ago'),
    new DateTime('1 hour ago'),
    new DateTime('1 day ago'),
    new DateTime('1 month ago'),
    new DateTime('1 year ago'),
    
    new DateTime('2 second ago'),
    new DateTime('2 minute ago'),
    new DateTime('2 hour ago'),
    new DateTime('2 day ago'),
    new DateTime('2 month ago'),
    new DateTime('2 year ago'),
    
    new DateTime('5 second ago'),
    new DateTime('5 minute ago'),
    new DateTime('5 hour ago'),
    new DateTime('5 day ago'),
    new DateTime('5 month ago'),
    new DateTime('5 year ago'),
    
    new DateTime('10 second ago'),
    new DateTime('10 minute ago'),
    new DateTime('10 hour ago'),
    new DateTime('10 day ago'),
    new DateTime('10 month ago'),
    new DateTime('10 year ago'),
    
    new DateTime('15 second ago'),
    new DateTime('15 minute ago'),
    new DateTime('15 hour ago'),
    new DateTime('15 day ago'),
    new DateTime('15 month ago'),
    new DateTime('15 year ago'),
    
    new DateTime('20 second ago'),
    new DateTime('20 minute ago'),
    new DateTime('20 hour ago'),
    new DateTime('20 day ago'),
    new DateTime('20 month ago'),
    new DateTime('20 year ago'),
    
    new DateTime('25 second ago'),
    new DateTime('25 minute ago'),
    new DateTime('25 hour ago'),
    new DateTime('25 day ago'),
    new DateTime('25 month ago'),
    new DateTime('25 year ago')
    );

$smarty->display('header.tpl');

$smarty->assign('dates', $dates);

$smarty->assign('lang', 'en');
$smarty->display('template.tpl');

$smarty->assign('lang', 'pl');
$smarty->display('template.tpl');