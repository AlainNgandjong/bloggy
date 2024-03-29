<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\Tag;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public function __construct(private AdminUrlGenerator $adminUrlGenerator)
    {
    }

    #[Route('/admin', name: 'admin')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(): Response
    {
        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        return $this->redirect($this->adminUrlGenerator->setController(PostCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Bloggy');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToUrl('Visit home page', null, '/');

        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('Blog');
        yield menuItem::linkToCrud('Posts', 'fas fa-list', Post::class);
        yield menuItem::linkToCrud('Comments', 'fas fa-comments', Comment::class);
        yield menuItem::linkToCrud('Tags', 'fas fa-tag', Tag::class);

        yield MenuItem::section('User');
        yield MenuItem::subMenu('Users', 'fas fa-users')->setSubItems([
            menuItem::linkToCrud('Add', 'fas fa-plus', User::class)->setAction(Crud::PAGE_NEW),
            menuItem::linkToCrud('All Users', 'fas fa-list', User::class),
        ]);
    }
}
