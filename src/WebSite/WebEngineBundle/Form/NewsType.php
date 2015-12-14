<?php

namespace WebSite\WebEngineBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NewsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'label' => 'Заголовок',
                'required' => true
            ))
            ->add('caption', 'text', array(
                'label' => 'Источник',
                'attr' => array(
                  'title' => 'Указывается название сайта или издания, откуда была взято информационное сообщение'
                ),
                'required' => false
            ))
            ->add('link', 'text', array(
                'label' => 'Ссылка на источник',
                'attr' => array(
                    'title' => 'Указывается ссылка на изначальное информационное сообщение'
                ),
                'required' => false
            ))
            ->add('body', 'textarea', array(
                'label' => 'Содержание',
                'required' => true
            ))
            ->add('preview', 'textarea', array(
                'label' => 'Превью',
                'required' => true
            ))
            ->add('datePublication', 'datetime', array(
                'label' => 'Дата публиуации',
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy H:mm',
                'required' => true
            ))
            ->add('dateHidden', 'datetime', array(
                'label' => 'Дата снятия с публикации',
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy H:mm',
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
