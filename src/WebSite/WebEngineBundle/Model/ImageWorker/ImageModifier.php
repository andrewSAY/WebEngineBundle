<?php
/**
 * Created by PhpStorm.
 * User: Сапрыкин А_Ю
 * Date: 13.10.15
 * Time: 12:42
 */

namespace WebSite\WebEngineBundle\Model\ImageWorker;


use Symfony\Component\Config\Definition\Exception\Exception;

class ImageModifier
{
    private $image;
    private $fullName;
    private $fileName;
    private $fileExtension;
    /**
     * @var ImageInfo
     */
    private $imageInfo;
    private $supportedExtensionList = array(
        'PNG',
        'JPG',
        'JPEG',
        'GIF',
    );

    function __construct()
    {
    }

    public function open($pathToFile)
    {
        if (!file_exists($pathToFile))
        {
            throw new Exception('File ' . $pathToFile . ' is not found');
        }
        $this->fullName = $pathToFile;
        if (!$this->setExtension())
        {
            throw new Exception('File extension must be one from next values ' . implode(',', $this->supportedExtensionList));
        }
        $this->image = imagecreatefromstring(file_get_contents($pathToFile));
        if (!$this->image)
        {
            throw new Exception('File ' . $pathToFile . ' is don`t open as image');
        }
        $this->fileName = substr($this->fullName, stripos($this->fullName, DIRECTORY_SEPARATOR) + 1);
        $this->imageInfo = new ImageInfo($this->image);
    }

    private function setExtension()
    {
        $position = strripos($this->fullName, '.');
        if ($position < strlen($this->fullName) - 1)
        {
            $position = $position + 1;
        }
        $extension = strtoupper(substr($this->fullName, $position));
        if (!in_array($extension, $this->supportedExtensionList))
        {
            return false;
        }

        $this->fileExtension = $extension;
        return true;
    }

    /**
     * This method detected direction for reduced (height or wight) and apply him on this image
     * @param $value
     */
    public function reduceImageTo($value)
    {
        $k = 1;
        if ($this->imageInfo->getHeight() > $value)
        {
            $k = $value / $this->imageInfo->getHeight();
        }
        if ($this->imageInfo->getWidth() > $value)
        {
            $k = $value / $this->imageInfo->getWidth();
        }
        if ($k == 0)
        {
            $k = 1;
        }

        $newHeight = round($this->imageInfo->getHeight() * $k);
        $newWight = round($this->imageInfo->getWidth() * $k);
        $newImage = imagecreatetruecolor($newWight, $newHeight);
        imagecopyresampled($newImage, $this->image, 0, 0, 0, 0, $newWight, $newHeight, $this->imageInfo->getWidth(), $this->imageInfo->getHeight());
        $this->image = $newImage;
        $this->imageInfo = new ImageInfo($this->image);
    }

    public function saveOrFlush($path = null)
    {
        switch ($this->fileExtension)
        {
            case 'PNG':
                imagepng($this->image, $path);
                break;
            case 'GIF':
                imagegif($this->image, $path);
                break;
            case 'JPG':
                imagejpeg($this->image, $path);
                break;
            case 'JPEG':
                imagejpeg($this->image, $path);
                break;
            default:
                break;
        }
    }

    public function close()
    {
        imagedestroy($this->image);
        $this->fullName = null;
        $this->fileName = null;
        $this->imageInfo = null;
        $this->fileExtension = null;
    }

    public function getFilename()
    {
        return $this->fileName;
    }

    public function getExtension()
    {
        return $this->fileExtension;
    }

    public function getFilenameWithoutExtension()
    {
        return str_replace('.' . $this->fileExtension, strtolower('.' . $this->fileExtension), '');
    }

    public function fileSupported($fileName)
    {
        $this->fullName = $fileName;
        $value = $this->setExtension();
        $this->fullName = null;
        $this->fileExtension = null;
        return $value;
    }

    public function getExtensionsSupportedFormats()
    {
        return $this->supportedExtensionList;
    }
} 