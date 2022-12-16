<?php

namespace App\Entity;

use App\Entity\Traits\SluggerTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\TagRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Slug;


#[ORM\Entity(repositoryClass: TagRepository::class)]
#[ORM\Table(name: '`tags`')]
#[ORM\HasLifecycleCallbacks]
class Tag
{

    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Slug(fields: ['name'], updatable: false)]
    private ?string $slug = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getName();
    }
}