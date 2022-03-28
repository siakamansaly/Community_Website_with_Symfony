<?php

namespace App\Form;

use App\Entity\Trick;
use App\Entity\TypeTrick;
use App\Repository\TypeTrickRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrickFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [])
            ->add('content', TextareaType::class, [
                'attr' => ['rows' => 6],
            ])
            ->add('featuredPicture', FileType::class, [
                'label_attr' => ['class' => 'd-none'],
                'required' => false,
                'data_class' => null
            ])
            ->add('type', EntityType::class, [
                'class' => TypeTrick::class,
                'required' => false,
                'placeholder' => 'Uncategorized',
                'query_builder' => function (TypeTrickRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.name', 'ASC');
                },
                'choice_label' => 'name',
            ])
            ->add('mediasVideos', CollectionType::class, [
                'entry_type' => VideoFormType::class,
                'label_attr' => ['class' => 'd-none'],
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,

            ])
            ->add('mediasPicture', CollectionType::class, [
                'entry_type' => PictureFormType::class,
                'label_attr' => ['class' => 'd-none'],
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,


            ]);
        $options = [];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
