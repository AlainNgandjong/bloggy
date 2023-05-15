<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\CommentFormType;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Repository\TagRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\EventListener\AbstractSessionListener;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;

class PostsController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    #[Route(
        '/tags/{slug}',
        name: 'app_posts_by_tag',
        requirements: [
            'slug' => Requirement::ASCII_SLUG,
        ],
        methods: ['GET']
    )]
    public function index(Request $request, ?string $slug, PostRepository $postRepository,  TagRepository $tagRepository,  PaginatorInterface $paginator,): Response
    {
        $tag = null;
        if ($slug) {
            $tag = $tagRepository->findOneBySlug($slug);
        }

        // Order by publishedAt DESC
        $query = $postRepository->findAllPublishedOrderedByNewestQuery($tag);

        $page = $request->query->getInt('page', 1);

        if (0 === $page) {
            throw $this->createNotFoundException('Page not found');
        }

        $pagination = $paginator->paginate($query, $page, Post::NUM_ITEMS_PER_PAGE);


        $response =  $this->render('posts/index.html.twig',
            compact('pagination', 'tag'))->setSharedMaxAge(30);

        $response->headers->set(
            AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, 'true'
        );

        return $response;

    }

    #[Route(
        '/posts/{slug}',
        name: 'app_posts_show',
        requirements: [
            'slug' => Requirement::ASCII_SLUG,
        ],
        methods: ['GET', 'POST']
    )]
    public function show(Request $request,
        #[MapEntity(expr: 'repository.findOnePublishedBySlug(slug)')] Post $post,
        PostRepository $postRepository,
        CommentRepository $commentRepository
    ): Response
    {

        
        $similarPosts = $postRepository->findSimilar($post);
        
        $comments = $post->getActiveComments();

        $commentForm = $this->createForm(CommentFormType::class);

        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment = $commentForm->getData();
            $comment->setPost($post);

            $commentRepository->add($comment, flush: true);

            $this->addFlash(
                'success',
                '🚀 Comment successfully added!'
            );

            return $this->redirectToRoute('app_posts_show', ['slug' => $post->getSlug()]);
        }

        return $this->render('posts/show.html.twig', compact('post', 'comments', 'commentForm', 'similarPosts'));
    }

    #[Route(
        '/posts/featured-content',
        name: 'app_posts_featured_content',
        methods: ['GET'],
        priority: 10
    )]
    public  function featuredContent(PostRepository $postRepository, int $maxResults = 5) : Response {

        $totalPosts = $postRepository->count([]);
        $latestPosts = $postRepository->findBy([], ['publishedAt'=> 'DESC'], $maxResults);
        $mostCommentedPosts = $postRepository->findMostCommented($maxResults);

        return $this->render('posts/_featured_content.html.twig',
            compact('totalPosts', 'latestPosts', 'mostCommentedPosts'
            ))->setSharedMaxAge(50);
    }
}
