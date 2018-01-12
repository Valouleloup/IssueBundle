<?php

namespace Valouleloup\IssueBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ElasticType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('search', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Search',
                    'class' => 'elastic-search-input'
                ]
            ])
        ;
    }

    public function getBlockPrefix()
    {
        return 'form_elastic';
    }

}