<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\MediaBundle\Tests\Provider;

use Sonata\MediaBundle\Tests\Entity\Media;
use Sonata\MediaBundle\Model\MediaInterface;

class DailyMotionProviderTest extends \PHPUnit_Framework_TestCase
{

    public function getProvider()
    {
        $resizer = $this->getMock('Sonata\MediaBundle\Media\ResizerInterface', array('resize'));
        $resizer->expects($this->any())
            ->method('resize')
            ->will($this->returnValue(true));

        $adapter = $this->getMock('Gaufrette\Filesystem\Adapter');

        $file = $this->getMock('Gaufrette\Filesystem\File', array(), array($adapter));

        $filesystem = $this->getMock('Gaufrette\Filesystem\Filesystem', array('get'), array($adapter));
        $filesystem->expects($this->any())
            ->method('get')
            ->will($this->returnValue($file));

        $cdn = new \Sonata\MediaBundle\CDN\Server('/updoads/media');

        $generator = new \Sonata\MediaBundle\Generator\DefaultGenerator();

        $provider = new \Sonata\MediaBundle\Provider\DailyMotionProvider('file', $filesystem, $cdn, $generator);
        $provider->setResizer($resizer);

        return $provider;
    }

    public function testProvider()
    {
        $provider = $this->getProvider();

        $media = new Media;
        $media->setName('les tests fonctionnels - Symfony Live 2009');
        $media->setProviderName('dailymotion');
        $media->setProviderReference('x9wjql');
        $media->setProviderMetadata(json_decode('{"type":"video","version":"1.0","provider_name":"Dailymotion","provider_url":"http:\/\/www.dailymotion.com","title":"Thomas Rabaix - les tests fonctionnels - Symfony Live 2009","author_name":"Guillaume Pon\u00e7on","author_url":"http:\/\/www.dailymotion.com\/phptv","width":480,"height":270,"html":"<iframe src=\"http:\/\/www.dailymotion.com\/embed\/video\/x9wjql\" width=\"480\" height=\"270\" frameborder=\"0\"><\/iframe>","thumbnail_url":"http:\/\/ak2.static.dailymotion.com\/static\/video\/711\/536\/16635117:jpeg_preview_large.jpg?20100801072241","thumbnail_width":426.666666667,"thumbnail_height":240}', true));

        $this->assertEquals('http://ak2.static.dailymotion.com/static/video/711/536/16635117:jpeg_preview_large.jpg?20100801072241', $provider->getReferenceImage($media));

        $media->setId(1023458);

        $this->assertEquals('default/0011/24', $provider->generatePath($media));
        $this->assertEquals('/updoads/media/default/0011/24/thumb_1023458_big.jpg', $provider->generatePublicUrl($media, 'big'));

    }

    public function testThumbnail()
    {
        $provider = $this->getProvider();

        $media = new Media;
        $media->setName('les tests fonctionnels - Symfony Live 2009');
        $media->setProviderName('dailymotion');
        $media->setProviderReference('x9wjql');
        $media->setProviderMetadata(json_decode('{"type":"video","version":"1.0","provider_name":"Dailymotion","provider_url":"http:\/\/www.dailymotion.com","title":"Thomas Rabaix - les tests fonctionnels - Symfony Live 2009","author_name":"Guillaume Pon\u00e7on","author_url":"http:\/\/www.dailymotion.com\/phptv","width":480,"height":270,"html":"<iframe src=\"http:\/\/www.dailymotion.com\/embed\/video\/x9wjql\" width=\"480\" height=\"270\" frameborder=\"0\"><\/iframe>","thumbnail_url":"http:\/\/ak2.static.dailymotion.com\/static\/video\/711\/536\/16635117:jpeg_preview_large.jpg?20100801072241","thumbnail_width":426.666666667,"thumbnail_height":240}', true));

        $media->setId(1023458);

        $this->assertTrue($provider->requireThumbnails($media));

        $provider->addFormat('big', array('width' => 200, 'height' => null, 'constraint' => true));

        $this->assertNotEmpty($provider->getFormats(), '::getFormats() return an array');

        $provider->generateThumbnails($media);

        $this->assertEquals('default/0011/24/thumb_1023458_big.jpg', $provider->generatePrivateUrl($media, 'big'));
    }

    public function testEvent()
    {
        $provider = $this->getProvider();
        $provider->addFormat('big', array('width' => 200, 'height' => null, 'constraint' => true));

        $media = new Media;
        $media->setBinaryContent('x9wjql');
        $media->setId(1023456);

        stream_wrapper_unregister('http');
        stream_wrapper_register('http', 'Sonata\\MediaBundle\\Tests\\Provider\\FakeHttpWrapper');

        // pre persist the media
        $provider->prePersist($media);

        $this->assertEquals('Thomas Rabaix - les tests fonctionnels - Symfony Live 2009', $media->getName(), '::getName() return the file name');
        $this->assertEquals('x9wjql', $media->getProviderReference(), '::getProviderReference() is set');

        // post persit the media
        $provider->postPersist($media);

        $provider->postRemove($media);

        $media->setProviderStatus('fake');
        $provider->preUpdate($media);

        $this->assertEquals(MediaInterface::STATUS_OK, $media->getProviderStatus());
        $provider->postUpdate($media);

        $media->setProviderStatus('fake');
        $media->setBinaryContent(null);

        $provider->prePersist($media);
        $this->assertEquals('fake', $media->getProviderStatus());

        $provider->preUpdate($media);
        $this->assertEquals('fake', $media->getProviderStatus());

        $provider->postPersist($media);
        $this->assertEquals('fake', $media->getProviderStatus());

        $provider->preRemove($media);

        stream_wrapper_restore('http');
    }

    public function testForm()
    {
        $provider = $this->getProvider();

        $formMapper = $this->getMock('Sonata\AdminBundle\Form\FormMapper', array('add'), array(), '', false);
        $formMapper->expects($this->exactly(8))
            ->method('add')
            ->will($this->returnValue(null));


        $provider->buildCreateForm($formMapper);

        $provider->buildEditForm($formMapper);
    }

    public function testHelperProperies()
    {
        $provider = $this->getProvider();

        $provider->addFormat('admin', array('width' => 100));
        $media = new Media;
        $media->setName('Les tests');
        $media->setProviderReference('ASDASDAS.png');
        $media->setId(10);
        $media->setHeight(100);
        $media->setWidth(100);


        $properties = $provider->getHelperProperties($media, 'admin');

        $this->assertInternalType('array', $properties);
        $this->assertEquals(100, $properties['height']);
        $this->assertEquals(100, $properties['width']);

        $properties = $provider->getHelperProperties($media, 'admin', array('width' => 150));
        $this->assertEquals(150, $properties['width']);
    }
}