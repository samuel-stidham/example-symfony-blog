<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/post')]
final class PostController extends AbstractController
{
    #[Route(name: 'app_post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('post/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_post_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    #[Route('/new', name: 'app_post_new')]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger
    ): Response {
        $post = new Post();
        $form = $this->createForm(\App\Form\PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            if (!$user instanceof User) {
                throw $this->createAccessDeniedException();
            }
            $post->setAuthor($user);

            if ($post->getSlug() === '') {
                $post->setSlug(strtolower($slugger->slug($post->getTitle())->toString()));
            }

            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('app_post_show_slug', [
                'slug' => $post->getSlug(),
            ]);
        }

        return $this->render('post/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{slug}', name: 'app_post_show_slug', methods: ['GET'], requirements: ['slug' => '[a-z0-9-]+'])]
    public function showBySlug(string $slug, PostRepository $posts): Response
    {
        $post = $posts->findOneBy(['slug' => $slug]);
        if (!$post) {
            throw new NotFoundHttpException("Post not found");
        }

        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/{id}', name: 'app_post_show', methods: ['GET'], requirements: ['id' => '\d+'], priority: 10)]
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_post_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
    }
}
