<?php

namespace Valouleloup\IssueBundle\Component\Elasticsearch\Post;


use Elasticsearch\Client;
use Valouleloup\IssueBundle\Entity\Issue;
use Valouleloup\IssueBundle\Entity\Post;

class PostManager
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param Issue $issue
     */
    public function indexIssue(Issue $issue)
    {
        $params = [
            'index' => 'posts',
            'type'  => 'issue',
            'id'    => $issue->getId(),
            'body'  => [ 'title' => $issue->getLabel(), 'body' => $issue->getBody()]
        ];

        $this->client->index($params);
    }

    /**
     * @param Post $post
     */
    public function indexPost(Post $post)
    {
        $params = [
            'index' => 'posts',
            'type'  => 'post',
            'id'    => $post->getId(),
            'body'  => [ 'body' => $post->getBody()]
        ];

        $this->client->index($params);
    }

    /**
     * @param string $terms
     *
     * @return array
     */
    public function getPosts($terms)
    {
        $params = [
            'scroll' => '2m',
            'index' => 'posts',
            'type' => 'issue',
            'body' => [
                'query' => [
                    'wildcard' => [
                        'title' => '*' . $terms . '*'
                    ]
                ]
            ]
        ];

        $postIds = [];

        $results = $this->client->search($params);

        while (isset($results['hits']['hits']) && count($results['hits']['hits']) > 0) {
            foreach ($results['hits']['hits'] as $post) {
                $postIds[] = $post['_id'];
            }

            $results = $this->client->scroll([
                'scroll'    => '2m',
                'scroll_id' => $results['_scroll_id'],
            ]);
        }

        return $postIds;
    }

    public function dropIndex()
    {
        $params = [
            'index' => 'posts'
        ];

        $this->client->indices()->delete($params);
    }
}