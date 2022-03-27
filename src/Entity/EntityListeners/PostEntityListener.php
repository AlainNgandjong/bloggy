<?php


namespace App\Entity\EntityListeners;


use App\Entity\Post;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

class PostEntityListener
{

    public function __construct(private SluggerInterface $slugger){}

    public function prePersist(Post $post, LifecycleEventArgs $event)
    {
        $post->computeSlug($this->slugger, $post->getTitle());
    }

    public function preUpdate(Post $post, LifecycleEventArgs $event)
    {
        $post->computeSlug($this->slugger, $post->getTitle());
    }

}