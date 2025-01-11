<?php

namespace App\Form\Gallery;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;

class UploadGalleryFilesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('watermark', CheckboxType::class, [
                'required' => false,
                'mapped' => false,
            ])
            ->add('files', FileType::class, [
                'label' => 'File',
                'mapped' => false,
                'multiple' => true,
                'required' => false,
                'attr' => [
                    'accept' => "image/*"
                ],
                'constraints' => [
                    new All([
                        'constraints' => [
                            new File(
                                ['maxSize' => '25M',
                                    'mimeTypes' => [
                                        'image/jpeg',
                                        'image/png',
                                        'application/pdf',
                                    ],
                                    'mimeTypesMessage' => 'Please upload a valid file (JPEG, PNG, or PDF).',
                                ],)
                        ],
                    ]),

                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {

    }
}