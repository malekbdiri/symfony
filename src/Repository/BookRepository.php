<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

//    /**
//     * @return Book[] Returns an array of Book objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Book
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }


function SearchByRef($ref){
    return $this->createQueryBuilder('b')
    ->where('b.ref LIKE ?1')
    ->setParameter(1,'%'.$ref.'%')
    ->orderBy('b.title','ASC')
    ->getQuery()->getResult();
}

public function booksListByAuthors()
{
    return $this->createQueryBuilder('b')
        ->join('b.author', 'a')
        ->addSelect('a')
        ->orderBy('a.username', 'DESC')
        ->getQuery()
        ->getResult();
}

function findBook(){
    return $this->createQueryBuilder('b')
    ->join('b.author','a')
    ->addSelect('a')
    ->where('a.nb_books > :nb')
    ->andWhere('b.publicationDate < :year')
    ->setParameters([
        'nb'=>10,'year'=>'2023-01-01'])
        ->getQuery()->getResult();
}

function findByDate(){
    $em=$this->getEntityManager();
    return $em->createQuery(
        'select b from App\Entity\Book b
        where b.publicationDate  BETWEEN ?1 and ?2'
    )
    ->setParameters([
        1=>'2014-01-01',2=>'2018-12-31'
    ])
    ->getResult();
}

public function updateCat()
{

    return $this->createQueryBuilder('b')
        ->where('b.category LIKE  :cat ')
        ->update()->set('b.category', ':r')
        ->setParameters(['cat' => 'Science-Fiction', 'r' => 'Romance'])

        ->getQuery()
        ->getResult();
}

public function numberOfBooks()
{
    $em = $this->getEntityManager();

    return $em->createQuery("SELECT count(b)  from App\Entity\Book b where b.category LIKE :r ")
        ->setParameter('r', 'Romance')
        ->getSingleScalarResult();
}
}


