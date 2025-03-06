<?php

namespace App\Controller\Admin;

// Importations nécessaires pour le fonctionnement du contrôleur
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
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Contrôleur CRUD pour gérer les utilisateurs dans l'interface d'administration.
 * Étend AbstractCrudController pour bénéficier des fonctionnalités de base d'EasyAdmin.
 */
class UserCrudController extends AbstractCrudController
{
    /**
     * Service pour le hachage des mots de passe.
     */
    private UserPasswordHasherInterface $passwordHasher;

    /**
     * Constructeur pour injecter le service de hachage des mots de passe.
     *
     * @param UserPasswordHasherInterface $passwordHasher Service de hachage des mots de passe
     */
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Retourne le FQCN (Fully Qualified Class Name) de l'entité gérée par ce contrôleur.
     *
     * @return string Le FQCN de l'entité User
     */
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    /**
     * Configure les options générales du CRUD pour l'entité User.
     *
     * @param Crud $crud L'objet de configuration du CRUD
     * @return Crud L'objet de configuration modifié
     */
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Utilisateur')
            ->setEntityLabelInPlural('Utilisateurs')
            ->setPageTitle('index', 'Liste des utilisateurs')
            ->setPageTitle('new', 'Créer un utilisateur')
            ->setPageTitle('edit', 'Modifier un utilisateur')
            ->setPageTitle('detail', 'Détails de l\'utilisateur')
            ->setDefaultSort(['id' => 'DESC']); // Tri par défaut par ID décroissant
    }

    /**
     * Configure les champs à afficher/éditer pour l'entité User.
     *
     * @param string $pageName Le nom de la page en cours (ex: 'index', 'detail', 'edit', 'new')
     * @return iterable Un itérable de champs configurés
     */
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->hideOnForm(); // Cache le champ ID dans les formulaires

        yield EmailField::new('email')
            ->setLabel('Email');

        // Champ mot de passe, caché dans la liste et les détails
        yield TextField::new('password')
            ->setLabel('Mot de passe')
            ->setFormType(PasswordType::class)
            ->hideOnIndex()
            ->hideOnDetail()
            ->setRequired($pageName === Crud::PAGE_NEW); // Obligatoire uniquement à la création

        // Champ de rôles, avec des options prédéfinies
        yield ChoiceField::new('roles')
            ->setLabel('Rôles')
            ->setChoices([
                'Utilisateur' => 'ROLE_USER',
                'Administrateur' => 'ROLE_ADMIN'
            ])
            ->allowMultipleChoices()
            ->renderExpanded();
    }

    /**
     * Configure les filtres disponibles pour la liste des utilisateurs.
     *
     * @param Filters $filters L'objet de configuration des filtres
     * @return Filters L'objet de configuration des filtres modifié
     */
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('email')
            ->add(ChoiceFilter::new('roles')->setChoices([
                'Utilisateur' => 'ROLE_USER',
                'Administrateur' => 'ROLE_ADMIN'
            ]));
    }

    /**
     * Configure les actions disponibles pour l'entité User.
     *
     * @param Actions $actions L'objet de configuration des actions
     * @return Actions L'objet de configuration des actions modifié
     */
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setIcon('fa fa-plus')->setLabel('Ajouter un utilisateur');
            })
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action->setIcon('fa fa-edit')->setLabel('Modifier');
            })
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action->setIcon('fa fa-trash')->setLabel('Supprimer');
            });
    }

    /**
     * Méthode appelée avant la persistance d'une nouvelle entité User.
     * Hache le mot de passe avant de sauvegarder l'utilisateur.
     *
     * @param EntityManagerInterface $entityManager Le gestionnaire d'entités
     * @param mixed $entityInstance L'instance de l'entité à persister (ici, un User)
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /** @var User $user */
        $user = $entityInstance;

        if (!empty($user->getPassword())) {
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($hashedPassword);
        }

        parent::persistEntity($entityManager, $user);
    }

    /**
     * Méthode appelée avant la mise à jour d'une entité User existante.
     * Hache le nouveau mot de passe si fourni, sinon conserve l'ancien.
     *
     * @param EntityManagerInterface $entityManager Le gestionnaire d'entités
     * @param mixed $entityInstance L'instance de l'entité à mettre à jour (ici, un User)
     */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /** @var User $user */
        $user = $entityInstance;

        // Si un nouveau mot de passe a été défini
        if (!empty($user->getPassword())) {
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($hashedPassword);
        } else {
            // Sinon, récupérer le mot de passe existant dans la base de données
            $originalUser = $entityManager->getUnitOfWork()->getOriginalEntityData($user);
            $user->setPassword($originalUser['password']);
        }

        parent::updateEntity($entityManager, $user);
    }
}
