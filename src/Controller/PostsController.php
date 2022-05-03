<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostsController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(PostRepository $postRepository): Response
    {
        // TODO filters to select only published posts
        // Order by publishedAt DESC
        $posts = $postRepository->findAllPublished();


        return $this->render('posts/index.html.twig', compact('posts'));
    }

    #[Route('/posts/{slug}', name: 'app_posts_show')]
    public function show(Post $post): Response
    {
        return $this->render('posts/show.html.twig', compact('post'));
    }
}
