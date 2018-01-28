<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;

class OrmProjectsRepository implements ProjectsRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param int $limit
     * @param int $after
     * @return Project[]
     */
    public function fetchLatest(int $limit = 10, int $after = 0): array
    {
        $qb = $this->em->createQueryBuilder()
            ->select('p')
            ->from('App:Project', 'p')
            ->setMaxResults($limit)
            ->setFirstResult($after);

        return $qb->getQuery()->getResult();
    }

    public function persist(Project $project)
    {
        $this->em->persist($project);
        $this->em->flush();
    }

    public function find(Uuid $uuid)
    {
        return $this->em->find('App:Project', $uuid);
    }

    public function flush(Project $project)
    {
        $this->em->flush();
    }
}
