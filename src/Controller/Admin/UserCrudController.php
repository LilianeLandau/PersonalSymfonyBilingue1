<?php
// src/Controller/Admin/UserCrudController.php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Contrôleur CRUD pour la gestion des utilisateurs
 */
class UserCrudController extends AbstractCrudController
{
    private UserPasswordHasherInterface $passwordHasher;

    /**
     * Constructeur pour l'injection du service de hachage de mot de passe
     */
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Retourne la classe de l'entité gérée
     */
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    /**
     * Configuration du CRUD
     */
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Utilisateur')
            ->setEntityLabelInPlural('Utilisateurs')
            ->setPageTitle('index', 'Liste des utilisateurs')
            ->setPageTitle('new', 'Créer un utilisateur')
            ->setPageTitle('edit', "Modifier l'utilisateur")
            ->setPageTitle('detail', "Détails de l'utilisateur");
    }

    /**
     * Configuration des champs du formulaire
     */
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            EmailField::new('email', 'Email'),
            TextField::new('password', 'Mot de passe')
                ->setFormType(PasswordType::class)
                ->onlyOnForms()
                ->setRequired($pageName === Crud::PAGE_NEW),
            ChoiceField::new('roles', 'Rôles')
                ->setChoices([
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN'
                ])
                ->allowMultipleChoices()
                ->renderExpanded()
        ];
    }

    /**
     * Configuration des actions
     */
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel('Ajouter un utilisateur');
            });
    }

    /**
     * Traitement avant persistance pour hasher le mot de passe
     */
    public function persistEntity($entityManager, $entityInstance): void
    {
        $this->hashPassword($entityInstance);
        parent::persistEntity($entityManager, $entityInstance);
    }

    /**
     * Traitement avant mise à jour pour hasher le mot de passe si modifié
     */
    public function updateEntity($entityManager, $entityInstance): void
    {
        $this->hashPassword($entityInstance);
        parent::updateEntity($entityManager, $entityInstance);
    }

    /**
     * Méthode privée pour hasher le mot de passe de l'utilisateur
     */
    private function hashPassword(User $user): void
    {
        // Ne hasher que si un mot de passe est fourni
        if ($user->getPassword() && $user->getPassword() !== '') {
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($hashedPassword);
        }
    }
}
