<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/', name: 'home_redirect')]
    public function redirectToLocale(Request $request): Response
    {
        // Rediriger vers la page d'accueil avec la locale par défaut (fr)
        return $this->redirectToRoute('product_index', ['_locale' => 'fr']);
    }

    // Afficher les produits sans utiliser {locale} dans l'URL (le préfixe est déjà géré par routes.yaml)
    #[Route('/product', name: 'product_index')]
    public function index(Request $request, ProductRepository $productRepository): Response
    {
        $locale = $request->getLocale();
        $products = $productRepository->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'locale' => $locale,
        ]);
    }
}
