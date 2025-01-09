<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;

class EventCookieListener
{
    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $email = $request->cookies->get('gallery_email');

        if ($email) {
            $request->attributes->set('gallery_email', $email);
        }
    }
}