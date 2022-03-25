<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class UserAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private UrlGeneratorInterface $urlGenerator;
    private UserRepository $user;
    private $params;
    private FlashBagInterface $flashBagInterface;


    public function __construct(UrlGeneratorInterface $urlGenerator, UserRepository $user, ContainerBagInterface $params, FlashBagInterface $flashBagInterface)
    {
        $this->urlGenerator = $urlGenerator;
        $this->user = $user;
        $this->params = $params;
        $this->flashBagInterface = $flashBagInterface;
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');

        $request->getSession()->set(Security::LAST_USERNAME, $email);

        // Check user status
        $userConnected = $this->user->findOneBy(['email' => $email]);
        if ($userConnected) {
            $userStatus = $this->user->checkStatus($userConnected);
            if ($userStatus !== "") {
                throw new CustomUserMessageAuthenticationException($userStatus);
            }
        }

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        date_default_timezone_set($this->params->get('app.timezone'));
        $userConnected = $this->user->findOneBy(['email' => $token->getUserIdentifier()]);
        $userConnected->setLastConnectionDate(new \DateTime('now'));
        $this->user->add($userConnected);
        return new RedirectResponse($this->urlGenerator->generate('index'));

    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
