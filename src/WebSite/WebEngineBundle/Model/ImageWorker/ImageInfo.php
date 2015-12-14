<?php
/**
 * Created by PhpStorm.
 * User: Сапрыкин А_Ю
 * Date: 13.10.15
 * Time: 15:51
 */

namespace WebSite\WebEngineBundle\Model\ImageWorker;


class ImageInfo
{
    private $isLandscape;
    private $width;
    private $height;

    function __construct($image)
    {
        $this->width = imagesx($image);
        $this->height = imagesy($image);
        $this->isLandscape = true;
        if ($this->width < $this->height)
        {
          $this->isLandscape = false;
        }
    }

    public function  getIsLandscape()
    {
        return $this->isLandscape;
    }

    public function  getWidth()
    {
        return $this->width;
    }

    public function  getHeight()
    {
        return $this->height;
    }


} 