<?php

namespace App\Controller;

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
        $posts = $postRepository->findAll();

        return $this->render('posts/index.html.twig', compact('posts'));
    }
}
