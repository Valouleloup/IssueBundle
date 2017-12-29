<?php

namespace Valouleloup\IssueBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Valouleloup\IssueBundle\Entity\Post;
use Valouleloup\IssueBundle\Form\PostType;

class PostController extends Controller
{
    /**
     * @return Response
     */
    public function newAction()
    {
        $post = new Post();

        $form = $this->createForm(
            PostType::class,
            $post,
            [
                'action' => $this->generateUrl('create_post'),
                'method' => 'POST',
            ]
        );

        return $this->render('@ValouleloupIssue/Post/add.html.twig', [
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
        $post = new Post();

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            $this->addFlash('success', 'flash.post.create');

            return $this->redirectToRoute('valouleloup_issue_homepage');
        }

        return $this->render('@ValouleloupIssue/Post/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return Response
     */
    public function editAction(Post $post)
    {
        $form = $this->createForm(
            PostType::class,
            $post,
            [
                'action' => $this->generateUrl('update_post', ['id' => $post->getId()]),
                'method' => 'POST',
            ]
        );

        return $this->render('@ValouleloupIssue/Post/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function updateAction(Request $request, Post $post)
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('success', 'flash.post.update');

            return $this->redirectToRoute('show_issue', ['id' => $post->getIssue()->getId()]);
        }

        return $this->render('@ValouleloupIssue/Post/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}