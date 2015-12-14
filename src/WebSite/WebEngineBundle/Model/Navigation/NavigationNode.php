<?php
/**
 * Created by PhpStorm.
 * User: Сапрыкин А_Ю
 * Date: 17.09.15
 * Time: 17:06
 */

namespace WebSite\WebEngineBundle\Model\Navigation;


use Symfony\Component\Routing\Router;
use WebSite\WebEngineBundle\Entity\Navigation;

class NavigationNode
{
    private $item;
    private $parent;
    private $children;

    function __construct()
    {
        $this->children = array();
        $this->item = null;
        $this->parent = null;
    }

    public function add(NavigationNode $node)
    {
        $node->setParent($this);
        $this->children[] = $node;
    }

    public function addFromItem(Navigation $item)
    {
        $node = new NavigationNode();
        $node->setItem($item);
        $node->setParent($this);
        $this->children[] = $node;
    }

    public function setItem($item)
    {
        $this->item = $item;
    }

    public function getItem()
    {
        return $this->item;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return NavigationNode
     */
    public function getParent()
    {
        return $this->parent;
    }

    public function getCountChildren()
    {
        return count($this->children);
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function setFieldsForSerialize()
    {

    }

    public function jsonSerialize($json, Router $router)
    {
        if ($this->item != null)
        {
            $json = array(
                'id' => $this->item->getId(),
                'label' => $this->item->getName(),
                'link' => $router->generate('web_engine_cms_navigation_edit', array('id' => $this->item->getId()))
            );
        }

        foreach ($this->children as $child)
        {
            $json['children'][] = $child->jsonSerialize($json, $router);
        }

        return $json;
    }
} 