<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Repository\ArticleRepository;
use App\Repository\CommentaryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    #[Route('/voir-articles/{alias}', name: 'show_articles_from_category', methods: ['GET'])]
    public function showArticlesFromCategory(Category $category, ArticleRepository $repository): Response
    {
        $articles = $repository->findBy([
            'deletedAt' => null,
            'category' => $category->getId()
        ]);

        return $this->render('article/show_articles_from_categories.html.twig', [
            'articles' => $articles,
            'category' => $category
        ]);
    }

    #[Route('{cat_alias}/{art_alias}_{id}', name: 'show_article', methods: ['GET'])]
    public function showArticle(Article $article, CommentaryRepository $commentaryRepository): Response
    {
        $commentaries = $commentaryRepository->findBy(['deletedAt' => null, 'article' => $article->getId()]);

        return $this->render('article/show_article.html.twig', [
            'article' => $article,
            'commentaries' => $commentaries
        ]);
    }
}