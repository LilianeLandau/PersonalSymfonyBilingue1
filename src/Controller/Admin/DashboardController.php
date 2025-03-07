<?php
// src/Controller/Admin/AdminDashboardController.php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Product;
use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur principal du tableau de bord d'administration
 * Accessible uniquement aux utilisateurs avec le rôle ROLE_ADMIN
 */
//ATTENTION ICI - ENLEVER LE COMMENTAIRE POUR ACTIVER LA SECURITE
#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractDashboardController
{
    /**
     * Route principale pour accéder au tableau de bord
     */
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // Option 1 : Vous pouvez rendre une template personnalisée
        return $this->render('admin/dashboard.html.twig');

        // Option 2 : Rediriger vers une page de CRUD (comme recommandé par EasyAdmin)
        //  $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        //  return $this->redirect(
        //     $adminUrlGenerator
        //      ->setController(UserCrudController::class)
        //      ->generateUrl()
        // );
    }

    /**
     * Configuration du tableau de bord
     */
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Administration')
            ->setFaviconPath('favicon.svg')
            ->renderContentMaximized() // Utiliser tout l'espace disponible
            ->renderSidebarMinimized(); // Sidebar minimisée par défaut
    }

    /**
     * Configuration des éléments du menu
     */
    public function configureMenuItems(): iterable
    {
        // Lien vers le tableau de bord
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        // Gestion des utilisateurs
        yield MenuItem::section('Utilisateurs');
        yield MenuItem::linkToCrud('Utilisateurs', 'fa fa-user', User::class);

        // Gestion du catalogue
        yield MenuItem::section('Catalogue');
        yield MenuItem::linkToCrud('Catégories', 'fa fa-tags', Category::class);
        yield MenuItem::linkToCrud('Produits', 'fa fa-box', Product::class);

        // Lien vers le site principal
        yield MenuItem::section('Site');
        yield MenuItem::linkToRoute('Retour au site', 'fa fa-arrow-left', 'home');

        // Lien de déconnexion
        yield MenuItem::section();
        //ATTENTION ICI - ENLEVER LE COMMENTAIRE POUR ACTIVER LA SECURITE
        yield MenuItem::linkToLogout('Déconnexion', 'fa fa-sign-out');
    }
}
