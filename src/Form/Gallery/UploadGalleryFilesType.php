<?php

namespace App\Form\Gallery;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class UploadGalleryFilesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('files', FileType::class, [
                'label' => 'File',
                'mapped' => false,
                'multiple' => true,
                'required' => false,
                'attr' => [
                    'accept' => "image/*"
                ],
                'constraints' => [
                    new Image(
                        [
                            'maxSize' => '25M',
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/png',
                                'application/pdf',
                            ],
                            'mimeTypesMessage' => 'Please upload a valid file (JPEG, PNG, or PDF).',
                        ],

                    )
                ]
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {

    }
}