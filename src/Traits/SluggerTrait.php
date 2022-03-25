<?php

namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\SluggerInterface;

trait SluggerTrait
{

    #[ORM\Column(type: 'string', length: 255)]
    private $slug;

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Why do we need the - special value? Because when adding a conference in the backend, the slug is required.
     * So, we need a non-empty value that tells the application that we want the slug to be automatically generated.
     */
    public function computeSlug(SluggerInterface $slugger, string $name)
    {
        if (!$this->slug || '-' === $this->slug) {
            $this->slug = (string) $slugger->slug((string) $name)->lower();
        }
    }
}