<?php
namespace FluidTYPO3\Fluidpages\Tests\Unit\Controller;

/*
 * This file is part of the FluidTYPO3/Fluidpages project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Fluidpages\Controller\PageController;
use FluidTYPO3\Fluidpages\Service\ConfigurationService;
use FluidTYPO3\Fluidpages\Service\PageService;
use FluidTYPO3\Fluidpages\Tests\Fixtures\Controller\DummyPageController;
use FluidTYPO3\Fluidpages\Tests\Unit\AbstractTestCase;
use FluidTYPO3\Flux\Provider\Provider;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\TemplateView;

/**
 * Class PageControllerTest
 */
class PageControllerTest extends AbstractTestCase
{

    /**
     * @return void
     */
    public function testPerformsInjections()
    {
        $instance = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager')
            ->get('FluidTYPO3\\Fluidpages\\Controller\\PageController');
        $this->assertAttributeInstanceOf('FluidTYPO3\\Fluidpages\\Service\\PageService', 'pageService', $instance);
        $this->assertAttributeInstanceOf('FluidTYPO3\\Fluidpages\\Service\\ConfigurationService', 'pageConfigurationService', $instance);
    }

    /**
     * @return void
     */
    public function testGetRecordReadsFromTypoScriptFrontendController()
    {
        $GLOBALS['TSFE'] = (object) ['page' => ['foo' => 'bar']];
        /** @var PageController $subject */
        $subject = $this->getMockBuilder('FluidTYPO3\\Fluidpages\\Controller\\PageController')->setMethods(array('dummy'))->getMock();
        $record = $subject->getRecord();
        $this->assertSame(['foo' => 'bar'], $record);
    }

    public function testInitializeProvider()
    {
        /** @var ConfigurationService|\PHPUnit_Framework_MockObject_MockObject $pageConfigurationService */
        $pageConfigurationService = $this->getMockBuilder(
            'FluidTYPO3\\Fluidpages\\Service\\ConfigurationService'
        )->setMethods(
            array(
                'resolvePrimaryConfigurationProvider',
            )
        )->getMock();
        /** @var PageService $pageService */
        $pageService = $this->getMockBuilder(
            'FluidTYPO3\\Fluidpages\\Service\\PageService'
        )->setMethods(
            array(
                'getPageTemplateConfiguration'
            )
        )->getMock();
        $pageConfigurationService->expects($this->once())->method('resolvePrimaryConfigurationProvider');
        /** @var PageController|\PHPUnit_Framework_MockObject_MockObject $instance */
        $instance = $this->getMockBuilder('FluidTYPO3\\Fluidpages\\Controller\\PageController')->setMethods(array('getRecord'))->getMock();
        $instance->expects($this->once())->method('getRecord')->willReturn(array());
        $instance->injectpageConfigurationService($pageConfigurationService);
        $instance->injectPageService($pageService);
        $this->callInaccessibleMethod($instance, 'initializeProvider');
    }
}
