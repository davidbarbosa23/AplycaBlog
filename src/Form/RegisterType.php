<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'translation_domain' => 'forms'
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Password',
                'translation_domain' => 'forms'
            ])
            ->add('name', TextType::class, [
                'label' => 'Name',
                'translation_domain' => 'forms'
            ])
            ->add('phone', TelType::class, [
                'label' => 'Phone',
                'translation_domain' => 'forms'
            ])
            ->add('register', SubmitType::class, [
                'label' => 'Register',
                'translation_domain' => 'forms'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
