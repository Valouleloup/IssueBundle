<?php

namespace Valouleloup\IssueBundle\Component\Pagination;

use Doctrine\ORM\Query;
use Symfony\Component\HttpFoundation\RequestStack;

class Paginator
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @param Query $query
     * @param int $limit
     *
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function paginate(Query $query, $limit = 10)
    {
        $request = $this->requestStack->getCurrentRequest();

        $page = 1;

        if (!empty($request->get('page'))) {
            $page = $request->get('page');
        }

        $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator($query);
        $paginator
            ->getQuery()
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
        ;

        return $paginator;
    }
}