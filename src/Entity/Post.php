<?php

namespace App\Entity;


use App\Repository\PostRepository;
use App\Entity\Traits\SluggerTrait;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Slug;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ORM\Table(name: '`posts`')]
#[ORM\HasLifecycleCallbacks]
class Post
{
    use SluggerTrait;
    use TimestampableTrait;

    public const NUM_ITEMS_PER_PAGE = 2;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $title;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private $slug;

    #[ORM\Column(type: 'text')]
    private $body;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $publishedAt;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private $author;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Comment::class, orphanRemoval: true)]
    private $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeImmutable $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function __toString():string
    {
        return sprintf('#%d %s', $this->getId(),$this->getTitle());
    }

//    public function getPathParams(): array
//    {
//        return [
//            'date'  =>  $this->getPublishedAt()->format('Y-m-d'),
//            'slug' =>  $this->getSlug()
//        ];
//    }

/**
 * @return Collection<int, Comment>
 */
public function getComments(): Collection
{
    return $this->comments;
}

public function addComment(Comment $comment): self
{
    if (!$this->comments->contains($comment)) {
        $this->comments[] = $comment;
        $comment->setPost($this);
    }

    return $this;
}

public function removeComment(Comment $comment): self
{
    if ($this->comments->removeElement($comment)) {
        // set the owning side to null (unless already changed)
        if ($comment->getPost() === $this) {
            $comment->setPost(null);
        }
    }

    return $this;
}


}
