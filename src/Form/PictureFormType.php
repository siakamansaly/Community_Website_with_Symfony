<?php

namespace App\Form;

use App\Entity\MediaPicture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PictureFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', FileType::class, [
                'label' => false,
                'data_class' => null,

            ])
            ->add('pictureEdit', HiddenType::class, [
                'mapped' => false,
                'attr' => ['class' => 'hidden-field', 'value' => '0', 'id' => 'pictureEdit']
            ]);
        $options = [];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MediaPicture::class,
        ]);
    }
}
