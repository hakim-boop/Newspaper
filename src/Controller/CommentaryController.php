<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Commentary;
use App\Form\CommentaryFormType;
use App\Repository\CommentaryRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentaryController extends AbstractController
{
    #[Route('/ajouter-un-commentaire/{id}', name: 'add_commentary', methods: ['GET', 'POST'])]
    public function addCommentary(Article $article, Request $request, CommentaryRepository $repository): Response
    {
        $commentary = new Commentary();

        $form = $this->createForm(CommentaryFormType::class, $commentary)
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $commentary->setCreatedAt(new DateTime());
            $commentary->setUpdatedAt(new DateTime());

            $commentary->setArticle($article);
            $commentary->setAuthor($this->getUser());

            $repository->add($commentary, true);

            $this->addFlash('success', "Vous avez commenté l'article <strong>". $article->getTitle() . "</strong> avec succès !");

            return $this->redirectToRoute('show_article', [
                'cat_alias' => $article->getCategory()->getAlias(),
                'art_alias' => $article->getAlias(),
                'id' => $article->getId()
            ]);
        }

        return $this->render('rendered/form_commentary.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/archiver-un-commentaire/{id}', name: 'soft_delete_commentary', methods: ['GET'])]
    public function softDelete(Commentary $commentary, EntityManagerInterface $entityManager, Request $request): RedirectResponse
    {
        $commentary->setDeletedAt(new DateTime());

        /** @var Article $article */
        $article = $entityManager->getRepository(Article::class)->find($request->query->get('article_id'));

        $entityManager->persist($commentary);
        $entityManager->flush();

        $this->addFlash('success', "Votre commentaire est archivé");
        return $this->redirectToRoute('show_article', [
            'cat_alias' => $article->getCategory()->getAlias(),
            'art_alias' => $article->getAlias(),
            'id' => $article->getId()
        ]);
    }
}