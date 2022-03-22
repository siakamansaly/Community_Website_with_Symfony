<?php

namespace App\Form;

use App\Entity\MediaVideo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VideoFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', UrlType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'https://']
            ])
            ->add('videoEdit', HiddenType::class, [
                'mapped' => false,
                'attr' => ['class' => 'hidden-field', 'value' => '0', 'id' => 'videoEdit']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MediaVideo::class,
        ]);
    }
}
