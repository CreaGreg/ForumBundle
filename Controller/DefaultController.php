<?php

namespace Cornichon\ForumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CornichonForumBundle:Default:index.html.twig', array('name' => $name));
    }
}
