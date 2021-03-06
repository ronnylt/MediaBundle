<?php
/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\MediaBundle\Provider;

use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\AdminBundle\Form\FormMapper;

interface MediaProviderInterface
{

    /**
     * @param string $name
     * @param array $format
     *
     * @return void
     */
    function addFormat($name, $format);

    /**
     * return the format settings
     *
     * @param string $name
     *
     * @return array|false the format settings
     */
    function getFormat($name);

    /**
     * return true if the media related to the provider required thumbnails (generation)
     *
     * @return boolean
     */
    function requireThumbnails();

    /**
     * generated thumbnails linked to the media, a thumbnail is a format used on the website
     *
     * @return void
     */
    function generateThumbnails(MediaInterface $media);

    /**
     *
     * @return \Gaufrette\Filesystem\File
     */
    function getReferenceFile(MediaInterface $media);

    /**
     * return the correct format name : providerName_format
     *
     * @param \Sonata\MediaBundle\Model\MediaInterface $media
     * @param string $format
     * @return string
     */
    function getFormatName(MediaInterface $media, $format);

    /**
     * return the reference image of the media, can be the video thumbnail or the original uploaded picture
     *
     * @abstract
     * @return string to the reference image
     */
    function getReferenceImage(MediaInterface $media);

    /**
     *
     * @abstract
     * @param  $media
     * @return void
     */
    function postUpdate(MediaInterface $media);

    /**
     * @param \Sonata\MediaBundle\Model\MediaInterface $media
     * @return void
     */
    function postRemove(MediaInterface $media);

    /**
     * build the related create form
     *
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     */
    function buildCreateForm(FormMapper $formMapper);

    /**
     * build the related create form
     *
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     */
    function buildEditForm(FormMapper $formMapper);

    /**
     *
     * @abstract
     * @param  $media
     * @return void
     */
    function postPersist(MediaInterface $media);

    /**
     * @param \Sonata\MediaBundle\Model\MediaInterface $media
     * @param string $format
     */
    function getHelperProperties(MediaInterface $media, $format);

    /**
     * Generate the media path
     *
     * @param \Sonata\MediaBundle\Model\MediaInterface $media
     * @return string
     */
    function generatePath(MediaInterface $media);

    /**
     * Generate the public path
     *
     * @param \Sonata\MediaBundle\Model\MediaInterface $media
     * @param string $format
     * @return string
     */
    function generatePublicUrl(MediaInterface $media, $format);

    /**
     * Generate the private path
     *
     * @param \Sonata\MediaBundle\Model\MediaInterface $media
     * @param string $format
     * @return string
     */
    function generatePrivateUrl(MediaInterface $media, $format);

    /**
     *
     * @return array
     */
    function getFormats();

    /**
     *
     * @param string $name
     */
    function setName($name);

    /**
     * @return string
     */
    function getName();

    /**
     *
     * @param array $templates
     */
    function setTemplates(array $templates);
    /**
     *
     * @return array
     */
    function getTemplates();

    /**
     * @param string $name
     * @return void
     */
    function getTemplate($name);
}