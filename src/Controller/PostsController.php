<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostsController extends AbstractController
{
    public function __construct(Private PostRepository $postRepository){}

    #[Route('/', name: 'app_home')]
    public function index(PostRepository $postRepository): Response
    {
        // TODO filters to select only published posts
        // Order by publishedAt DESC
        $posts = $postRepository->findAllPublishedOrdered();


        return $this->render('posts/index.html.twig', compact('posts'));
    }

    #[Route('/posts/{year}/{month}/{day}/{slug}', name: 'app_posts_show')]
    public function show(int $year, int $month, int $day, string $slug): Response
    {

        $post = $this->postRepository->findOneByPublishDateAndSlug($year, $month, $day, $slug);

        if(!$post){
            throw $this->createNotFoundException("Post not found.");
        }
        
        return $this->render('posts/show.html.twig', compact('post'));
    }
}
