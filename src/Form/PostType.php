<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $textareaOptions = $options['textarea_rows'] ? ['attr' => ['rows' => $options['textarea_rows']]] : [];

        $builder
            ->add('title', options: [
                'disabled' => 'new' !== $options['controller_action'],
            ])
            ->add('category', options: [
                'choice_label' => 'toLabel', // Possibilité de faire référence à une propriété ou une méthode
                // 'choice_label' => fn (Category $c) => $c->getName(), ou encore d'utiliser une Lambda
            ])
            ->add('body', options: $textareaOptions)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
            'textarea_rows' => null,
            'controller_action' => 'new',
        ])->setAllowedTypes('textarea_rows', ['null', 'integer'])
        ->setAllowedValues('controller_action', ['new', 'edit']);
    }
}
