<?php

namespace Valouleloup\IssueBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Valouleloup\IssueBundle\Entity\Tag;
use Valouleloup\IssueBundle\Form\TagType;

class TagController extends Controller
{
    public function listAction()
    {
        $repo = $this->getDoctrine()->getRepository('ValouleloupIssueBundle:Tag');
        $tags = $repo->findAll();

        return $this->render('@ValouleloupIssue/Tag/list.html.twig', [
            'tags' => $tags,
        ]);
    }

    /**
     * @return Response
     */
    public function newAction()
    {
        $tag = new Tag();

        $form = $this->createForm(
            TagType::class,
            $tag,
            [
                'action' => $this->generateUrl('create_tag'),
                'method' => 'POST',
            ]
        );

        return $this->render('@ValouleloupIssue/Tag/add.html.twig', [
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
        $tag = new Tag();

        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($tag);
            $em->flush();

            $this->addFlash('success', 'flash.tag.create');

            return $this->redirectToRoute('list_tags');
        }

        return $this->render('@ValouleloupIssue/Tag/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}