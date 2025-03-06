<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


use Symfony\Component\HttpFoundation\RequestStack;


class ProductController extends AbstractController
{

    //pour le dashboard, permet un retour à l'accueil

    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    //ROUTE / 
    // RACINE REDIRIGE VERS LA PAGE D'ACCUEIL
    #[Route('/', name: 'home_redirect')]
    public function redirectToLocale(): Response
    {
        // Redirige vers la page d'accueil avec "fr" par défaut
        //template : aucun template
        return $this->redirectToRoute('home', ['_locale' => 'fr']);
    }

    //PAGE D'ACCUEIL
    //ROUTE /{_locale}/home
    //template : product/home.html.twig
    //paramètres de route : _locale: doit être 'fr' ou 'en'
    //nome de la route : home
    #[Route('/{_locale}/home', name: 'home', requirements: ['_locale' => 'fr|en'])]
    public function home(Request $request): Response
    {
        // Récupérer la locale depuis la requête
        $locale = $request->getLocale();

        //template : product/home.html.twig
        //paramètres de route : _locale: doit être 'fr' ou 'en'
        //la variable $locale est passée au template sous le nom de 'current_locale' 
        return $this->render('product/home.html.twig', [
            'current_locale' => $locale
        ]);
    }


    //PAGE DE LISTE DES PRODUITS
    //ROUTE /{_locale}/product
    //template : product/index.html.twig
    //paramètres de route : _locale: doit être 'fr' ou 'en'
    //nome de la route : product_index
    #[Route('/{_locale}/product', name: 'product_index', requirements: ['_locale' => 'fr|en'])]
    public function index(ProductRepository $productRepository, Request $request): Response
    {
        $locale = $request->getLocale();
        $products = $productRepository->findAll();

        //template : product/index.html.twig
        //paramètres de route : _locale: doit être 'fr' ou 'en'
        return $this->render('product/index.html.twig', [
            'products' => $products,
            'current_locale' => $locale
        ]);
    }
}
