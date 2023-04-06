<?php

namespace App\Twig\Extension;

use App\Repository\PostRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function __construct(private readonly PostRepository $postRepository)
    {
    }
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('filter_name', [$this, 'doSomething']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('pluralize', [$this, 'pluralize']),
            new TwigFunction('total_posts', [$this, 'totalPosts']),
            new TwigFunction('latest_posts', [$this, 'latestPosts']),
            new TwigFunction('most_commented_posts', [$this, 'mostCommentedPosts']),
        ];
    }

    public function pluralize(int $quantity, string $singular, ?string $plural = null): string
    {
        $plural ??= $singular.'s';

        $singularOrplural = 1 === $quantity ? $singular : $plural;

        return sprintf('%d %s', $quantity, $singularOrplural);
    }

    public function totalPosts(): int
    {
        return $this->postRepository->count([]);
    }
    public function latestPosts(int $maxResults = 5): array
    {
        return $this->postRepository->findBy([], ['publishedAt'=> 'DESC'], $maxResults);
    }
    public function mostCommentedPosts(int $maxResults = 5): array
    {
        return $this->postRepository->findMostCommented($maxResults);
    }
}
