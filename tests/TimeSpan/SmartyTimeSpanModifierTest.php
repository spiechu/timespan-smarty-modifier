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

class SmartyTimeSpanModifier extends \PHPUnit_Framework_TestCase {

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
    public function testJustNow($date, $expectedOutput) {
        $this->_smarty->assign('date', $date);
        $output = $this->_smarty->fetch('WithoutArgs.tpl');
        $this->assertEquals($output, $expectedOutput);
    }

    public function justNowDatesProvider() {
        return array(array(new \DateTime('1 second ago'), 'just now'),
            array(new \DateTime('2 second ago'), 'just now'),
            array(new \DateTime('5 second ago'), 'just now'),
            array(new \DateTime('8 second ago'), 'just now'),
            array(new \DateTime('10 second ago'), 'just now'));
    }

}

