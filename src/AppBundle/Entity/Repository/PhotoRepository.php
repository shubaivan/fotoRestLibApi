<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Photo;
use AppBundle\Entity\Repository\RepoInterface\PhotoRepositoryInterface;
use Doctrine\ORM\EntityRepository;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * PhotoRepository
 */
class PhotoRepository extends EntityRepository implements PhotoRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function removeEntityFlush(Photo $entity)
    {
        $this->removeEntity($entity);
        $this->flushEntity();
    }

    /**
     * {@inheritdoc}
     */
    public function removeEntity(Photo $entity)
    {
        $this->_em->remove($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function postEntity(Photo $entity)
    {
        $this->persistEntity($entity);
        $this->flushEntity();
    }

    /**
     * {@inheritdoc}
     */
    public function persistEntity(Photo $entity)
    {
        $this->_em->persist($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function flushEntity()
    {
        $this->_em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findEntityBy(array $parameters)
    {
        return $this->findOneBy($parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getPhotoByParameters(
        ParameterBag $parameterBag,        
        ParamFetcher $paramFetcher,
        $dateFrom,
        $dateTo
    )
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('p')
            ->from('AppBundle:Photo', 'p')
            ->leftJoin('p.tags', 't');
        
        if ($parameterBag->get('tag_ids') !== null
            && is_array($tagIds = $parameterBag->get('tag_ids'))
        ) {
            $qb->andWhere($qb->expr()->in('t.id', $parameterBag->get('tag_ids')));
        }

        if ($dateTo instanceof \DateTime && $dateFrom instanceof \DateTime) {
            $qb
                ->andWhere($qb->expr()->between('p.createdAt', ':dateFrom', ':dateTo'))
                ->setParameter('dateFrom', $dateFrom->format('Y-m-d H:i:s'))
                ->setParameter('dateTo', $dateTo->format('Y-m-d H:i:s'));
        }

        $qb
            ->orderBy('p.' . $paramFetcher->get('sort_by'), $paramFetcher->get('sort_order'))
            ->setFirstResult($paramFetcher->get('count') * ($paramFetcher->get('page') - 1))
            ->setMaxResults($paramFetcher->get('count'));

        $query = $qb->getQuery();
        $results = $query->getResult();

        return $results;
    }
}
