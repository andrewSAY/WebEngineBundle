<?php

namespace WebSite\WebEngineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Navigation
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="WebSite\WebEngineBundle\Entity\NavigationRepository")
 * @UniqueEntity("link")
 */
class Navigation
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="ownerId", type="integer")
     */
    private $ownerId;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=1024)
     */
    private $link;

    /**
     * @var string
     *
     * @ORM\Column(name="target", type="string", length=25)
     */
    private $target;

    /**
     * @var string
     *
     * @ORM\Column(name="roles", type="string", length=1024)
     */
    private $roles;

    /**
     * @var boolean
     *
     * @ORM\Column(name="hide", type="boolean")
     */
    private $hide;

    /**
     * @var integer
     *
     * @ORM\Column(name="inner_order", type="integer")
     */
    private $innerOrder;

    /**
     * @var boolean
     * @ORM\Column(name="show_previews", type="boolean")
     */
    private $showPreviews;

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
     * Set name
     *
     * @param string $name
     * @return Navigation
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set ownerId
     *
     * @param integer $ownerId
     * @return Navigation
     */
    public function setOwnerId($ownerId)
    {
        if (!is_numeric($ownerId))
        {
            $ownerId = 0;
        }
        $this->ownerId = $ownerId;

        return $this;
    }

    /**
     * Get ownerId
     *
     * @return integer
     */
    public function getOwnerId()
    {
        return $this->ownerId;
    }

    /**
     * Set link
     *
     * @param string $link
     * @return Navigation
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
     * Set target
     *
     * @param string $target
     * @return Navigation
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Get target
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set roles
     *
     * @param string $roles
     * @return Navigation
     */
    public function setRoles($roles)
    {
        $rolesString = '';

        foreach ($roles as $role)
        {
            $rolesString = $rolesString . ',' . $role;
        }
        $this->roles = $rolesString;

        return $this;
    }

    /**
     * Get roles
     *
     * @return string
     */
    public function getRoles()
    {
        return explode(',', $this->roles);
    }

    /**
     * Set hide
     *
     * @param boolean $hide
     * @return Navigation
     */
    public function setHide($hide)
    {
        $this->hide = $hide;

        return $this;
    }

    /**
     * Get hide
     *
     * @return boolean
     */
    public function getHide()
    {
        return $this->hide;
    }

    public function getInnerOrder()
    {
        return $this->innerOrder;
    }

    public function setInnerOrder($innerOrder)
    {
        $this->innerOrder = $innerOrder;
    }

    public function setShowPreviews($value)
    {
        $this->showPreviews = $value;
    }

    public function getShowPreviews()
    {
        return $this->showPreviews;
    }
}
