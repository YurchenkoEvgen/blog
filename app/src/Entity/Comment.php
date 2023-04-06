<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\CommentRepository;
use App\State\CommentCreateProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ApiResource(
    uriTemplate: '/blog_posts/{post_id}/comments/{id}',
    uriVariables: [
        'post_id' => new Link(
            toProperty: 'blogPost',
            fromClass: BlogPost::class
        ),
        'id' => new Link(
            fromClass: Comment::class
        )
    ]
)]
#[Get(normalizationContext: ['groups' => ['comment:read']])]
#[GetCollection(
    uriTemplate: '/blog_posts/{post_id}/comments',
    uriVariables: [
        'post_id' => new Link(
            toProperty: 'blogPost',
            fromClass: BlogPost::class
        )
    ],
    normalizationContext: ['groups' => ['comment:read']]
)]
#[Post(
    uriTemplate: '/blog_posts/{post_id}/comments',
    uriVariables: [
        'post_id' => new Link(
            fromClass: BlogPost::class,
            toProperty: 'blogPost'
        )
    ],
    normalizationContext: ['groups' => ['comment:read']],
    denormalizationContext: ['groups' => ['comment:edit']],
    security: "is_granted('ROLE_USER')",
    validationContext: ['groups' => ['comment:edit']],
    read: false,
    processor: CommentCreateProcessor::class
)]
#[Patch(
    normalizationContext: ['groups' => ['comment:read']],
    denormalizationContext: ['groups' => ['comment:edit']],
    security: "is_granted('ROLE_ADMIN')",
    validationContext: ['groups' => ['comment:edit']]
)]
#[Delete(security: "is_granted('ROLE_ADMIN')")]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['comment:read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['comment:read', 'comment:edit'])]
    #[Assert\NotBlank(groups: ['comment:edit'])]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['comment:read'])]
    private ?User $author = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'comments')]
    #[Groups(['comment:read', 'comment:edit'])]
    private ?self $parent = null;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
    #[Groups(['comment:read'])]
    private Collection $comments;

    #[ORM\ManyToOne(targetEntity: BlogPost::class, inversedBy: 'comments', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['comment:read'])]
    private ?BlogPost $blogPost = null;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(self $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setParent($this);
        }

        return $this;
    }

    public function removeComment(self $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getParent() === $this) {
                $comment->setParent(null);
            }
        }

        return $this;
    }

    public function getBlogPost(): ?BlogPost
    {
        return $this->blogPost;
    }

    public function setBlogPost(?BlogPost $blogPost): self
    {
        $this->blogPost = $blogPost;

        return $this;
    }
}
