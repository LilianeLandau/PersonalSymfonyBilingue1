<?php
// src/Controller/Admin/ProductCrudController.php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

/**
 * Contrôleur CRUD pour la gestion des produits
 */
class ProductCrudController extends AbstractCrudController
{
    /**
     * Retourne la classe de l'entité gérée
     */
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    /**
     * Configuration du CRUD
     */
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Produit')
            ->setEntityLabelInPlural('Produits')
            ->setPageTitle('index', 'Liste des produits')
            ->setPageTitle('new', 'Créer un produit')
            ->setPageTitle('edit', 'Modifier le produit')
            ->setPageTitle('detail', 'Détails du produit')
            ->setDefaultSort(['id' => 'DESC']);
    }

    /**
     * Configuration des champs du formulaire
     */
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('nameFr', 'Nom (Français)'),
            TextField::new('nameEn', 'Nom (Anglais)'),
            AssociationField::new('category', 'Catégorie')
                ->setFormTypeOption('placeholder', 'Sélectionnez une catégorie')
                ->setRequired(true)
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
                return $action->setLabel('Ajouter un produit');
            });
    }
}
