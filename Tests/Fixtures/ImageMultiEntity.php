<?php
namespace Casper\AttachmentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ImageEntity
 *
 * @ORM\Table()
 * @ORM\Entity()
 *
 * @package Casper\AttachmentBundle\Tests\Fixtures\
 */
class ImageMultiEntity extends AbstractMultiAttachment
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Related Album
     *
     * @var AlbumEntity
     *
     * @ORM\ManyToOne(
     *     targetEntity="Casper\AttachmentBundle\Entity\AlbumEntity"
     * )
     */
    protected $holder;

    /**
     * Get Id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Relative path to upload dir (from the web folder)
     *
     * @return string
     */
    protected function getUploadDir()
    {
        return 'uploads/images';
    }

    /**
     * @return string
     */
    protected function getAbsoluteSitePath()
    {
        return __DIR__;
    }

    /**
     * Should return ID of a related entity
     *
     * @return mixed
     */
    protected function getHolderId()
    {
        return $this->getHolder()
            ? $this->getHolder()->getId()
            : null;
    }
}