<?php

namespace App\Security;

use App\Entity\GuestUser;
use Override;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;


class GuestAuthenticator extends AbstractAuthenticator
{

    use TargetPathTrait;
    #[Override]
    public function supports(Request $request): ?bool
    {
        return $request->isMethod('POST');
    }

    #[Override]
    public function authenticate(Request $request): Passport
    {
        $email = $request->getPayload()->get('email');

//        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
//            throw new AuthenticationException('Invalid email provided.');
//        }

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $request->getPayload()->get('email'));


        return new Passport(
            new UserBadge($email,function ($userIdentifier) {
                // Create a new GuestUser dynamically
                return new GuestUser($userIdentifier);
            }),
            new CustomCredentials(function () {
                // You can skip any additional validation here
                return true;
            }, null), // Null as we don't have a traditional "user object" or password
        );
    }

    #[Override]
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Set the authenticated token in the session
        $request->getSession()->set('_security_' . $firewallName, serialize($token));
        $request->getSession()->save();

        return null;
    }

    #[Override]
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new Response($exception->getMessage(), 401);
    }
}