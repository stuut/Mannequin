<?php


namespace LastCall\Mannequin\Html\Tests\Pattern;


use LastCall\Mannequin\Html\Pattern\HtmlPattern;
use PHPUnit\Framework\TestCase;

class HtmlPatternTest extends TestCase {

  public function testConstruction() {
    $fileInfo = new \SplFileInfo('baz');
    $pattern = new HtmlPattern('foo', 'bar', $fileInfo);
    $this->assertEquals('foo', $pattern->getId());
    $this->assertEquals('bar', $pattern->getName());
    $this->assertEquals($fileInfo, $pattern->getFileInfo());
  }

  public function testIsTaggable() {
    $pattern = new HtmlPattern('foo', 'bar', new \SplFileInfo('baz'));
    $pattern->addTag('type', 'atom');
    $this->assertTrue($pattern->hasTag('type', 'atom'));
    $this->assertEquals(['type' => 'atom'], $pattern->getTags());
  }
}