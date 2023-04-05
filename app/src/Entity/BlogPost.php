<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\BlogPostRepository;
use App\State\BlogPostCreateProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BlogPostRepository::class)]
#[ApiFilter(SearchFilter::class, properties: ['category' => 'exact'])]
#[Get(normalizationContext: ['groups' => ['post:read']])]
#[GetCollection(
    normalizationContext: ['groups' => 'post:read']
)]
#[Post(
    normalizationContext: ['groups' => ['post:response']],
    denormalizationContext: ['groups' => ['post:edit']],
    security: "is_granted('ROLE_ADMIN')",
    validationContext: ['groups' => ['post:edit']],
    processor: BlogPostCreateProcessor::class
)]
#[Patch(
    normalizationContext: ['groups' => ['post:read']],
    denormalizationContext: ['groups' => ['post:update']],
    security: "is_granted('ROLE_ADMIN')"
)]
#[Delete(security: "is_granted('ROLE_ADMIN')")]
class BlogPost
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['post:response', 'post:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 1024)]
    #[Assert\NotBlank(groups: ['post:edit'])]
    #[Groups(['post:response', 'post:edit', 'post:update', 'post:read'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(groups: ['post:edit'])]
    #[Groups(['post:response', 'post:edit', 'post:update', 'post:read'])]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'blogPosts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['post:response', 'post:read'])]
    private ?User $author = null;

    #[ORM\ManyToOne(inversedBy: 'blogPosts')]
    #[Groups(['post:edit', 'post:update', 'post:response', 'post:read'])]
    private ?Category $category = null;

    #[ORM\OneToMany(mappedBy: 'blogPost', targetEntity: Comment::class, orphanRemoval: true)]
    #[Groups(['post:read'])]
    private Collection $comments;

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

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
            $this->comments->add($comment);
            $comment->setBlogPost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getBlogPost() === $this) {
                $comment->setBlogPost(null);
            }
        }

        return $this;
    }
}
