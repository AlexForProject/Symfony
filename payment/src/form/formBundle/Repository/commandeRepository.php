<?php

namespace form\formBundle\Repository;

/**
 * commandeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class commandeRepository extends \Doctrine\ORM\EntityRepository
{
	public function getQuota($date)
	{
		$nbPlace=$this->createQueryBuilder('c')
		->andwhere('c.date = :date')
		->setParameter('date', $date)
		->SELECT('SUM(c.nbPlace) as somme')
		->getQuery()
		->getSingleScalarResult();

		return $nbPlace;
	}

	public function getCommandes($date)
	{
		$commandes=$this->createQueryBuilder('c')
		->ANDWHERE('c.date < :date')
		->setParameter('date', $date)
		->ANDWHERE('c.codeBarre = 0')
		->getQuery()
		->getResult();

		return $commandes;
	}

}
