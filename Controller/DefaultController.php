<?php

namespace Reliefapps\NotificationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('ReliefappsNotificationBundle:Default:index.html.twig');
    }

    /**
    * @Get("/send")
    */
    public function testSendAction(){
      $users = $this->getDoctrine()->getRepository('RwNotificationsBundle:User')->findAll();
      $notificationDispatcher = $this->container->get("Notification_Dispatcher");
      $notificationDispatcher->sendNotificationtoUser($users, array("title" => "Désolé pour les notifs, je fais mes tests !<br />Monique, de la compta"));
      $response = new Response();
      $response->setContent("Test");
      return $response;
    }
}
