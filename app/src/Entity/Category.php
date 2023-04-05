<?php

namespace App\Entity;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[Get(normalizationContext: ['groups' => 'category:read'])]
#[GetCollection(normalizationContext: ['groups' => 'category:read'])]
#[Post(
    normalizationContext: ['groups' => ['category:read']],
    denormalizationContext: ['groups' => ['category:edit']],
    security: "is_granted('ROLE_ADMIN')",
    validationContext: ['category:edit']
)]
#[Patch(
    normalizationContext: ['groups' => ['category:read']],
    denormalizationContext: ['groups' => ['category:edit']],
    security: "is_granted('ROLE_ADMIN')"
)]
#[Delete(security: "is_granted('ROLE_ADMIN')")]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['category:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 500)]
    #[Groups(['category:edit', 'category:read'])]
    #[Assert\NotBlank(groups: ['category:edit'])]
    private ?string $title = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: BlogPost::class)]
    private Collection $blogPosts;

    public function __construct()
    {
        $this->blogPosts = new ArrayCollection();
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

    /**
     * @return Collection<int, BlogPost>
     */
    public function getBlogPosts(): Collection
    {
        return $this->blogPosts;
    }

    public function addBlogPost(BlogPost $blogPost): self
    {
        if (!$this->blogPosts->contains($blogPost)) {
            $this->blogPosts->add($blogPost);
            $blogPost->setCategory($this);
        }

        return $this;
    }

    public function removeBlogPost(BlogPost $blogPost): self
    {
        if ($this->blogPosts->removeElement($blogPost)) {
            // set the owning side to null (unless already changed)
            if ($blogPost->getCategory() === $this) {
                $blogPost->setCategory(null);
            }
        }

        return $this;
    }
}
