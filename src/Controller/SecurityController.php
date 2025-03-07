<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Contrôleur pour la gestion de l'authentification
 */
class SecurityController extends AbstractController
{
    private RequestStack $requestStack;

    // Injection de dépendance pour obtenir la langue courante
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * Page de connexion
     */
    // #[Route('/login', name: 'app_login')]
    //#[Route('/{_locale}/login', name: 'app_login')]
    #[Route('/{_locale}/login', name: 'app_login', requirements: ['_locale' => 'fr|en'])]    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Si l'utilisateur est déjà connecté
        if ($this->getUser()) {
            // Vérifier si l'utilisateur a le rôle ROLE_ADMIN
            if (in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
                // Récupérer la langue courante
                $locale = $this->requestStack->getCurrentRequest()->getLocale(); // Récupère la langue de la requête
                // Rediriger vers la page admin dans la bonne langue
                //   return $this->redirectToRoute('admin.' . $locale);

                return $this->redirectToRoute('admin', ['_locale' => $locale]);
            }

            // Si l'utilisateur n'est pas un admin, rediriger vers la page d'accueil
            $locale = $this->requestStack->getCurrentRequest()->getLocale(); // Récupère la langue de la requête
            //return $this->redirectToRoute('home.' . $locale);
            return $this->redirectToRoute('home', ['_locale' => $locale]);
        }

        // Récupérer l'erreur de connexion s'il y en a une
        $error = $authenticationUtils->getLastAuthenticationError();

        // Dernier nom d'utilisateur saisi
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * Route de déconnexion
     */
    /// #[Route('/logout', name: 'app_logout')]
    // #[Route('/{_locale}/logout', name: 'app_logout')]
    #[Route('/{_locale}/logout', name: 'app_logout', requirements: ['_locale' => 'fr|en'])]
    public function logout(): void
    {
        // Cette méthode peut rester vide - Symfony gère la déconnexion
    }
}
