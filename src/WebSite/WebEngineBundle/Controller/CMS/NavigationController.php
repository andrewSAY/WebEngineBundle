<?php

namespace WebSite\WebEngineBundle\Controller\CMS;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use WebSite\WebEngineBundle\Entity\Navigation;
use WebSite\WebEngineBundle\Form\NavigationType;
use WebSite\WebEngineBundle\Model\Navigation\NavigationBuilder;
use WebSite\WebEngineBundle\Model\Navigation\NavigationManager;

/**
 * Navigation controller.
 *
 * @Route("/cms/navigation")
 */
class NavigationController extends Controller
{

    /**
     * Lists all Navigation entities.
     *
     * @Route("/", name="web_engine_cms_navigation")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('WebEngineBundle:Navigation')->findAllOrdered();
        $builder = new NavigationBuilder();
        $tree = $builder->buildFullTree($entities);
        $json = $tree->jsonSerialize(array(), $this->container->get('router'));

        return array(
            'entities' => $tree,
            'json' => json_encode($json),
            'move_form' => $this->createMoveForm()->createView()
        );
    }
    /**
     * Creates a new Navigation entity.
     *
     * @Route("/", name="web_engine_cms_navigation_create")
     * @Method("POST")
     * @Template("WebEngineBundle:CMS\Navigation:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Navigation();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $innerOrder = $em->getRepository('WebEngineBundle:Navigation')->getNextNumberByOwner(0);

            $entity->setOwnerId(0);
            $entity->setInnerOrder($innerOrder);

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('web_engine_cms_navigation'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Navigation entity.
     *
     * @param Navigation $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Navigation $entity)
    {
        $form = $this->createForm(new NavigationType(), $entity, array(
            'action' => $this->generateUrl('web_engine_cms_navigation_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Создать'));

        return $form;
    }

    /**
     * Displays a form to create a new Navigation entity.
     *
     * @Route("/new", name="web_engine_cms_navigation_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Navigation();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }


    /**
     * Displays a form to edit an existing Navigation entity.
     *
     * @Route("/{id}/edit", name="web_engine_cms_navigation_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebEngineBundle:Navigation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Navigation entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Navigation entity.
    *
    * @param Navigation $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Navigation $entity)
    {
        $form = $this->createForm(new NavigationType(), $entity, array(
            'action' => $this->generateUrl('web_engine_cms_navigation_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Сохранить'));

        return $form;
    }
    /**
     * Edits an existing Navigation entity.
     *
     * @Route("/{id}", name="web_engine_cms_navigation_update")
     * @Method("PUT")
     * @Template("WebEngineBundle:Navigation:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebEngineBundle:Navigation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Navigation entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('web_engine_cms_navigation_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Navigation entity.
     *
     * @Route("/{id}", name="web_engine_cms_navigation_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('WebEngineBundle:Navigation')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Navigation entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('web_engine_cms_navigation'));
    }

    /**
     * Creates a form to delete a Navigation entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('web_engine_cms_navigation_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    public function moveAction(Request $request)
    {
        $form = $this->createMoveForm();
        $form->submit($request->request->get($form->getName()));

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $manager = new NavigationManager();

        if ($form->isValid())
        {
            $moveNodes = json_decode($form->get('moves_data')->getData());
            foreach ($moveNodes as $moveNode)
            {
                $manager->moveNode($moveNode->node, $moveNode->owner, $moveNode->position, $this->getDoctrine()->getManager());
            }
        }

        return $response;
    }

    private function createMoveForm()
    {
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('web_engine_cms_navigation_move'))
            ->add('moves_data', 'text')
            ->add('submit', 'submit', array(
                'label' => 'Create',
                'attr' => array(
                    'style' => 'display:none;'
                )
            ))
            ->getForm();

        return $form;
    }
}
