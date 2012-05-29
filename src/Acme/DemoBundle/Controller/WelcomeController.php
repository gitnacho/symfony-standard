<?php

namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class WelcomeController extends Controller
{
    public function indexAction()
    {
        /*
         * Las acciones de la vista se pueden pintar usando el método render()
         * o la anotación @Template como se muestra en DemoController.
         *
         */
        return $this->render('AcmeDemoBundle:Welcome:index.html.twig');
    }
}
