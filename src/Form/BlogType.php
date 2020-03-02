<?php

namespace App\Form;

use App\Entity\Blog;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('content')
            ->add('slug')
            ->add('image')
            ->add('author')
            ->add('active')
        ;
        /**
         * Si champs avec un choix et constante défini utiliser la méthode getchoices
         * exemple : $builder->add('categories', ChoicesTypes::class, ['choices' => $this->getChoices())
         */
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Blog::class,
            'translation_domain' => 'formsBlog'
        ]);
    }

    /**private function getChoices() {
        $choices = Blog::CONSTANTE A DEFINIR;
        $output = [];
        foreach ($choices as $k => $v) {
            $output[$v] => $k;
        }
        return $output;
    }
    */
}
