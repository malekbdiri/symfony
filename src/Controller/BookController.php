<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }


 

    #[Route('/addBook', name: 'app_AddBook')]
    public function addBook(Request $request,ManagerRegistry $managerRegistry)
    {
        $book= new Book();
        $form= $this->createForm(BookType::class,$book);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            $book->setPublished(true);
            $nbBooks=$book->getAuthor()->getnbBooks();
            $book->getAuthor()->setnbBooks($nbBooks+1);
            $em= $managerRegistry->getManager();
            $em->persist($book);
            $em->flush();
            return new Response("Done!");
        }
        //1ere methode
        // return $this->render('book/add.html.twig',array("formulaireBook"=>$form->createView()));
        //2eme methode
        return $this->renderForm('book/add.html.twig',array("f"=>$form));
    }

    #[Route('/listBook', name: 'list_book')]
    public function listBook(BookRepository  $repository)
    {
        return $this->render("book/list.html.twig",
        array('books'=>$repository->findAll()));
      /*  return $this->render("book/list.html.twig",
            array('books'=>$repository->findBy(['published'=>false])));*/
    }

    #[Route('/updateBook/{ref}', name: 'update_book')]
    public function updateBook($ref,BookRepository $repository,Request  $request, ManagerRegistry $managerRegistry)
    {
        $book= $repository->find($ref) ;
        $form= $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            $nbBooks= $book->getAuthor()->getNbBooks();
            $book->getAuthor()->setNbBooks($nbBooks+1);
            $book->setPublished(true);
            $em = $managerRegistry->getManager();
            $em->flush();
           // return  new Response("Done!");
            return  $this->redirectToRoute("list_book");
        }
        return $this->renderForm('book/update.html.twig',
            array('f'=>$form));
    }


    #[Route('/deletebook/{ref}', name: 'app_deleteBook')]
    public function delete($ref, BookRepository $repository)
    {
        $book = $repository->find($ref);

        $em = $this->getDoctrine()->getManager();
        $em->remove($book);
        $em->flush();

        return $this->redirectToRoute('list_book');
    }


    #[Route('/showBook/{ref}', name: 'app_detailBook')]

    public function showBook($ref, BookRepository $repository)
    {
        $book = $repository->find($ref);
        if (!$book) {
            return $this->redirectToRoute('list_book');
        }

        return $this->render('book/show.html.twig', ['b' => $book]);

}



}

