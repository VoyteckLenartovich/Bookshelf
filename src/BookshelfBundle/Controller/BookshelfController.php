<?php

namespace BookshelfBundle\Controller;

use BookshelfBundle\Entity\Bookshelf;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BookshelfController
 * @package BookshelfBundle\Controller
 */
class BookshelfController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function showAllAction()
    {
        $allBookshelves = $repo = $this->getDoctrine()->getRepository("BookshelfBundle:Bookshelf")->findAll();

        return array(
                "bookshelves" => $allBookshelves
            );
    }

    /**
     * @Route("/show/{id}")
     * @Template()
     */
    public function showAction($id)
    {
        $bookshelf = $repo = $this->getDoctrine()->getRepository("BookshelfBundle:Bookshelf")->find($id);

        return array(
                "bookshelf" => $bookshelf
            );
    }

    // This controller generates a form used to generate a new bookshelf. "createAction" controller (below) handles the form.
    /**
     * @Route("/createForm")
     * @Template()
     */
    public function createFormAction()
    {
        $newBookshelf = new Bookshelf();

        $form = $this->createFormBuilder($newBookshelf)
            ->add("name", "text")
            ->add("submit", "submit")
            ->getForm();

        return array(
                'form' => $form->createView()
            );
    }

    // This controller handles the form generated by "createFormAction" controller.
    /**
     * @Route("/create")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $newBookshelf = new Bookshelf();

        $form = $this->createFormBuilder($newBookshelf)
            ->add("name", "text")
            ->add("submit", "submit")
            ->getForm();

        $form->handleRequest($request);

        $validator = $this->get("validator");
        $errors = $validator->validate($newBookshelf);

        $em = $this->getDoctrine()->getManager();
        $em->persist($newBookshelf);
        $em->flush();

        if($form->isValid()){
            return $this->redirectToRoute("bookshelf_bookshelf_show", array("id" => $newBookshelf->getId()));
        } else {
            // Form is being sent again for user's convenience.
            return $this->render("BookshelfBundle:Bookshelf:createForm.html.twig", array("form" => $form, "errors" => $errors));
        }
    }

    /**
     * @Route("/delete/{id}")
     */
    public function deleteAction($id)
    {
        $bookshelf = $this->getDoctrine()->getRepository("BookshelfBundle:Bookshelf")->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($bookshelf);
        $em->flush();

        return $this->redirectToRoute("bookshelf_bookshelf_showall");
    }

}
