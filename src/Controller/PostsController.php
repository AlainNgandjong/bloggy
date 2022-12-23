<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Tag;
use App\Form\CommentFormType;
use App\Form\SharePostFormType;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Message;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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
    public function index(PostRepository $postRepository, PaginatorInterface $paginator, Request $request, TagRepository $tagRepository, ?string $slug): Response
    {

        $tag = null;
        if($slug) {
            $tag = $tagRepository->findOneBySlug($slug);
        }

        // Order by publishedAt DESC
        $query =  $postRepository->findAllPublishedOrderedQuery($tag);

        $page =  $request->query->getInt('page', 1);

        if($page === 0){
            throw $this->createNotFoundException("Page not found");
        }

        $pagination = $paginator->paginate($query, $page, Post::NUM_ITEMS_PER_PAGE);

        return $this->render('posts/index.html.twig', compact('pagination', 'tag'));
    }



    #[Route(
        '/posts/{slug}',
        name: 'app_posts_show',
        requirements: [
            'slug' => Requirement::ASCII_SLUG,
        ],
        methods: ['GET', 'POST']
    )]
    public function show(Post $post, Request $request, CommentRepository $commentRepository): Response
    {


        $comments = $post->getActiveComments();
        
        $commentForm = $this->createForm(CommentFormType::class);

        $commentForm->handleRequest($request);

        if($commentForm->isSubmitted() && $commentForm->isValid()) {

            $comment = $commentForm->getData();
            $comment->setPost($post);


            $commentRepository->add($comment, flush: true);

            $this->addFlash(
                'success',
                '🚀 Comment successfully added!'
            );

            return $this->redirectToRoute('app_posts_show', ['slug' => $post->getSlug()]);


        }
        return $this->render('posts/show.html.twig', compact('post', 'comments','commentForm'));
    }

}
