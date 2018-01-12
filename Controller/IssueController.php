<?php

namespace Valouleloup\IssueBundle\Controller;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Converter;
use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\HtmlRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Valouleloup\IssueBundle\Entity\Issue;
use Valouleloup\IssueBundle\Entity\Post;
use Valouleloup\IssueBundle\Entity\Tag;
use Valouleloup\IssueBundle\Entity\Theme;
use Valouleloup\IssueBundle\Form\ElasticType;
use Valouleloup\IssueBundle\Form\IssueType;
use Valouleloup\IssueBundle\Form\PostType;
use Webuni\CommonMark\TableExtension\TableExtension;

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

        $environment = Environment::createCommonMarkEnvironment();
        $environment->addExtension(new TableExtension());

        $converter = new Converter(new DocParser($environment), new HtmlRenderer($environment));

        foreach ($issue->getPosts() as $post) {
            $post->setBody($converter->convertToHtml($post->getBody()));
        }

        $issue->setBody($converter->convertToHtml($issue->getBody()));

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
            $issue->setUpdatedAt(new \DateTime('now'));

            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            $this->get('val_issue.component.elastic.post.manager')->indexPost($post);

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

        return $this->renderList($issues);
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

        return $this->renderList($issues);
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

        return $this->renderList($issues);
    }

    /**
     * @param $terms
     *
     * @return Response
     */
    public function listElasticAction($terms)
    {
        $repo = $this->getDoctrine()->getRepository('ValouleloupIssueBundle:Issue');

        $postIds = $this->get('val_issue.component.elastic.post.manager')->getPosts($terms);

        $issues = $repo->findByListId($postIds);

        return $this->renderList($issues);
    }

    /**
     * @return Response
     */
    public function listSearchAction(Request $request)
    {
        $form = $this->createForm(ElasticType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->get('search')->getData();

            return $this->redirectToRoute('list_issue_elastic', ['terms' => $search]);
        }

        return $this->redirectToRoute('list_issue');
    }

    /**
     * @param $issues
     *
     * @return Response
     */
    private function renderList($issues)
    {
        $form = $this->createForm(ElasticType::class, null, [
            'action' => $this->generateUrl('list_issue_search'),
            'method' => 'POST',
        ]);

        $paginator = $this->get('val_issue.component.pagination.paginator');
        $issues    = $paginator->paginate($issues, 5);

        return $this->render('@ValouleloupIssue/Issue/list.html.twig', [
            'issues' => $issues,
            'form' => $form->createView(),
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

            $this->get('val_issue.component.elastic.post.manager')->indexIssue($issue);

            $this->addFlash('success', 'flash.issue.create');

            return $this->redirectToRoute('list_issue');
        }

        return $this->render('@ValouleloupIssue/Issue/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}