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

class SmartyTimeSpanModifierTest extends \PHPUnit_Framework_TestCase {

    protected $_smarty;

    protected function setUp() {
        require_once __DIR__ . '/../../Smarty/libs/Smarty.class.php';
        $this->_smarty = new \Smarty();
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
        return array(array(new \DateTime('1 second ago')),
            array(new \DateTime('2 second ago')),
            array(new \DateTime('5 second ago')),
            array(new \DateTime('8 second ago')),
            array(new \DateTime('10 second ago')));
    }

}

