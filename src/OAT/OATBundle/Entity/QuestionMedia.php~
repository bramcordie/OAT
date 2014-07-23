<?php

namespace OAT\OATBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Media type enumerator.
 *
 * This class acts as an enumerator for all the valid media types and where they should be located.
 *
 */
class MediaTypes
{
    const Image_jpg = "imagepool";
}

/**
 * Media that can be used in a question.
 * 
 * @link http://symfony.com/doc/current/cookbook/doctrine/file_uploads.html The Symfony 2 documentation explaining how file uploads work.
 * @ORM\Entity
 */
class QuestionMedia{
    
    /**
     * path
     *
     * The path is a hash of the media file also making it his id.
     *
     * @ORM\Id
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $path;
    
    /**
     * description
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    protected $description;
    
    /**
     * file
     *
     * Placeholder for newly uploaded files.
     *
     * @Assert\File(maxSize="6000000")
     * @Assert\NotNull()
     */
    public $file;
    
    
    /**
     * media type
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    protected $mediaType = MediaTypes::Image_jpg;

    /**
     * Get absolute path
     *
     * Returns a string representing the absolute path of this media file or null if not available.
     *
     * @return null|string
     */
    public function getAbsolutePath()
    {
        return null === $this->getPath() ? null : $this->getUploadRootDir().'/'.$this->getPath();
    }

    /**
     * Get web path
     *
     * Returns a string representing the web path of this media file or null if not available
     *
     * @return null|string
     */
    public function getWebPath()
    {
        return null === $this->getPath() ? null : $this->getUploadDir().'/'.$this->getPath();
    }

    /**
     * Get upload root directory
     *
     * @return string
     */
    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded documents should be saved
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    /**
     * Get upload directory
     *
     * Return the right directory based on the media type of the file.
     *
     * @return string
     */
    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw when displaying uploaded doc/image in the view.
        return $this->getMediaType();
    }

    /**
     * Upload
     *
     * @return mixed
     */
    public function upload()
    {
        if (null === $this->file) {
            return;
        }

        $ext = $this->file->guessExtension();
        if (!$ext) {
            $ext = 'bin';
        }
        $fileHash = sha1_file($this->file);

        $this->file->move($this->getUploadRootDir(), $fileHash.".".$ext);

        $this->setPath($fileHash.".".$ext);

        $this->file = null;
    }

    /**
     * Set path
     *
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set mediaType
     *
     * @param string $mediaType
     */
    public function setMediaType($mediaType)
    {
        $this->mediaType = $mediaType;
    }

    /**
     * Get mediaType
     *
     * @return string 
     */
    public function getMediaType()
    {
        return $this->mediaType;
    }

    /**
     * Set description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
}