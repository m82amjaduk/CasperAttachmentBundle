<?php
namespace Casper\AttachmentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 *
 * Class AbstractAttachmentContainer
 * @package Casper\AttachmentBundle\Entity
 */
abstract class AbstractAttachmentContainer
{
    /**
     * Attachments
     *
     * @var ArrayCollection|AbstractMultiAttachment[]
     *
     * @ORM\OneToMany(
     *      targetEntity="Casper\AttachmentBundle\Entity\AbstractMultiAttachment",
     *      mappedBy="holder",
     *      cascade={"persist", "remove"},
     *      orphanRemoval=true
     * )
     * @ORM\OrderBy({"is_primary" = "DESC"})
     */
    protected $attachments;

    function __construct()
    {
        $this->attachments = new ArrayCollection();
    }

    /**
     * Get attachments
     *
     * @return AbstractMultiAttachment[]|ArrayCollection
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * @return AbstractMultiAttachment|null
     */
    public function getPrimaryAttachment()
    {
        foreach ($this->attachments as $item) {
            if ($item->getIsPrimary()) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Add an attachment
     *
     * @param AbstractMultiAttachment $attachment
     *
     * @return $this
     */
    public function addAttachment(AbstractMultiAttachment $attachment)
    {
        $attachment->setHolder($this);
        $this->attachments->add($attachment);

        return $this;
    }

    /**
     * Remove an attachment
     *
     * @param AbstractMultiAttachment $attachment
     *
     * @return $this
     */
    public function removeAttachment(AbstractMultiAttachment $attachment)
    {
        $this->attachments->removeElement($attachment);

        return $this;
    }
} 