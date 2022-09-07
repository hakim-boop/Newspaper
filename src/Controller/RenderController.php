<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RenderController extends AbstractController

{   #[Route('/voir-les-archives', name: 'show_archives', methods: ['GET'])]
    public function renderCategoriesTnNav(CategoryRepository $repository): Response 
    {
        $categories = $repository->findBy(['deletedAt' => null], ['name' => 'ASC']);

        return $this->render('rendered/categories_in_nav.html.twig', [
            'categories' => $categories
        ]);
    }
}
     