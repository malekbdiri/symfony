<?php

namespace App\Controller;
use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }

    #[Route('/listAuthor', name: 'authors')]
public function list(AuthorRepository $repository) {
    $authors=$repository->findall();
    return $this->render("author/listAuthors.html.twig",
     array (        'tabAuthors'=>$authors
));

    

}

    #[Route('/list/{var}', name: 'list_author')]
    public function listAuthor($var)
    {
        $authors = array(
            array('id' => 1, 'username' => ' Victor Hugo','email'=> 'victor.hugo@gmail.com', 'nb_books'=> 100),
            array ('id' => 2, 'username' => 'William Shakespeare','email'=>
                'william.shakespeare@gmail.com','nb_books' => 200),
            array('id' => 3, 'username' => ' Taha Hussein','email'=> 'taha.hussein@gmail.com','nb_books' => 300),
        );

        return $this->render("author/list.html.twig",
            array('variable'=>$var,
                'tabAuthors'=>$authors
                ));
    }
    #[Route('/author/{id}', name: 'author_details')]
public function authorDetails($id)
{
    $authors = array(
        array('id' => 1, 'username' => 'Victor Hugo', 'email' => 'victor.hugo@gmail.com', 'nb_books' => 100,'image'=>'imgs/1.jfif'),
        array('id' => 2, 'username' => 'William Shakespeare', 'email' => 'william.shakespeare@gmail.com', 'nb_books' => 200,'image'=>'imgs/2.jfif'),
        array('id' => 3, 'username' => 'Taha Hussein', 'email' => 'taha.hussein@gmail.com', 'nb_books' => 300,'image'=>'imgs/3.jfif'),
    );

    
    $author = null;
    foreach ($authors as $a) {
        if ($a['id'] == $id) {
            $author = $a;
            break;
        }
    }

    return $this->render('author/showAuthor.html.twig', [
        'author' => $author,
    ]);
}
 
#[Route('/add', name: 'add_author')]

public function  add (ManagerRegistry $manager, Request $req)
{   
    $em = $manager->getManager();
    $author=new Author();
    $form =$this->CreateForm(AuthorType::class,$author);
    $form->add('Save',SubmitType::class);
    $form->handleRequest($req);
    if ($form->isSubmitted() )
    {
        $em->persist($author);
        $em->flush();
        return $this->redirectToRoute('authors');
    }
    return $this->render('author/add.html.twig',['f'=>$form->createView()]);

}
    #[Route('/edit/{id}', name: 'app_edit')]
    public function edit(AuthorRepository $repository, $id, Request $request)
    {
        $author = $repository->find($id);
        $form = $this->createForm(AuthorType::class, $author);
        $form->add('Edit', SubmitType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute("authors");
        }

        return $this->render('author/edit.html.twig', [
            'f' => $form->createView(),
        ]);
    }
    #[Route('/delete/{id}', name: 'app_delete')]
    public function delete($id, AuthorRepository $repository)
    {
        $author = $repository->find($id);

        if (!$author) {
            throw $this->createNotFoundException('Auteur non trouvé');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($author);
        $em->flush();

        return $this->redirectToRoute('authors');
    }

    #[Route('/searchNb', name: "search_nb")]
    public function searchAuthors(AuthorRepository $repository, Request $req)
    {

        $min = $req->get('min');
        $max = $req->get('max');
        return $this->render('author/listAuthors.html.twig', ["tabAuthors" => $repository->nbBooksMinMax($min, $max)]);
    }

    #[Route('/remove', name: "remove")]
    public function removeA(AuthorRepository $repository)
    {
        $repository->removeAuthorr();

        return $this->redirectToRoute("authors");
    }
}






    /*
    #[Route('/AddStatistique', name: 'app_AddStatistique')]

    public function addStatistique(EntityManagerInterface $entityManager): Response
    {
        // Créez une instance de l'entité Author
        $author1 = new Author();
        $author1->setUsername("test"); // Utilisez "setUsername" pour définir le nom d'utilisateur
        $author1->setEmail("test@gmail.com"); // Utilisez "setEmail" pour définir l'email

        // Enregistrez l'entité dans la base de données
        $entityManager->persist($author1);
        $entityManager->flush();

        return $this->redirectToRoute('authors'); // Redirigez vers la route 'app_Affiche'
    }

*/