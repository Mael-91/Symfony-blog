<?php

namespace App\Form;

use App\Entity\User;
use Mael\MaelRecaptchaBundle\Type\MaelRecaptchaSubmitType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfilSecurityPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('captcha', MaelRecaptchaSubmitType::class, [
                'label' => 'I would like to change my password',
                'attr' => ['class' => 'btn btn-outline-danger']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
