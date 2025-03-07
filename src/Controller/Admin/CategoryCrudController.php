<?php
// src/Controller/Admin/CategoryCrudController.php

namespace App\Controller\Admin;

use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

/**
 * Contrôleur CRUD pour la gestion des catégories
 */
class CategoryCrudController extends AbstractCrudController
{
    /**
     * Retourne la classe de l'entité gérée
     */
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    /**
     * Configuration du CRUD
     */
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Catégorie')
            ->setEntityLabelInPlural('Catégories')
            ->setPageTitle('index', 'Liste des catégories')
            ->setPageTitle('new', 'Créer une catégorie')
            ->setPageTitle('edit', 'Modifier la catégorie')
            ->setPageTitle('detail', 'Détails de la catégorie')
            ->setDefaultSort(['id' => 'DESC']);
    }

    /**
     * Configuration des champs du formulaire
     */
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('titleFr', 'Titre (Français)'),
            TextField::new('titleEn', 'Titre (Anglais)'),
            AssociationField::new('products', 'Produits')
                ->hideOnForm()
                ->formatValue(function ($value, $entity) {
                    return count($entity->getProducts()) . ' produit(s)';
                })
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
                return $action->setLabel('Ajouter une catégorie');
            });
    }
}
