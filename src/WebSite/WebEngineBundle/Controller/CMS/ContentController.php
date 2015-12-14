<?php

namespace WebSite\WebEngineBundle\Controller\CMS;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use WebSite\WebEngineBundle\Entity\Content;
use WebSite\WebEngineBundle\Form\ContentType;
use WebSite\WebEngineBundle\Form\NewsType;
use WebSite\WebEngineBundle\Form\PageType;
use WebSite\WebEngineBundle\Model\Files\FileManager;
use WebSite\WebEngineBundle\Model\ImageWorker\ImageModifier;

/**
 * Content controller.
 *
 * @Route("/cms/content")
 */
class ContentController extends Controller
{

    /**
     * Lists all Content entities.
     *
     * @Route("/", name="web_engine_cms_content")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($type)
    {
        $this->check($type);
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('WebEngineBundle:Content')->findBy(array('type' => $type), array('datePublication' => 'desc'));

        return array(
            'entities' => $entities,
            'type' => $type
        );
    }

    /**
     * Creates a new Content entity.
     *
     * @Route("/", name="web_engine_cms_content_create")
     * @Method("POST")
     */
    public function createAction(Request $request, $type)
    {
        $this->check($type);
        $entity = new Content();
        $form = $this->createCreateForm($entity, $type);
        $form->handleRequest($request);

        $this->customValid($form, $type);
        if ($form->isValid())
        {
            $entity->setType($type);
            if ($type == 'news')
            {
                $entity->setForComments(true);
            }
            $entity->setVisits(0);
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $fileManager = new FileManager($entity);
            $fileManager->createStorageDirectory();

            return $this->redirect($this->generateUrl('web_engine_cms_content_edit', array('id' => $entity->getId())));
        }

        return $this->render('WebEngineBundle:CMS\Content:' . $type . '.html.twig', array(
            'form' => $form->createView(),
            'entity' => $entity
        ));
    }

    private function customValid(Form $form, $type)
    {
        switch ($type)
        {
            case 'page':
                if ($form->get('link')->isEmpty())
                {
                    $form->get('link')->addError(new FormError('Укажите имя для ссылки'));
                }
                break;
            case 'news':
                if ($form->get('title')->isEmpty())
                {
                    $form->get('title')->addError(new FormError('Укажите заголовок'));
                }
                if ($form->get('body')->isEmpty())
                {
                    $form->get('body')->addError(new FormError('Отсутствует превью'));
                }
                if ($form->get('preview')->isEmpty())
                {
                    $form->get('preview')->addError(new FormError('Отсутствует превью'));
                }
                if ($form->get('caption')->isEmpty() && !$form->get('link')->isEmpty())
                {
                    $form->get('caption')->addError(new FormError('Укажите источник'));
                }
                if (!$form->get('caption')->isEmpty() && $form->get('link')->isEmpty())
                {
                    $form->get('link')->addError(new FormError('Укажите ссылку на источник'));
                }
                break;
            default:
                break;
        }

        if ($form->get('datePublication')->isEmpty())
        {
            $form->get('datePublication')->addError(new FormError('Укажите дату публикации'));
        }
    }


    private function createCreateForm(Content $entity, $type)
    {
        $formType = null;
        switch ($type)
        {
            case 'page':
                $formType = new PageType();
                break;
            case 'news':
                $formType = new NewsType();
                break;
            default:
                break;
        }

        $form = $this->createForm($formType, $entity, array(
            'action' => $this->generateUrl('web_engine_cms_content_create', array('type' => $type)),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Создать'));

        return $form;
    }

    /**
     * Displays a form to create a new Content entity.
     *
     * @Route("/new", name="web_engine_cms_content_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction($type)
    {
        $this->check($type);
        $entity = new Content();
        $form = $this->createCreateForm($entity, $type);

        return $this->render('WebEngineBundle:CMS\Content:' . $type . '.html.twig', array(
            'form' => $form->createView(),
            'entity' => $entity
        ));
    }

    /**
     * Displays a form to edit an existing Content entity.
     *
     * @Route("/{id}/edit", name="web_engine_cms_content_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebEngineBundle:Content')->find($id);

        if (!$entity)
        {
            throw $this->createNotFoundException('Unable to find Content entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('WebEngineBundle:CMS\Content:' . $entity->getType() . '.html.twig', array(
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'entity' => $entity,
            'files' => json_encode($this->getFiles($entity))
        ));
    }

    private function createEditForm(Content $entity)
    {
        $formType = null;
        switch ($entity->getType())
        {
            case 'page':
                $formType = new PageType();
                break;
            case 'news':
                $formType = new NewsType();
                break;
            default:
                break;
        }

        $form = $this->createForm($formType, $entity, array(
            'action' => $this->generateUrl('web_engine_cms_content_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Сохранить изменения'));

        return $form;
    }

    /**
     * Edits an existing Content entity.
     *
     * @Route("/{id}", name="web_engine_cms_content_update")
     * @Method("PUT")
     * @Template("WebEngineBundle:Content:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebEngineBundle:Content')->find($id);

        if (!$entity)
        {
            throw $this->createNotFoundException('Unable to find Content entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        $this->customValid($editForm, $entity->getType());
        if ($editForm->isValid())
        {
            $em->flush();

            return $this->redirect($this->generateUrl('web_engine_cms_content_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Content entity.
     *
     * @Route("/{id}", name="web_engine_cms_content_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('WebEngineBundle:Content')->find($id);

            if (!$entity)
            {
                throw $this->createNotFoundException('Unable to find Content entity.');
            }

            $type = $entity->getType();

            $fileManager = new FileManager($entity);
            $fileManager->removeStorageDirectory();

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('web_engine_cms_content', array('type' => $type)));
    }

    public function uploadFileAction(Request $request, $id)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $result = $this->createJsonArray();

        if ($request->files->count() > 0)
        {
            $file = current($request->files->all());

            /**
             * @var UploadedFile $file
             */
            if ($file->isValid())
            {
                $entity = $this->getDoctrine()->getManager()->getRepository('WebEngineBundle:Content')->find($id);
                if (!$entity)
                {
                    throw $this->createNotFoundException('Unable to find Content entity.');
                }
                $fileManager = new FileManager($entity);
                try
                {
                    $fileManager->save($file);
                } catch (Exception $exc)
                {
                    $result['state'] = 'failed';
                    $result['error'] = $exc->getMessage();
                }

            } else
            {
                $result['state'] = 'failed';
                $result['error'] = 'Upload error';
            }
        } else
        {
            $result['state'] = 'failed';
            $result['error'] = 'Not found files';
        }

        $result['files'] = $this->getFiles($entity);
        $response->setContent(json_encode($result));
        return $response;
    }

    public function deleteFileAction(Request $request, $id, $filename)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $result = $this->createJsonArray();
        $fileNameFull = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $filename;
        $fileSystem = new Filesystem();

        if ($fileSystem->exists($fileNameFull))
        {
            $fileSystem->remove($fileNameFull);
        }
        if ($fileSystem->exists($fileNameFull . '_cache'))
        {
            $fileSystem->remove($fileNameFull . '_cache');
        }
        if ($fileSystem->exists($fileNameFull))
        {
            $result['state'] = 'failed';
            $result['error'] = 'File not has been deleted';
        }

        $response->setContent(json_encode($result));

        return $response;
    }

    public function getFileAction($id, $filename, $size)
    {
        $entity = $this->getDoctrine()->getManager()->getRepository('WebEngineBundle:Content')->find($id);
        if (!$entity)
        {
            throw $this->createNotFoundException('Unable to find Content entity.');
        }
        $fileManager = new FileManager($entity);
        $file = $fileManager->getFile($filename, $size);

        $response = new Response();
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', $file->getMimeType());
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $file->getName() . '";');
        $response->headers->set('Content-length', $file->getSize());
        $response->sendHeaders();
        $response->setContent($file->getContent());

        return $response;
    }

    private function createJsonArray()
    {
        return array(
            'state' => 'ok',
            'error' => '',
            'files' => array()
        );
    }

    private function getFiles(Content $entity)
    {
        $fileManager = new FileManager($entity);
        $files = array();
        $findFiles = $fileManager->getFileList();
        foreach ($findFiles as $findFile)
        {
            $file = array(
                'label' => $findFile->getName(),
                'link' => $this->generateUrl('web_engine_cms_get_file', array('id' => $entity->getId(), 'filename' => $findFile->getName(), 'size' => ' ')),
                'delete_' => $this->generateUrl('web_engine_cms_delete_file', array('id' => $entity->getId(), 'filename' => $findFile->getName()))
            );
            if ($findFile->getIsImage())
            {
                $file['img'] = $this->generateUrl('web_engine_cms_get_file', array('id' => $entity->getId(), 'filename' => $findFile->getName(), 'size' => ' '));
            }
            $files[] = $file;
        }

        return $files;
    }

    /**
     * Creates a form to delete a Content entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('web_engine_cms_content_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }

    private function check($type)
    {
        $list = array(
            'page',
            'news'
        );

        if (!in_array($type, $list))
        {
            throw new HttpException('404', 'Value of type must by one from next values ' . implode(',', $list));
        }
    }
}
