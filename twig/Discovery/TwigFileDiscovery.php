<?php


namespace LastCall\Patterns\Twig\Discovery;


use LastCall\Patterns\Core\Discovery\DiscoveryInterface;
use LastCall\Patterns\Core\Metadata\MetadataFactoryInterface;
use LastCall\Patterns\Core\Pattern\PatternCollection;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use LastCall\Patterns\Twig\Pattern\TwigPattern;

class TwigFileDiscovery implements DiscoveryInterface {

  /**
   * @var \Twig_LoaderInterface|\Twig_SourceContextLoaderInterface|\Twig_ExistsLoaderInterface
   */
  private $loader;
  private $finder;
  private $prefix = 'twig://';
  private $metadataParser;

  public function __construct(\Twig_LoaderInterface $loader, Finder $finder, MetadataFactoryInterface $metadataParser) {
    if(!$loader instanceof \Twig_SourceContextLoaderInterface) {
      throw new \InvalidArgumentException('Twig loader must implement Twig_SourceContextLoaderInterface');
    }
    if(!$loader instanceof \Twig_ExistsLoaderInterface) {
      throw new \InvalidArgumentException('Twig loader must implement Twig_ExistsLoaderInterface');
    }
    $this->loader = $loader;
    $this->finder = $finder;
    $this->metadataParser = $metadataParser;
  }

  public function setPrefix(string $prefix) {
    $this->prefix = $prefix;
  }

  public function discover(): PatternCollection {
    $patterns = [];
    foreach($this->finder->files() as $fileInfo) {
      if($pattern = $this->parseFile($fileInfo)) {
        $patterns[] = $pattern;
      }
    }
    return new PatternCollection($patterns);
  }

  public function parseFile(SplFileInfo $fileInfo) {
    if($this->loader->exists($fileInfo->getRelativePathname())) {
      $id = $this->prefix . $fileInfo->getRelativePathname();
      $source = $this->loader->getSourceContext($fileInfo->getRelativePathname());

      $pattern = new TwigPattern($id, $source);

      if($this->metadataParser->hasMetadata($pattern)) {
        $metadata = $this->metadataParser->getMetadata($pattern);
        $pattern->setName($metadata['name']);
        $pattern->setTags($metadata['tags']);
        $pattern->setVariables($metadata['variables']);
      }
      return $pattern;
    }
  }
}