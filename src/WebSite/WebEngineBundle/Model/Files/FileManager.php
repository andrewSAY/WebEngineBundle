<?php
/**
 * Created by PhpStorm.
 * User: Сапрыкин А_Ю
 * Date: 14.10.15
 * Time: 15:51
 */

namespace WebSite\WebEngineBundle\Model\Files;


use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use WebSite\WebEngineBundle\Entity\Content;
use WebSite\WebEngineBundle\Model\ImageWorker\ImageInfo;
use WebSite\WebEngineBundle\Model\ImageWorker\ImageModifier;
use WebSite\WebEngineBundle\Model\Translate\TransLitTranslator;

class FileManager
{
    private $storageDirectory;
    private $fileSystem;
    private $content;

    function __construct(Content $entity)
    {
        $this->storageDirectory = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . $entity->getId();
        $this->content = $entity;
        $this->fileSystem = new Filesystem();
    }

    public function save(UploadedFile $file)
    {
        $imageWorker = new ImageModifier();
        $translator = new TransLitTranslator();
        $fileName = $translator->translateToLatin($file->getClientOriginalName());
        $filePath = $this->storageDirectory . DIRECTORY_SEPARATOR . $fileName;
        $isImage = $imageWorker->fileSupported($fileName);
        if ($this->content->getType() == 'news' && !$isImage)
        {
            throw new Exception('File ' . $fileName . ' is not supported. File must be image (' . implode(',', $imageWorker->getExtensionsSupportedFormats()) . ')');
        }
        if ($this->fileSystem->exists($filePath))
        {
            throw new Exception('File exist ' . $fileName);
        }
        if (!$this->fileSystem->exists($this->storageDirectory))
        {
            $this->fileSystem->mkdir($this->storageDirectory);
        }
        if ($isImage)
        {
            $this->fileSystem->mkdir($filePath);
            $file->move($filePath, $fileName);
            return;
        }
        $file->move($filePath, $fileName);
    }

    public function deleteFile($fileName)
    {
        $fileName = $this->storageDirectory . DIRECTORY_SEPARATOR . $fileName;
        if ($this->fileSystem->exists($fileName))
        {
            $this->fileSystem->remove($fileName);
        }
    }

    public function removeStorageDirectory()
    {
        if ($this->fileSystem->exists($this->storageDirectory))
        {
            $this->fileSystem->remove($this->storageDirectory);
        }
    }

    public function createStorageDirectory()
    {
        if (!$this->fileSystem->exists($this->storageDirectory))
        {
            $this->fileSystem->mkdir($this->storageDirectory);
        }
    }

    /**
     * @param $fileName
     * @param $size
     * @return FileItem
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function getFile($fileName, $size)
    {
        $fileNameFull = $this->storageDirectory . DIRECTORY_SEPARATOR . $fileName;
        if (!$this->fileSystem->exists($fileNameFull))
        {
            throw new NotFoundHttpException('File ' . $fileName . ' is not found');
        }

        $imageWorker = new ImageModifier();
        $isImage = $imageWorker->fileSupported($fileNameFull);
        if ($isImage)
        {
            $fileNameFull = $fileNameFull . DIRECTORY_SEPARATOR . $fileName;
            if (!$this->fileSystem->exists($fileNameFull . '' . $size))
            {
                $imageWorker->open($fileNameFull);
                $imageWorker->reduceImageTo($size);
                $imageWorker->saveOrFlush($fileNameFull . '' . $size);
                $imageWorker->close();
            }
            $fileNameFull = $fileNameFull . '' . $size;
        }
        $fInfo = new \finfo(FILEINFO_MIME);
        $file = new FileItem($fileName, $isImage, filesize($fileNameFull), $fInfo->file($fileNameFull), file_get_contents($fileNameFull));
        return $file;
    }

    public function getFileList()
    {
        $files = array();
        if (!$this->fileSystem->exists($this->storageDirectory))
        {
            return $files;
        }
        $finder = new Finder();
        $find = $finder->name('*.*')->depth(0)->in($this->storageDirectory);
        $imageModifier = new ImageModifier();
        foreach ($find as $findItem)
        {
            $fileItem = new FileItem($findItem->getFileName(), $imageModifier->fileSupported($findItem->getFileName()));
            $files[] = $fileItem;
        }
        return $files;
    }

} 