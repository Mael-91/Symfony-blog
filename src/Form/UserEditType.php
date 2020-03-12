<?php

namespace App\Form;

use App\Entity\User;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserEditType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('roles', ChoiceType::class, [
                'multiple' => true,
                'expanded' => true,
                'choices' => User::Role
            ])
            ->add('enabled', ChoiceType::class, [
                'expanded' => true,
                'choices' => [
                    'Yes' => true,
                    'No' => false,
                ],
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'attr' => ['placeholder' => 'example@example.com']
            ])
            ->add('first_name', null, [
                'required' => true,
            ])
            ->add('last_name')
            ->add('birthday', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'attr' => [
                    'placeholder' => 'Select a date',
                    'class' => 'flatpickr'
                ]
            ])
            ->add('sexe', ChoiceType::class, [
                'choices' => $this->getChoices(),
                'translation_domain' => 'userForm'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'translation_domain' => 'userForm'
        ]);
    }

    private function getChoices() {
        $choices = User::Sexe;
        $output = [];
        foreach ($choices as $k => $v) {
            $output[$v] = $k;
        }
        return $output;
    }
}
