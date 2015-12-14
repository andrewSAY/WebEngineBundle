<?php

namespace WebSite\WebEngineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Date;

/**
 * Content
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="WebSite\WebEngineBundle\Entity\ContentRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Content
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=1024, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="caption", type="string", length=1024, nullable=true)
     */
    private $caption;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=1024, nullable=true)
     */
    private $link;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text", nullable=true)
     */
    private $body;

    /**
     * @var string
     *
     * @ORM\Column(name="preview", type="string", length=2048, nullable=true)
     */
    private $preview;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var boolean
     *
     * @ORM\Column(name="forComments", type="boolean")
     */
    private $forComments;

    /**
     * @var integer
     *
     * @ORM\Column(name="visits", type="integer")
     */
    private $visits;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreate", type="datetime")
     */
    private $dateCreate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateUpdate", type="datetime")
     */
    private $dateUpdate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datePublication", type="datetime")
     */
    private $datePublication;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateHidden", type="datetime", nullable=true)
     */
    private $dateHidden;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isHide", type="boolean", nullable=true)
     */
    private $isHide;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Content
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set caption
     *
     * @param string $caption
     * @return Content
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;

        return $this;
    }

    /**
     * Get caption
     *
     * @return string 
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * Set link
     *
     * @param string $link
     * @return Content
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string 
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set body
     *
     * @param string $body
     * @return Content
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string 
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set preview
     *
     * @param string $preview
     * @return Content
     */
    public function setPreview($preview)
    {
        $this->preview = $preview;

        return $this;
    }

    /**
     * Get preview
     *
     * @return string 
     */
    public function getPreview()
    {
        return $this->preview;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Content
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set forComments
     *
     * @param boolean $forComments
     * @return Content
     */
    public function setForComments($forComments)
    {
        $this->forComments = $forComments;

        return $this;
    }

    /**
     * Get forComments
     *
     * @return boolean 
     */
    public function getForComments()
    {
        return $this->forComments;
    }

    /**
     * Set visits
     *
     * @param integer $visits
     * @return Content
     */
    public function setVisits($visits)
    {
        $this->visits = $visits;

        return $this;
    }

    /**
     * Get visits
     *
     * @return integer 
     */
    public function getVisits()
    {
        return $this->visits;
    }

    /**
     * Set dateCreate
     *
     * @param \DateTime $dateCreate
     * @return Content
     */
    public function setDateCreate($dateCreate)
    {
        $this->dateCreate = $dateCreate;

        return $this;
    }

    /**
     * Get dateCreate
     *
     * @return \DateTime 
     */
    public function getDateCreate()
    {
        return $this->dateCreate;
    }

    /**
     * Set dateUpdate
     *
     * @param \DateTime $dateUpdate
     * @return Content
     */
    public function setDateUpdate($dateUpdate)
    {
        $this->dateUpdate = $dateUpdate;

        return $this;
    }

    /**
     * Get dateUpdate
     *
     * @return \DateTime 
     */
    public function getDateUpdate()
    {
        return $this->dateUpdate;
    }

    /**
     * Set datePublication
     *
     * @param \DateTime $datePublication
     * @return Content
     */
    public function setDatePublication($datePublication)
    {
        $this->datePublication = $datePublication;

        return $this;
    }

    /**
     * Get datePublication
     *
     * @return \DateTime 
     */
    public function getDatePublication()
    {
        return $this->datePublication;
    }

    /**
     * Set dateHidden
     *
     * @param \DateTime $dateHidden
     * @return Content
     */
    public function setDateHidden($dateHidden)
    {
        $this->dateHidden = $dateHidden;

        return $this;
    }

    /**
     * Get dateHidden
     *
     * @return \DateTime 
     */
    public function getDateHidden()
    {
        return $this->dateHidden;
    }

    /**
     * Set isHide
     *
     * @param boolean $isHide
     * @return Content
     */
    public function setIsHide($isHide)
    {
        $this->isHide = $isHide;

        return $this;
    }

    /**
     * Get isHide
     *
     * @return boolean 
     */
    public function getIsHide()
    {
        return $this->isHide;
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        if(!$this->getDateCreate())
        {
            $date = new \DateTime();
            $this->dateCreate = $date;
            $this->dateUpdate = $date;
        }
    }

    /**
     * @ORM\PreUpdate()
     */
    public function preUpdate()
    {
        $this->setDateUpdate(new \DateTime());
    }
}
