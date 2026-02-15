<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/article')]
class ArticleController extends AbstractController
{
    // Liste des articles (uniquement ceux de l'utilisateur connecté)
    #[Route('', name: 'admin_article_index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository): Response
    {
        // Récupère uniquement les articles de l'utilisateur connecté
        $articles = $articleRepository->findByAuthor($this->getUser());

        return $this->render('pages/admin/article/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    // Créer un article
    #[Route('/new', name: 'admin_article_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Générer le slug automatiquement
            $slug = $slugger->slug($article->getTitle())->lower();
            $article->setSlug($slug);

            // Définir la date de création
            $article->setCreatedAt(new \DateTimeImmutable());

            // Définir l'auteur (utilisateur connecté)
            $article->setAuthor($this->getUser());

            // Gérer l'upload de l'image
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                    $article->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image');
                }
            }

            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('success', 'Article créé avec succès !');

            return $this->redirectToRoute('admin_article_index');
        }

        return $this->render('pages/admin/article/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    // Modifier un article (uniquement si l'utilisateur est l'auteur)
    #[Route('/{id}/edit', name: 'admin_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        // Vérifier que l'utilisateur est l'auteur de l'article
        if ($article->getAuthor() !== $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas modifier cet article.');
            return $this->redirectToRoute('admin_article_index');
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Mettre à jour le slug si le titre a changé
            $slug = $slugger->slug($article->getTitle())->lower();
            $article->setSlug($slug);

            // Gérer l'upload de l'image
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );

                    // Supprimer l'ancienne image si elle existe
                    if ($article->getImage()) {
                        $oldImagePath = $this->getParameter('images_directory').'/'.$article->getImage();
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }

                    $article->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image');
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Article modifié avec succès !');

            return $this->redirectToRoute('admin_article_index');
        }

        return $this->render('pages/admin/article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    // Supprimer un article (uniquement si l'utilisateur est l'auteur)
    #[Route('/{id}', name: 'admin_article_delete', methods: ['POST'])]
    public function delete(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que l'utilisateur est l'auteur de l'article
        if ($article->getAuthor() !== $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer cet article.');
            return $this->redirectToRoute('admin_article_index');
        }

        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            // Supprimer l'image si elle existe
            if ($article->getImage()) {
                $imagePath = $this->getParameter('images_directory').'/'.$article->getImage();
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $entityManager->remove($article);
            $entityManager->flush();

            $this->addFlash('success', 'Article supprimé avec succès !');
        }

        return $this->redirectToRoute('admin_article_index');
    }
}