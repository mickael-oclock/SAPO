<?php

namespace App\Security;

use App\CockpitApi\CockpitApiClient;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Guard\PasswordAuthenticatedInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class ApiAuthenticator extends AbstractFormLoginAuthenticator implements PasswordAuthenticatedInterface
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private $entityManager;
    private $urlGenerator;
    private $csrfTokenManager;
    private $passwordEncoder;
    private $cockpitApiClient;

    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->cockpitApiClient = new CockpitApiClient();
    }

    public function supports(Request $request)
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        $credentials = [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $credentials['email']]);

        if (!$user){
            $user = $this->importUserFromCockpit($credentials);
            if (!$user)  throw new CustomUserMessageAuthenticationException('Email could not be found.');
        }
        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        // we can check if LocalPassword still working
        if ($this->passwordEncoder->isPasswordValid($user, $credentials['password'])){
            return true;
        }else{
            // if not Check And Update CockpitCredential
            return $this->updateLocalCredentials($credentials,$user);
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function getPassword($credentials): ?string
    {
        return $credentials['password'];
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }
        return new RedirectResponse($this->urlGenerator->generate('test'));
    }

    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

    protected function importUserFromCockpit($credentials){
        $apiUser = $this->cockpitApiClient->auth($credentials['email'],$credentials['password']);
        if($apiUser){
            $user = new User();
            $user->setPassword($credentials['password']);
            $user->setEmail($apiUser->getEmail());
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            return $user;
        }
        return false;
    }
    protected function checkCockpitCredentials(array $credentials){
        return $this->cockpitApiClient->auth($credentials['email'],$credentials['password']);
    }
    protected  function updateLocalCredentials(array $credentials,UserInterface $user){
        if($this->checkCockpitCredentials($credentials)){
            $user->setPassword($credentials['password']);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            return true;
        }
        return false;
    }

}
