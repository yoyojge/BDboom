<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Symfony\Component\Form\{FormBuilderInterface, FormEvent, FormEvents};


class UserCrudController extends AbstractCrudController
{
    public function __construct(
        public UserPasswordHasherInterface $userPasswordHasher
    ) {}
    
    
    
    
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    private function addPasswordEventListener(FormBuilderInterface $formBuilder): FormBuilderInterface
    {
        return $formBuilder->addEventListener(FormEvents::POST_SUBMIT, $this->hashPassword());
    }

    public function configureFields(string $pageName): iterable
    {
        
        $fields = [
            TextField::new('lastName'),
            TextField::new('firstName'),          
            EmailField::new('email'),
            TextField::new('adress'),
            TextField::new('city'),
            TextField::new('zipCode'),
             // TextEditorField::new('role'),
        ];

        $password = TextField::new('password')
            // ->setFormType(RepeatedType::class)
            ->setFormTypeOptions([
                'type' => PasswordType::class,
                // 'first_options' => ['label' => 'Password'],
                // 'second_options' => ['label' => '(Repeat)'],
                // 'mapped' => false,
            ])
            // ->setRequired($pageName === Crud::PAGE_NEW)
            // ->onlyOnForms()
        ;

        $fields[] = $password;

        return $fields;       
        
        
    }

    private function hashPassword() {
        return function($event) {
            $form = $event->getForm();
            if (!$form->isValid()) {
                return;
            }
            $password = $form->get('password')->getData();
            if ($password === null) {
                return;
            }

            $hash = $this->userPasswordHasher->hashPassword($this->getUser(), $password);
            $form->getData()->setPassword($hash);
        };
    }
    
}
