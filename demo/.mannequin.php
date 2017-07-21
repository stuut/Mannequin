<?php

use LastCall\Mannequin\Core\MannequinConfig;
use LastCall\Mannequin\Twig\TwigExtension;
use LastCall\Mannequin\Html\HtmlExtension;
use Symfony\Component\Finder\Finder;

/**
 * This is a Mannequin configuration file.
 *
 * It defines what Mannequin should do.  This one tells the system to use the
 * Twig extension to look for patterns in the templates directory.
 */

/**
 * Create a finder to search and list the template files for the TwigExtension.
 */
$twigFinder = Finder::create()
    ->files()
    ->in(__DIR__.'/templates')
    ->name('*.twig');

/**
 * Create the TwigExtension object.
 */
$twigExtension = new TwigExtension([
    'finder' => $twigFinder,
    'twig_root' => __DIR__.'/templates',
]);

/**
 * Create a finder to search and list the static HTML files.
 */
$htmlFinder = Finder::create()
    ->files()
    ->in(__DIR__.'/static')
    ->name('*.html');

$htmlExtension = new HtmlExtension([
    'finder' => $htmlFinder
]);

/**
 * Create and return the configuration.  Don't forget to return it!
 */
return MannequinConfig::create([
        'styles' => ['https://cdnjs.cloudflare.com/ajax/libs/foundation/6.4.1/css/foundation.css'],
        'scripts' => ['https://cdnjs.cloudflare.com/ajax/libs/foundation/6.4.1/js/foundation.min.js'],
    ])
    ->addExtension($htmlExtension)
    ->addExtension($twigExtension);