<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class GuestController extends AbstractController
{
    #[Route('/guest/login', name: 'app_guest_login', methods: ['POST'])]
    public function guestLogin(Request $request): Response
    {
        $json = (array) json_decode($request->getContent());
        $email = $json['email'];

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new Response('Invalid email.', 400);
        }

        return new Response('Login triggered. Check for authentication success.');
    }
}