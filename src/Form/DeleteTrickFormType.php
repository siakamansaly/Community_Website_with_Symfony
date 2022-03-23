<?php

namespace App\Form;

use App\Entity\Trick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeleteTrickFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('delete', HiddenType::class, [
            'mapped' => false,
            'attr' => ['class' => 'hidden-field', 'value' => '0', 'id' => 'delete']
        ])
        ->add('action', HiddenType::class, [
            'mapped' => false,
            'attr' => ['class' => 'hidden-field', 'value' => '', 'id' => 'action']
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
