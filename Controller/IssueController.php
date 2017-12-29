<?php

namespace Valouleloup\IssueBundle\Controller;

use League\CommonMark\CommonMarkConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Valouleloup\IssueBundle\Entity\Issue;
use Valouleloup\IssueBundle\Entity\Post;
use Valouleloup\IssueBundle\Entity\Tag;
use Valouleloup\IssueBundle\Entity\Theme;
use Valouleloup\IssueBundle\Form\IssueType;
use Valouleloup\IssueBundle\Form\PostType;

class IssueController extends Controller
{
    public function showAction(Issue $issue)
    {
        $post = new Post();

        $form = $this->createForm(
            PostType::class,
            $post,
            [
                'action' => $this->generateUrl('show_issue_new_post', ['id' => $issue->getId()]),
                'method' => 'POST',
            ]
        );

        $mark = new CommonMarkConverter();

        foreach ($issue->getPosts() as $post) {
            $post->setBody($mark->convertToHtml($post->getBody()));
        }

        $issue->setBody($mark->convertToHtml($issue->getBody()));

        return $this->render('@ValouleloupIssue/Issue/show.html.twig', [
            'issue' => $issue,
            'form' => $form->createView(),
        ]);
    }

    public function showNewPostAction(Request $request, Issue $issue)
    {
        $post = new Post();
        $post->setIssue($issue);
        $post->setAuthor($this->getUser());

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            $this->addFlash('success', 'flash.post.create');

            return $this->redirectToRoute('show_issue', ['id' => $issue->getId()]);
        }

        return $this->render('@ValouleloupIssue/Issue/show.html.twig', [
            'issue' => $issue,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return Response
     */
    public function listAction()
    {
        $repo = $this->getDoctrine()->getRepository('ValouleloupIssueBundle:Issue');
        $issues = $repo->findAllMostRecent();

        return $this->render('@ValouleloupIssue/Issue/list.html.twig', [
            'issues' => $issues,
        ]);
    }

    /**
     * @param Tag $tag
     *
     * @return Response
     */
    public function listTagAction(Tag $tag)
    {
        $repo = $this->getDoctrine()->getRepository('ValouleloupIssueBundle:Issue');
        $issues = $repo->findByTag($tag);

        return $this->render('@ValouleloupIssue/Issue/list.html.twig', [
            'issues' => $issues,
        ]);
    }

    /**
     * @param Theme $theme
     *
     * @return Response
     */
    public function listThemeAction(Theme $theme)
    {
        $repo = $this->getDoctrine()->getRepository('ValouleloupIssueBundle:Issue');
        $issues = $repo->findByTheme($theme);

        return $this->render('@ValouleloupIssue/Issue/list.html.twig', [
            'issues' => $issues,
        ]);
    }

    /**
     * @return Response
     */
    public function newAction()
    {
        $issue = new Issue();

        $form = $this->createForm(
            IssueType::class,
            $issue,
            [
                'action' => $this->generateUrl('create_issue'),
                'method' => 'POST',
            ]
        );

        return $this->render('@ValouleloupIssue/Issue/add.html.twig', [
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
        $issue = new Issue();
        $issue->setAuthor($this->getUser());

        $form = $this->createForm(IssueType::class, $issue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($issue);
            $em->flush();

            $this->addFlash('success', 'flash.issue.create');

            return $this->redirectToRoute('list_issue');
        }

        return $this->render('@ValouleloupIssue/Issue/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}