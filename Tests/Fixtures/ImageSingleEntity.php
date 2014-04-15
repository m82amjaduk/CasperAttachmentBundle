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
class ImageSingleEntity extends AbstractSingleAttachment
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id = 1;

    public $wrongPath = false;

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
        if ($this->wrongPath) {
            return 'wrong_path';
        } else {
            return __DIR__;
        }
    }
}