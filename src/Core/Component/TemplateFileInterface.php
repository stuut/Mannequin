<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Component;

/**
 * Denotes a component that may be sourced from a file.
 *
 * NB: This does not necessarily mean that the template comes from a file,
 * just that it is of a type that may.  One example of this is Twig components,
 * which often come from a file, but may also come from Twig_Loader_Array.
 */
interface TemplateFileInterface extends ComponentInterface
{
    /**
     * Get the path to the source template.
     *
     * @return false|\SplFileInfo
     */
    public function getFile();
}
