<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Twig\Engine;

use LastCall\Mannequin\Core\Engine\EngineInterface;
use LastCall\Mannequin\Core\Exception\UnsupportedPatternException;
use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Rendered;
use LastCall\Mannequin\Twig\Pattern\TwigPattern;

class TwigEngine implements EngineInterface
{
    private $twig;

    public function __construct(
        \Twig_Environment $twig
    ) {
        $this->twig = $twig;
    }

    public function render(PatternInterface $pattern, array $variables = []): Rendered
    {
        if ($this->supports($pattern)) {
            $rendered = new Rendered();

            $styles = ['@global_styles'];
            $scripts = ['@global_scripts'];

            $rendered->setMarkup(
                $this->twig->render(
                    $pattern->getSource()->getName(),
                    $this->wrapRendered($variables, $styles, $scripts)
                )
            );
            $rendered->setStyles($styles);
            $rendered->setScripts($scripts);

            return $rendered;
        }
        throw new UnsupportedPatternException('Unsupported pattern.');
    }

    private function wrapRendered(array $variables, &$styles, &$scripts)
    {
        $wrapped = [];
        foreach ($variables as $key => $value) {
            if ($value instanceof Rendered) {
                $wrapped[$key] = new \Twig_Markup($value, 'UTF-8');
                $styles = array_merge($styles, $value->getStyles());
                $scripts = array_merge($scripts, $value->getScripts());
            } else {
                $wrapped[$key] = is_array($value)
                    ? $this->wrapRendered($value, $styles, $scripts)
                    : $value;
            }
        }

        return $wrapped;
    }

    public function supports(PatternInterface $pattern): bool
    {
        return $pattern instanceof TwigPattern;
    }

    public function renderSource(PatternInterface $pattern): string
    {
        if ($this->supports($pattern)) {
            return $pattern->getSource()->getCode();
        }
        throw new UnsupportedPatternException('Unsupported pattern.');
    }
}
