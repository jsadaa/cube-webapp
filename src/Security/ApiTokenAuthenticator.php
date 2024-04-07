<?php

namespace App\Security;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;


class ApiTokenAuthenticator extends AbstractAuthenticator
{
    use TargetPathTrait;

    private $httpClient;
    private $urlGenerator;
    private $requestStack;

    public function __construct(HttpClientInterface $httpClient, UrlGeneratorInterface $urlGenerator, RequestStack $requestStack)
    {
        $this->httpClient = $httpClient;
        $this->urlGenerator = $urlGenerator;
        $this->requestStack = $requestStack;
    }

    public function supports(Request $request): ?bool
    {
        return $request->getPathInfo() === '/login' && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
        $credentials = $request->request->all();
        $email = $credentials['email'] ?? '';
        $password = $credentials['password'] ?? '';

        // Appeler l'API d'authentification et valider les credentials
        $response = $this->httpClient->request('POST', 'http://localhost:5273/api/auth/login', [
            'json' => [
                'email' => $email,
                'password' => $password,
            ],
        ]);

        if (200 !== $response->getStatusCode()) {
            $this->requestStack->getSession()->set('last_username', $email);
            throw new AuthenticationException('Les informations d\'identification ne sont pas valides.');
        }

        $data = $response->toArray();

        // Stockez les données de l'utilisateur dans la session
        $this->requestStack->getSession()->set('user', [
            'id' => $data['userId'],
            'email' => $email,
            'accessToken' => $data['accessToken'],
            'refreshToken' => $data['refreshToken'],
        ]);

        // Créer le UserBadge avec l'email uniquement
        $userBadge = new UserBadge($email);

        // Utilisez CustomCredentials sans validation spécifique puisque l'API a déjà validé l'utilisateur
        $customCredentials = new CustomCredentials(function ($credentials, $user) {
            return true; // Les credentials sont considérés comme valides car l'API a retourné un succès
        }, $credentials);

        return new Passport($userBadge, $customCredentials);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // Rediriger vers la page d'accueil ou autre après la connexion réussie
        return new RedirectResponse($this->urlGenerator->generate('home'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        // Stockez le message d'erreur dans la session pour pouvoir l'afficher plus tard
        $request->getSession()->set('auth_error', $exception->getMessage());
        
        return new RedirectResponse($this->urlGenerator->generate('login'));
    }
}

