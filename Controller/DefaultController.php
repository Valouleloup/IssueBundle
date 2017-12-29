<?php

namespace Valouleloup\IssueBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('@ValouleloupIssue/Default/index.html.twig');
    }
}
