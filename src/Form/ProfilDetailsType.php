<?php

namespace App\Form;

use App\Entity\User;
use Mael\MaelRecaptchaBundle\Type\MaelRecaptchaSubmitType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfilDetailsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, ['required' => true])
            ->add('first_name', null, ['required' => true])
            ->add('last_name', null, ['required' => true])
            ->add('birthday', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'attr' => [
                    'placeholder' => 'Select a your birthday',
                    'class' => 'flatpickr'
                ]
            ])
            ->add('sexe', ChoiceType::class, [
                'choices' => $this->getChoices(),
            ])
            ->add('avatarFile', FileType::class, [
                'required' => true,
            ])
            ->add('bannerFile', FileType::class, [
                'required' => true,
            ])
            ->add('captcha', MaelRecaptchaSubmitType::class, [
                'label' => 'Save',
                'attr' => ['class' => 'btn btn-primary']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
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
