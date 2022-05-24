<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostsController extends AbstractController
{
    public function __construct(Private PostRepository $postRepository){}

    #[Route('%app.admin.path%', name: 'app_home', methods: ['GET'])]
    public function index(PostRepository $postRepository, PaginatorInterface $paginator, Request $request): Response
    {
        // TODO filters to select only published posts

        // Order by publishedAt DESC
        $query =  $postRepository->findAllPublishedOrderedQuery();

        $page =  $request->query->getInt('page', 1);

        if($page === 0){
            throw $this->createNotFoundException("Page not found");
        }

        $pagination = $paginator->paginate($query, $page, Post::NUM_ITEMS_PER_PAGE);

        return $this->render('posts/index.html.twig', compact('pagination'));
    }

    #[Route(
        '/posts/{year}/{month}/{day}/{slug}',
        name: 'app_posts_show',
        requirements: [
            'year' => '[0-9]{4}',
            'month' => '[0-9]{2}',
            'day' => '[0-9]{2}',
            'slug' => '[a-z0-9\-]+',
        ],
        methods: ['GET']
    )]
    public function show(int $year, int $month, int $day, string $slug): Response
    {

        $post = $this->postRepository->findOneByPublishDateAndSlug($year, $month, $day, $slug);

        if(!$post){
            throw $this->createNotFoundException("Post not found.");
        }

        return $this->render('posts/show.html.twig', compact('post'));
    }
}
