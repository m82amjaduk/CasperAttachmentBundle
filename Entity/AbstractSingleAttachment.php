<?php
namespace Casper\AttachmentBundle\Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks()
 *
 * Class AbstractSingleAttachment
 * @package Casper\AttachmentBundle\Entity
 */
abstract class AbstractSingleAttachment
{
    /**
     * @var string $filename
     *
     * @ORM\Column(name="filename", type="string", length=255)
     */
    protected $filename;
    /**
     * @var string $filename
     *
     * @ORM\Column(name="path", type="string", length=255)
     */
    protected $path;
    /**
     * @var string
     */
    protected $oldPath;
    /**
     * @var UploadedFile
     */
    protected $uploadedFile;
    /**
     * @var bool
     */
    protected $softDelete = false;

    /**
     * Relative path to upload dir (from the web folder)
     *
     * @return string
     */
    abstract protected function getUploadDir();

    /**
     * This can be overridden in an extended class
     * Uses in the Casper\AttachmentBundle\Entity\AbstractMultiAttachment class to add sub-directory as an images group
     *
     * @return string
     */
    protected function getRealUploadDir()
    {
        return $this->getUploadDir();
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getAbsoluteSitePath()
    {
        return __DIR__ . '/../../../../../web';
    }

    /**
     * Get Filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set filename
     *
     * @param string $filename
     *
     * @return AbstractSingleAttachment
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get Path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set path
     *
     * @param string $path
     *
     * @return AbstractSingleAttachment
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get UploadedFile
     *
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    public function getUploadedFile()
    {
        return $this->uploadedFile;
    }

    /**
     * Set uploadedFile
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $uploadedFile
     *
     * @return AbstractSingleAttachment
     */
    public function setUploadedFile($uploadedFile)
    {
        $this->uploadedFile = $uploadedFile;

        if (isset($this->path)) {
            $this->oldPath = $this->path;
            $this->path = null;
        }

        return $this;
    }

    /**
     * Check if real file exits
     *
     * @return bool
     */
    public function isFileExists()
    {
        return file_exists($this->getAbsolutePath());
    }

    /**
     * Absolute path of the destination directory for all the images of this entity
     *
     * @return string
     */
    public function getUploadRootDir()
    {
        return $this->getCheckedAbsoluteSitePath() . '/' . $this->getRealUploadDir();
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function getCheckedAbsoluteSitePath()
    {
        $path = $this->getAbsoluteSitePath();
        $realPath = realpath($path);
        if (false == $realPath) {
            $class = get_class($this);
            throw new \Exception("Could not find a directory for the path '$path'. If this path is wrong, you can override the $class::getAbsoluteSitePath method to change it.");
        }

        return $realPath;
    }

    /**
     * @return null|string
     */
    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir() . '/' . $this->path;
    }

    /**
     * @return null|string
     */
    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getRealUploadDir() . '/' . $this->path;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->getUploadedFile()) {
            $this->filename = $this->getUploadedFile()->getClientOriginalName();
            $uid            = str_replace('.', '', microtime(true));
            $ext            = $this->getUploadedFile()->getExtension();
            if (!$ext) {
                $ext = $this->getUploadedFile()->guessExtension();
            }
            $this->path     = "$uid.$ext";
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null !== $this->getUploadedFile()) {
            $dir = $this->getUploadRootDir();
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }
            $this->getUploadedFile()->move($dir, $this->path);
            if (isset($this->oldPath)) {
                unlink("$dir/{$this->oldPath}");
                $this->oldPath = null;
            }
            $this->uploadedFile = null;
        }
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            if ($this->softDelete) {
                $this->softDeleteFile($file);
            } else {
                unlink($file);
            }
        }
    }

    protected function softDeleteFile($path)
    {
        rename($path, "$path.del");
    }
} 