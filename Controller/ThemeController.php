<?php

namespace Valouleloup\IssueBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Valouleloup\IssueBundle\Entity\Theme;
use Valouleloup\IssueBundle\Form\ThemeType;

class ThemeController extends Controller
{
    /**
     * @return Response
     */
    public function newAction()
    {
        $theme = new Theme();

        $form = $this->createForm(
            ThemeType::class,
            $theme,
            [
                'action' => $this->generateUrl('create_theme'),
                'method' => 'POST',
            ]
        );

        return $this->render('@ValouleloupIssue/Theme/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $theme = new Theme();

        $form = $this->createForm(ThemeType::class, $theme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($theme);
            $em->flush();

            $this->addFlash('success', 'flash.theme.create');

            return $this->redirectToRoute('list_themes');
        }

        return $this->render('@ValouleloupIssue/Theme/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return Response
     */
    public function listAction()
    {
        $repo = $this->getDoctrine()->getRepository('ValouleloupIssueBundle:Theme');
        $themes = $repo->findAll();

        return $this->render('@ValouleloupIssue/Theme/list.html.twig', [
            'themes' => $themes,
        ]);
    }
}