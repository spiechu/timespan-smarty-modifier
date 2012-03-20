<?php

/*
 * This file is part of the TimeSpan package.
 *
 * (c) Dawid Spiechowicz <spiechu@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spiechu\Tests\TimeSpan;

use \DateTime,
    \PHPUnit_Framework_TestCase,
    \Smarty,
    Spiechu\TimeSpan\TimeSpanException;

class SmartyTimeSpanModifierTest extends PHPUnit_Framework_TestCase {

    protected $_smarty;

    protected function setUp() {
        require_once __DIR__ . '/../../Smarty/libs/Smarty.class.php';
        $this->_smarty = new Smarty();
        $this->_smarty->setTemplateDir(__DIR__ . '/TestTemplates');
        $this->_smarty->setCompileDir(__DIR__ . '/../../temp_c');
        $this->_smarty->addPluginsDir(__DIR__ . '/../../plugins');
    }

    /**
     * @dataProvider justNowDatesProvider
     */
    public function testJustNowEN($date) {
        $this->_smarty->assign('date', $date);
        $output = $this->_smarty->fetch('WithoutArgs.tpl');
        $this->assertEquals($output, 'just now');
    }

    /**
     * @dataProvider justNowDatesProvider
     */
    public function testJustNowPL($date) {
        $this->_smarty->assign('date', $date);
        $this->_smarty->assign('lang', 'pl');
        $output = $this->_smarty->fetch('WithLangAttr.tpl');
        $this->assertEquals($output, 'przed chwilą');
    }

    public function justNowDatesProvider() {
        return array(array(new DateTime('now')),
            array(new DateTime('1 second ago')),
            array(new DateTime('2 second ago')),
            array(new DateTime('5 second ago')),
            array(new DateTime('8 second ago')),
            array(new DateTime('10 second ago')),
            array(time()),
            array(time() - 1),
            array(time() - 2),
            array(time() - 5),
            array(time() - 8),
            array(time() - 10));
    }

    public function testAutoSwitchToEN() {
        $this->_smarty->assign('lang', 'pln');
        $this->_smarty->assign('date', time());
        $output = $this->_smarty->fetch('WithLangAttr.tpl');
        $this->assertEquals($output, 'just now');
    }

    /**
     * @expectedException Spiechu\TimeSpan\TimeSpanException
     * @expectedExceptionMessage Unknown startDateTime: malformed date
     */
    public function testMalformedDate() {
        $this->_smarty->assign('date', 'malformed date');
        $this->_smarty->fetch('WithoutArgs.tpl');
    }

    /**
     * @dataProvider secondsDatesProvider
     */
    public function testExactSecondsEN($date, $expectedOutput) {
        $this->_smarty->assign('date', $date);
        $output = $this->_smarty->fetch('WithoutArgs.tpl');
        $this->assertEquals($output, $expectedOutput . ' seconds ago');
    }

    /**
     * @dataProvider secondsDatesProvider
     */
    public function testExactSecondsPL($date, $expectedOutput) {
        $this->_smarty->assign('date', $date);
        $this->_smarty->assign('lang', 'pl');
        $output = $this->_smarty->fetch('WithLangAttr.tpl');
        $this->assertEquals($output, $expectedOutput . ' sekund temu');
    }

    public function secondsDatesProvider() {
        return array(array(new DateTime('11 second ago'), 11),
            array(new DateTime('15 second ago'), 15),
            array(new DateTime('20 second ago'), 20),
            array(new DateTime('25 second ago'), 25),
            array(time() - 11, 11),
            array(time() - 15, 15),
            array(time() - 20, 20),
            array(time() - 25, 25));
    }

}