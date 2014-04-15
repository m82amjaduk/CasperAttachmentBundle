<?php
namespace Casper\AttachmentBundle\Tests\Entity;

use Casper\AttachmentBundle\Entity\ImageSingleEntity;
use Casper\AttachmentBundle\Tests\BaseAttachmentTestCase;

/**
 * Class AbstractSingleAttachmentTest
 * @package Casper\AttachmentBundle\Tests\Entity
 */
class AbstractSingleAttachmentTest extends BaseAttachmentTestCase
{
    protected function repo()
    {
        return $this->getMockEntityManager()->getRepository('Casper\AttachmentBundle\Entity\ImageSingleEntity');
    }

    public function testMain()
    {
        $this->installTestEntities('ImageSingleEntity');

        $this->createImage();
        $this->updateImage();
        $this->removeHardImage();
        $this->removeSoftImage();

        $item = new ImageSingleEntity();
        $this->rmDir($item->getUploadRootDir());
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Could not find a directory for the path 'wrong_path'. If this path is wrong, you can override the Casper\AttachmentBundle\Entity\ImageSingleEntity::getAbsoluteSitePath method to change it.
     */
    public function testGetRootDir()
    {
        $this->installTestEntities('ImageSingleEntity');
        // Check right path
        $item = new ImageSingleEntity();
        $this->assertRegExp('#uploads/images$#', $item->getUploadRootDir());
        // Check wrong path
        $item->wrongPath = true;
        $item->getUploadRootDir();
    }

    protected function createImage()
    {
        $em = $this->getMockEntityManager();
        $this->assertCount(0, $this->repo()->findAll());

        $image = new ImageSingleEntity();
        $image->setUploadedFile($this->createUploadedImagePng('image'));

        $this->assertNull($image->getWebPath());
        $this->assertNull($image->getAbsolutePath());

        $em->persist($image);
        $em->flush();

        $this->assertItem(1, 'image', $em);
    }

    public function updateImage()
    {
        $em = $this->getMockEntityManager();
        $item = $this->repo()->find(1);
        $this->assertNotNull($item);

        $oldPath = $item->getAbsolutePath();
        $item->setUploadedFile($this->createUploadedImagePng('changed'));

        $em->persist($item);
        $em->flush();

        $updatedItem = $this->assertItem(1, 'changed', $em);
        $this->assertNotEquals($updatedItem->getPath(), $oldPath);
        $this->assertFalse(file_exists($oldPath));
    }

    protected function removeHardImage()
    {
        $em = $this->getMockEntityManager();
        $item = $this->repo()->find(1);
        $this->assertNotNull($item);

        $newPath = $item->getAbsolutePath();

        $em->remove($item);
        $em->flush();

        $this->assertFalse(file_exists($newPath));
        $this->assertCount(0, $this->repo()->findAll());
    }

    protected function removeSoftImage()
    {
        $em = $this->getMockEntityManager();
        $this->assertCount(0, $this->repo()->findAll());

        $image = new ImageSingleEntity();
        $this->setNonPublicProperty($image, 'softDelete', true);
        $image->setUploadedFile($this->createUploadedImagePng('soft_image'));
        $em->persist($image);
        $em->flush();

        $this->assertTrue($image->isFileExists());
        $this->assertCount(1, $this->repo()->findAll());

        $oldPath = $image->getAbsolutePath();
        $em->remove($image);
        $em->flush();

        $this->assertFalse(file_exists($oldPath));
        $this->assertTrue(file_exists("$oldPath.del"));
        $this->assertCount(0, $this->repo()->findAll());
    }

    protected function assertItem($id, $name)
    {
        /** @var ImageSingleEntity $item */
        $item = $this->repo()->find($id);

        $this->assertEquals("$name.png", $item->getFilename());
        $this->assertRegExp('#\d+\.png#', $item->getPath());
        $this->assertNull($item->getUploadedFile());
        $this->assertTrue($item->isFileExists());
        $this->assertEquals($item->getUploadRootDir() . "/{$item->getPath()}", $item->getAbsolutePath());
        $this->assertEquals("uploads/images/{$item->getPath()}", $item->getWebPath());

        return $item;
    }
}