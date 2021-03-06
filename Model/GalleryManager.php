<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\MediaBundle\Model;

use Sonata\MediaBundle\Provider\Pool;

abstract class GalleryManager implements GalleryManagerInterface
{

    /**
     * Creates an empty gallery instance
     *
     * @return Gallery
     */
    function createGallery()
    {
        $class = $this->getClass();

        return new $class;
    }


}