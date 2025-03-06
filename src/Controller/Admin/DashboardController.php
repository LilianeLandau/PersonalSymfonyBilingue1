<?php

namespace App\Controller\Admin;

// Importation des entités nécessaires
use App\Entity\User;
use App\Entity\Product;
use App\Entity\Category;

// Importation des classes EasyAdmin nécessaires
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

// Importation des classes Symfony nécessaires
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;

class DashboardController extends AbstractDashboardController
{
    // Propriété pour stocker le service de sécurité
    private Security $security;

    // Constructeur pour injecter le service de sécurité
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    // Route pour le tableau de bord d'administration
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {

        return $this->render('admin/dashboard.html.twig');
    }

    // Configuration du tableau de bord
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Administration')  // Définit le titre du tableau de bord
            ->setFaviconPath('favicon.svg');  // Définit le chemin du favicon
    }

    // Configuration des éléments du menu
    public function configureMenuItems(): iterable
    {
        // Lien vers le tableau de bord principal
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        // Menu pour les utilisateurs, accessible uniquement aux ROLE_ADMIN
        if ($this->security->isGranted('ROLE_ADMIN')) {
            yield MenuItem::section('Utilisateurs');
            yield MenuItem::linkToCrud('Utilisateurs', 'fa fa-users', User::class);
        }

        // Menu pour les produits et catégories
        yield MenuItem::section('Catalogue');
        yield MenuItem::linkToCrud('Catégories', 'fa fa-tags', Category::class);
        yield MenuItem::linkToCrud('Produits', 'fa fa-box', Product::class);

        // Lien pour retourner au site principal
        yield MenuItem::section('Navigation');
        yield MenuItem::linkToRoute('Retour au site', 'fa fa-arrow-left', 'home');
    }
}
