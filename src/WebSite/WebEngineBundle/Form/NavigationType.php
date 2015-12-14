<?php

namespace WebSite\WebEngineBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use UserManagement\UserManagerBundle\Model\UserRoles;

class NavigationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'Название'
            ))
            ->add('link', 'text', array(
                'label' => 'Ссылка'
            ))
            ->add('target', 'choice', array(
                'label' => 'Тип ссылки',
                'choices' => array(
                    '_self' => '_self',
                    '_blank' => '_blank'
                )
            ))
            ->add('roles', 'choice', array(
                'label' => 'Ограничение по ролям (white list)',
                'choices' => (new UserRoles())->getList(),
                'expanded' => true,
                'multiple' => true
            ))
            ->add('hide', 'checkbox', array(
                'label' => 'Скрывать',
                'required' => false
            ))
            ->add('showPreviews', 'checkbox', array(
                'label' => 'Отображать миниатюры дочерних элементов на странице',
                'required' => false
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'WebSite\WebEngineBundle\Entity\Navigation'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'website_webenginebundle_navigation';
    }
}
