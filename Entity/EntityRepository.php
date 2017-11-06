<?php

namespace Loevgaard\PakkelabelsBundle\Entity;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * This entity repository is implemented using the principles described here:
 * https://www.tomasvotruba.cz/blog/2017/10/16/how-to-use-repository-with-doctrine-as-service-in-symfony/.
 *
 * @todo this class should probably be in a separate library
 *
 * @method null|object find($id)
 * @method array findBy(array $criteria, array $orderBy = null, int $limit = null, int $offset = null)
 * @method null|object findOneBy(array $criteria)
 * @method array findAll()
 * @method persist($object)
 * @method flush()
 * @method remove($object)
 */
abstract class EntityRepository
{
    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * @var DoctrineEntityRepository
     */
    protected $repository;

    /**
     * @var PaginatorInterface
     */
    protected $paginator;

    public function __construct(ManagerRegistry $managerRegistry, PaginatorInterface $paginator, string $class)
    {
        $this->manager = $managerRegistry->getManagerForClass($class);
        $this->repository = $this->manager->getRepository($class);
        $this->paginator = $paginator;
    }

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (method_exists($this->repository, $name)) {
            return call_user_func_array([$this->repository, $name], $arguments);
        }

        if (method_exists($this->manager, $name)) {
            return call_user_func_array([$this->manager, $name], $arguments);
        }
    }

    /**
     * Saves the $object.
     *
     * @param $object
     */
    public function save($object)
    {
        $this->manager->persist($object);
        $this->manager->flush();
    }

    /**
     * @param int          $page
     * @param int          $itemsPerPage
     * @param array        $orderBy
     * @param QueryBuilder $qb
     *
     * @return PaginationInterface
     */
    public function findAllWithPaging($page = 1, $itemsPerPage = 100, array $orderBy = [], QueryBuilder $qb = null): PaginationInterface
    {
        if (!$qb) {
            $qb = $this->getQueryBuilder('e');
        }

        foreach ($orderBy as $field => $direction) {
            $qb->addOrderBy($field, $direction);
        }

        $objs = $this->paginator->paginate(
            $qb,
            $page,
            $itemsPerPage
        );

        return $objs;
    }

    /**
     * @param string $alias
     *
     * @return QueryBuilder
     */
    public function getQueryBuilder(string $alias): QueryBuilder
    {
        return $this->repository->createQueryBuilder($alias);
    }
}
