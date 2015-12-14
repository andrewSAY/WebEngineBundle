<?php
/**
 * Created by PhpStorm.
 * User: Сапрыкин А_Ю
 * Date: 21.09.15
 * Time: 12:33
 */

namespace WebSite\WebEngineBundle\Model\Navigation;


use Doctrine\ORM\EntityManager;

class NavigationManager
{
    public function moveNode($nodeId, $ownerId, $position, EntityManager $em)
    {
        $repository = $em->getRepository('WebSite\WebEngineBundle\Entity\Navigation');

        $entity = $repository->find($nodeId);
        $entity->setOwnerId($ownerId);

        $repository->reorderInOwners($entity, $position, $entity->getOwnerId(), $ownerId);

        $em->flush($entity);
    }
} 