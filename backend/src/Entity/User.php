<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'], message: 'Cet email est déjà utilisé.')]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap([
    'user' => User::class,
    'teacher' => Teacher::class,
    'student' => Student::class,
    'parents' => Parents::class
])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read', 'teacher:read', 'student:read', 'parent:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Groups(['user:read', 'teacher:read', 'student:read', 'parent:read'])]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups(['user:read', 'teacher:read', 'student:read', 'parent:read'])]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 50)]
    #[Groups(['user:read', 'teacher:read', 'student:read', 'parent:read'])]
    private ?string $last_name = null;

    #[ORM\Column(length: 50)]
    #[Groups(['user:read', 'teacher:read', 'student:read', 'parent:read'])]
    private ?string $first_name = null;

    #[ORM\Column]
    #[Groups(['user:read', 'teacher:read', 'student:read', 'parent:read'])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'sender')]
    private Collection $sentMessages;

    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'receiver')]
    private Collection $receivedMessages;

    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'user')]
    private Collection $notifications;
   
    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
        $this->sentMessages = new ArrayCollection();
        $this->receivedMessages = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): static
    {
        $this->last_name = $last_name;
        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): static
    {
        $this->first_name = $first_name;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;
        return $this;
    }
   
    public function getSentMessages(): Collection { return $this->sentMessages; }
    public function getReceivedMessages(): Collection { return $this->receivedMessages; }
    public function getNotifications(): Collection { return $this->notifications; }

    public function eraseCredentials(): void {}
}