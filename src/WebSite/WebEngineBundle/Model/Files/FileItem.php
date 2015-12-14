<?php
/**
 * Created by PhpStorm.
 * User: Сапрыкин А_Ю
 * Date: 14.10.15
 * Time: 15:59
 */

namespace WebSite\WebEngineBundle\Model\Files;


class FileItem
{
    private $isImage;
    private $name;
    private $size;
    private $mimeType;
    private $content;

    function __construct($name, $isImage = false, $size = null, $mimeType = null, $content = null)
    {
        $this->name = $name;
        $this->isImage = $isImage;
        $this->size = $size;
        $this->mimeType = $mimeType;
        $this->content = $content;
    }

    public function getIsImage()
    {
        return $this->isImage;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getMimeType()
    {
        return $this->mimeType;
    }

    public function getContent()
    {
        return $this->content;
    }
} 