<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // $builder
        //     ->add('email')
        //     // ->add('roles')
        //     ->add('password')
        //     ->add('lastName')
        //     ->add('firstName')
        //     ->add('adress')
        //     ->add('city')
        //     ->add('zipCode')
        // ;



        $builder
            ->add('email', EmailType::class, [
                'label' => 'Votre adresse e-mail',
                'attr' => [
                    'placeholder' => 'Votre adresse e-mail'
                ]
            ])
            // ->add('password', PasswordType::class, [
            //     'label' => 'Votre mot de passe',
            //     'attr' => [
            //         'placeholder' => 'Votre mot de passe'
            //     ]
            // ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => [ 
                    'attr' => [
                        'placeholder' => 'Votre mot de passe'
                    ]
                ],
                'second_options'  => [ 
                    'attr' => [
                        'placeholder' => 'Confirmez votre mot de passe'
                    ]
                ],
                // 'second_options' => ['label' => 'Confirmer le mot de passe'],
            ])
            ->add('lastName', null, [
                'label' => 'Votre nom',
                'attr' => [
                    'placeholder' => 'Votre nom'
                ]
            ])
            ->add('firstName', null, [
                'label' => 'Votre prénom',
                'attr' => [
                    'placeholder' => 'Votre prénom'
                ]
            ])
            ->add('adress', null, [
                'label' => 'Votre adresse',
                'attr' => [
                    'placeholder' => 'Votre adresse'
                ]
            ])
            ->add('city', null, [
                'label' => 'Votre Ville',
                'attr' => [
                    'placeholder' => 'Votre Ville'
                ]
            ])
            ->add('zipCode', null, [
                'label' => 'Votre code postal',
                'attr' => [
                    'placeholder' => 'Votre code postal'
                ]
            ])
        ;    








    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
