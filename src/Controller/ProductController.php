<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/', name: 'home_redirect')]
    public function redirectToLocale(): Response
    {
        // Redirige vers la page d'accueil avec "fr" par défaut
        return $this->redirectToRoute('home', ['_locale' => 'fr']);
    }

    #[Route('/{_locale}/home', name: 'home', requirements: ['_locale' => 'fr|en'])]
    public function home(Request $request): Response
    {
        // Récupérer la locale depuis la requête
        $locale = $request->getLocale();

        return $this->render('product/home.html.twig', [
            'current_locale' => $locale
        ]);
    }

    #[Route('/{_locale}/product', name: 'product_index', requirements: ['_locale' => 'fr|en'])]
    public function index(ProductRepository $productRepository, Request $request): Response
    {
        $locale = $request->getLocale();
        $products = $productRepository->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'current_locale' => $locale
        ]);
    }
}
