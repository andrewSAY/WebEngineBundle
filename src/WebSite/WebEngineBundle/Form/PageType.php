<?php

namespace WebSite\WebEngineBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('link', 'text', array(
                'label' => 'Имя для ссылки'
            ))
            ->add('title', 'text', array(
                'label' => 'Заголовок',
                'required' => false
            ))
            ->add('caption', 'text', array(
                'label' => 'Подпись',
                'required' => false
            ))
            ->add('body', 'textarea', array(
                'label' => 'Тело',
                'required' => false
            ))
            ->add('preview', 'text', array(
                'label' => 'Превью',
                'required' => false
            ))
            ->add('datePublication', 'datetime', array(
                'label' => 'Дата публиуации',
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy H:mm',
            ))
            ->add('dateHidden', 'datetime', array(
                'label' => 'Дата снятия с публикации',
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd.MM.yyyy H:mm',
            ))
            ->add('forComments', 'checkbox', array(
                'label' => 'Разешить комментарии',
                'required' => false
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'WebSite\WebEngineBundle\Entity\Content'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'website_webenginebundle_content';
    }
}
