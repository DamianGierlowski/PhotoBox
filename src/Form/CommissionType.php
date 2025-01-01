<?php

namespace App\Form;

use App\Entity\Commission;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommissionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'attr' => [
                    'class' =>'w-full px-0 py-2 border-b-2 border-gray-300 focus:border-teal-500 focus:outline-none transition-colors duration-300 bg-transparent'
                ],
            ])
            ->add('date', null, [
                'widget' => 'single_text',
                'attr' => [
                    'class' =>'w-full px-0 py-2 border-b-2 border-gray-300 focus:border-teal-500 focus:outline-none transition-colors duration-300 bg-transparent'
                ],
            ]);
        $builder->add('submit', SubmitType::class, [
            'attr' => [
                'class' => 'w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500'
            ],
        ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commission::class,
        ]);
    }
}
