<?php
namespace Casper\AttachmentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class AlbumEntity
 *
 * @ORM\Table()
 * @ORM\Entity()
 *
 * @package Casper\AttachmentBundle\Tests\Fixtures
 */
class AlbumEntity extends AbstractAttachmentContainer
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id = 1;

    /**
     * Photos
     *
     * @var ArrayCollection|ImageMultiEntity[]
     *
     * @ORM\OneToMany(
     *      targetEntity="Casper\AttachmentBundle\Entity\ImageMultiEntity",
     *      mappedBy="holder",
     *      cascade={"persist", "remove"},
     *      orphanRemoval=true
     * )
     * @ORM\OrderBy({"is_primary" = "DESC"})
     */
    protected $attachments;

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
     * @param $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}