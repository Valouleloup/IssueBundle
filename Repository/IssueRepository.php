<?php

namespace Valouleloup\IssueBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Valouleloup\IssueBundle\Entity\Tag;
use Valouleloup\IssueBundle\Entity\Theme;

class IssueRepository extends EntityRepository
{
    /**
     * @return \Doctrine\ORM\Query
     */
    public function findAllMostRecent()
    {
        $qb = $this->createQueryBuilder('i')
            ->orderBy('i.updatedAt', 'DESC')
        ;

        return $qb->getQuery();
    }

    /**
     * @param Tag $tag
     *
     * @return \Doctrine\ORM\Query
     */
    public function findByTag(Tag $tag)
    {
        $qb = $this->createQueryBuilder('i')
            ->leftJoin('i.tags', 't')
            ->where('t.id = :tag')
            ->setParameter('tag', $tag->getId())
            ->orderBy('i.updatedAt', 'DESC')
        ;

        return $qb->getQuery();
    }

    /**
     * @param Theme $theme
     *
     * @return \Doctrine\ORM\Query
     */
    public function findByTheme(Theme $theme)
    {
        $qb = $this->createQueryBuilder('i')
            ->leftJoin('i.theme', 't')
            ->where('t.id = :theme')
            ->setParameter('theme', $theme->getId())
            ->orderBy('i.updatedAt', 'DESC')
        ;

        return $qb->getQuery();
    }

    /**
     * @param array $ids
     *
     * @return \Doctrine\ORM\Query
     */
    public function findByListId(array $ids)
    {
        $qb = $this->createQueryBuilder('i')
            ->where('i.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->orderBy('i.updatedAt', 'DESC')
        ;

        return $qb->getQuery();
    }
}