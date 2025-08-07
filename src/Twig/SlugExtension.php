<?php

namespace App\Twig;

use Symfony\Component\String\Slugger\SluggerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class SlugExtension extends AbstractExtension
{
    public function __construct(private SluggerInterface $slugger) {}

    public function getFilters(): array
    {
        return [
            new TwigFilter('slugify', [$this, 'slugify']),
        ];
    }

    public function slugify(string $string): string
    {
        return $this->slugger->slug($string)->lower();
    }
}