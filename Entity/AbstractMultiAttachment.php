<?php
namespace Casper\AttachmentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 *
 * Class AbstractMultiAttachment
 * @package Casper\AttachmentBundle\Entity
 */
abstract class AbstractMultiAttachment extends AbstractSingleAttachment
{
    /**
     * @var boolean
     *
     * @ORM\Column(name="isPrimary", type="boolean", nullable=false, options={"default"=false})
     */
    protected $isPrimary = false;

    /**
     * Related holder
     *
     * @var object|AbstractAttachmentContainer
     *
     * @ORM\ManyToOne(
     *     targetEntity="Casper\AttachmentBundle\Entity\AbstractAttachmentContainer"
     * )
     */
    protected $holder;

    /**
     * Should return ID of a related entity
     *
     * @return string|integer
     */
    abstract protected function getHolderId();

    /**
     * Get Holder
     *
     * @return AbstractAttachmentContainer|object
     */
    public function getHolder()
    {
        return $this->holder;
    }

    /**
     * Set holder
     *
     * @param AbstractAttachmentContainer|object $holder
     *
     * @return AbstractMultiAttachment
     */
    public function setHolder($holder)
    {
        $this->holder = $holder;

        return $this;
    }

    /**
     * Get IsPrimary
     *
     * @return boolean
     */
    public function getIsPrimary()
    {
        return $this->isPrimary;
    }

    /**
     * Set isPrimary
     *
     * @param boolean $isPrimary
     *
     * @return AbstractMultiAttachment
     */
    public function setIsPrimary($isPrimary)
    {
        $this->isPrimary = $isPrimary;

        return $this;
    }

    protected function getRealUploadDir()
    {
        $group = $this->getHolderId()
            ? '/' . $this->getHolderId()
            : '';

        return $this->getUploadDir() . $group;
    }
} 