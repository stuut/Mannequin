<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Tests\Extension;

use LastCall\Mannequin\Core\Engine\BrokenEngine;
use LastCall\Mannequin\Core\Extension\CoreExtension;
use LastCall\Mannequin\Core\Extension\ExtensionInterface;
use LastCall\Mannequin\Core\Subscriber\GlobalAssetSubscriber;
use LastCall\Mannequin\Core\Subscriber\LastChanceNameSubscriber;
use LastCall\Mannequin\Core\Subscriber\VariableResolverSubscriber;
use LastCall\Mannequin\Core\Subscriber\YamlFileMetadataSubscriber;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CoreExtensionTest extends ExtensionTestCase
{
    public function getExtension(): ExtensionInterface
    {
        return new CoreExtension();
    }

    public function testGetEngines()
    {
        $engines = parent::testGetEngines();
        $this->assertCount(1, $engines);
        $this->assertInstanceOf(BrokenEngine::class, reset($engines));
    }

    public function testGetFunctions()
    {
        $functions = parent::testGetFunctions();
        $names = array_map(function ($fn) {
            return $fn->getName();
        }, $functions);
        $this->assertEquals([
            'rendered',
            'asset',
            'sample',
        ], $names);
    }

    protected function getDispatcherProphecy(): ObjectProphecy
    {
        $dispatcher = $this->prophesize(EventDispatcherInterface::class);
        $dispatcher->addSubscriber(
            Argument::type(YamlFileMetadataSubscriber::class)
        )->shouldBeCalled();
        $dispatcher->addSubscriber(
            Argument::type(LastChanceNameSubscriber::class)
        )->shouldBeCalled();

        $dispatcher->addSubscriber(
            Argument::type(VariableResolverSubscriber::class)
        )->shouldBeCalled();

        $dispatcher->addSubscriber(
            Argument::type(GlobalAssetSubscriber::class)
        )->shouldBeCalled();

        return $dispatcher;
    }
}
