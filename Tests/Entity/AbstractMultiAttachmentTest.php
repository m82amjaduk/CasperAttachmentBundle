<?php
namespace Casper\AttachmentBundle\Tests\Entity;

use Casper\AttachmentBundle\Entity\AlbumEntity;
use Casper\AttachmentBundle\Entity\ImageMultiEntity;
use Casper\AttachmentBundle\Tests\BaseAttachmentTestCase;

/**
 * Class AbstractMultiAttachmentTest
 * @package Casper\AttachmentBundle\Tests\Entity
 */
class AbstractMultiAttachmentTest extends BaseAttachmentTestCase
{
    protected function repo()
    {
        return $this->getMockEntityManager()->getRepository('Casper\AttachmentBundle\Entity\AlbumEntity');
    }

    protected function createImage($name)
    {
        $image = new ImageMultiEntity();
        $image->setUploadedFile($this->createUploadedImagePng($name));

        return $image;
    }

    protected function clearUploads()
    {
        $tmp = new ImageMultiEntity();
        $dir = $tmp->getUploadRootDir();
        if (file_exists($dir)) {
            $this->rmDir($dir);
        }
    }

    public function testCreate()
    {
        $this->installTestEntities(['ImageMultiEntity', 'AlbumEntity']);
        $this->clearUploads();
        $this->assertCount(0, $this->repo()->findAll());
        $em = $this->getMockEntityManager();

        // Add first album
        $album = new AlbumEntity();
        $album->addAttachment($this->createImage('image_1'));
        $album->addAttachment($this->createImage('image_2'));

        $em->persist($album);
        $em->flush();

        $this->assertCount(1, $this->repo()->findAll());
        $this->checkImagesCollection(1, 2);

        // Add second album
        $album = new AlbumEntity();
        $album->setId(2);
        $album->addAttachment($this->createImage('image_3'));

        $em->persist($album);
        $em->flush();

        $this->assertCount(2, $this->repo()->findAll());
        $this->checkImagesCollection(2, 1);

        // Change first album
        $album = $this->repo()->find(1);
        $this->assertNotNull($album);
        $this->assertCount(2, $album->getAttachments());
        $album->getAttachments()[0]->setUploadedFile($this->createUploadedImagePng('image_4'));
        $album->addAttachment($this->createImage('image_5'));

        $em->flush();
        $this->checkImagesCollection(1, 3);
        $album = $this->repo()->find(1);
        $images = $album->getAttachments();
        $this->assertEquals('image_4.png', $images[0]->getFilename());
        $this->assertEquals('image_2.png', $images[1]->getFilename());
        $this->assertEquals('image_5.png', $images[2]->getFilename());

        $this->clearUploads();
    }

    private function checkImagesCollection($id, $count)
    {
        $item = $this->repo()->find($id);
        $this->assertCount($count, $item->getAttachments());
        $this->assertCountFiles($item->getAttachments()[0]->getUploadRootDir(), $count);
        $regex = sprintf('#uploads/images/%s/\d+\.png$#', $item->getId());
        foreach ($item->getAttachments() as $image) {
            $this->assertTrue($image->isFileExists());
            $this->assertRegExp($regex, $image->getWebPath());
            $this->assertRegExp($regex, $image->getAbsolutePath());
        }
    }
}