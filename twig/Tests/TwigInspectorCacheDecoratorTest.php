<?php


namespace LastCall\Mannequin\Twig\Tests;


use LastCall\Mannequin\Twig\TwigInspectorCacheDecorator;
use LastCall\Mannequin\Twig\TwigInspectorInterface;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class TwigInspectorCacheDecoratorTest extends TestCase {

  public function testReturnsFromCache() {
    $source = new \Twig_Source('foo', '', '');
    $inspector = $this->prophesize(TwigInspectorInterface::class);
    $inspector->inspectLinked($source)->shouldNotBeCalled();

    $item = $this->prophesize(CacheItemInterface::class);
    $item->isHit()->willReturn(TRUE);
    $item->get()->willReturn(['bar']);
    $cache = $this->prophesize(CacheItemPoolInterface::class);
    $cache->getItem('acbd18db4cc2f85cedef654fccc4a4d8')->willReturn($item);


    $inspector = new TwigInspectorCacheDecorator($inspector->reveal(), $cache->reveal());
    $this->assertEquals(['bar'], $inspector->inspectLinked($source));
  }

  public function testDelegatesToDecoratedWhenCacheMiss() {
    $source = new \Twig_Source('foo', '', '');
    $inspector = $this->prophesize(TwigInspectorInterface::class);
    $inspector->inspectLinked($source)
      ->willReturn(['bar'])
      ->shouldBeCalled();

    $item = $this->prophesize(CacheItemInterface::class);
    $item->isHit()->willReturn(FALSE);
    $item->set(['bar'])->will(function($args) {
      $this->get()->willReturn($args[0]);
    });

    $cache = $this->prophesize(CacheItemPoolInterface::class);
    $cache->getItem('acbd18db4cc2f85cedef654fccc4a4d8')->willReturn($item);
    $cache->save($item)->shouldBeCalled();


    $inspector = new TwigInspectorCacheDecorator($inspector->reveal(), $cache->reveal());
    $this->assertEquals(['bar'], $inspector->inspectLinked($source));
  }
}