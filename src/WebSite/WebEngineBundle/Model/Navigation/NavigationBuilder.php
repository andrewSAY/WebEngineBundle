<?php
/**
 * Created by PhpStorm.
 * User: Сапрыкин А_Ю
 * Date: 17.09.15
 * Time: 16:57
 */

namespace WebSite\WebEngineBundle\Model\Navigation;


class NavigationBuilder
{
    public function buildFullTree($entities)
    {
        $root = new NavigationNode();

        return $this->build($root, $entities);
    }

    private function build(NavigationNode $node, $entities)
    {
        foreach ($entities as $key => $entity)
        {

            if ($entity->getOwnerId() == 0 && $node->getParent() == null)
            {
                $node->addFromItem($entity);
                unset($entities[$key]);
                continue;
            }

            if($node->getItem() == null)
            {
                continue;
            }

            if ($entity->getOwnerId() == $node->getItem()->getId())
            {
                $node->addFromItem($entity);
                unset($entities[$key]);
            }
        }

        if (count($entities) > 0)
        {
            foreach($node->getChildren() as $child)
            {
                $child = $this->build($child, $entities);
            }
        }

        return $node;
    }
} 