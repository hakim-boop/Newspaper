<?php

namespace App\Controller;


use App\Entity\Category;
use App\Form\CategoryFormType;
use App\Repository\CategoryRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin')]
class CategoryController extends AbstractController
{
    #[Route('/creer-une-categorie', name: 'create_category', methods: ['GET', 'POST'])]
    public function createCategory(Request $request, SluggerInterface $slugger, CategoryRepository $repository): Response
    {
        $category = new Category();

        $form = $this->createForm(CategoryFormType::class, $category)->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $category->setCreatedAt(new DateTime());
            $category->setUpdatedAt(new DateTime());

            // L'alias nous servira à construire une URL.
            // Pour cela on utilise le $slugger :
            $category->setAlias($slugger->slug($category->getName()));

            $repository->add($category, true);

            $this->addFlash('success', 'La nouvelle catégorie est bien créée !');
            return $this->redirectToRoute('show_dashboard');
        }

        return $this->render('admin/form/category.html.twig', [
            'form' => $form->createView()
        ]);
    }

    # EXERCICE : Terminer le CRUD pour la catégorie, jusqu'au softDelete()
    # => faire l'affichage des catégories dans show_dashboard.html.twig

    #[Route('/modifier-une-categorie/{id}', name: 'update_category', methods: ['GET', 'POST'])]
    public function updateCategory(Category $category, Request $request, CategoryRepository $repository, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(CategoryFormType::class, $category, [
            'category' => $category
        ])
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $category->setUpdatedAt(new DateTime());
            $category->setAlias($slugger->slug($category->getName()));

            $repository->add($category, true);

            $this->addFlash('success', 'La catégorie est bien modifiée !');
            return $this->redirectToRoute('show_dashboard');
        } // end if $form

        return $this->render('admin/form/category.html.twig', [
            'category' => $category,
            'form' => $form->createView()
        ]);
    } // end function update()

    #[Route('/archiver-une-categorie/{id}', name: 'soft_delete_category', methods: ['GET'])]
    public function softDelete(Category $category, CategoryRepository $repository): Response
    {
        $category->setDeletedAt(new DateTime());

        $repository->add($category, true);

        $this->addFlash('success', 'La catégorie a bien été archivé. Voir les archives pour restaurer ;)');
        return $this->redirectToRoute('show_dashboard');
    }// end function softDelete()

    #[Route('/restaurer-une-categorie/{id}', name: 'restore_category', methods: ['GET'])]
    public function restore(Category $category, CategoryRepository $repository): Response
    {
        # Comme un bouton "on/off", on reset deletedAt à null pour remettre en 'ligne' une catégorie.
        $category->setDeletedAt(null);

        $repository->add($category, true);

        $this->addFlash('success', 'La catégorie a bien été restauré ;)');
        return $this->redirectToRoute('show_dashboard');
    }

    #[Route('/supprimer-une-categorie/{id}', name: 'hard_delete_category', methods: ['GET'])]
    public function hardDelete(Category $category, CategoryRepository $repository): Response
    {
        $repository->remove($category, true);

        $this->addFlash('success', 'La catégorie a bien été supprimé définitivement du système.');
        return $this->redirectToRoute('show_archives');
    }

}// end class