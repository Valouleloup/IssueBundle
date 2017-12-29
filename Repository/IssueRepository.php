<?php

namespace Valouleloup\IssueBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Valouleloup\IssueBundle\Entity\Tag;
use Valouleloup\IssueBundle\Entity\Theme;

class IssueRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function findAllMostRecent()
    {
        $qb = $this->createQueryBuilder('i')
            ->orderBy('i.updatedAt', 'DESC')
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @return array
     */
    public function findByTag(Tag $tag)
    {
        $qb = $this->createQueryBuilder('i')
            ->leftJoin('i.tags', 't')
            ->where('t.id = :tag')
            ->setParameter('tag', $tag->getId())
            ->orderBy('i.updatedAt', 'DESC')
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @return array
     */
    public function findByTheme(Theme $theme)
    {
        $qb = $this->createQueryBuilder('i')
            ->leftJoin('i.theme', 't')
            ->where('t.id = :theme')
            ->setParameter('theme', $theme->getId())
            ->orderBy('i.updatedAt', 'DESC')
        ;

        return $qb->getQuery()->getResult();
    }
}