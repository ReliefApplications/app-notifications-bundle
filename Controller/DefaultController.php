<?php

namespace Reliefapps\NotificationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('ReliefappsNotificationBundle:Default:index.html.twig');
    }
}
