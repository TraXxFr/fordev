<?php

namespace App\Entity;

use App\Repository\FriendRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(
 *     fields={"email"},
 *     message="L'email que vous avez indiqué est déjà utilisé !"
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\EqualTo(propertyPath="confirm_password")
     */
    private $password;

    /**
     * @Assert\EqualTo(propertyPath="password")
     */
    public $confirm_password;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $created_at;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity=Friend::class, mappedBy="user", orphanRemoval=true)
     */
    private $requestedFriends;

    /**
     * @ORM\OneToMany(targetEntity=Friend::class, mappedBy="friend", orphanRemoval=true)
     */
    private $acceptedFriends;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pp_path;

    /**
     * @ORM\OneToMany(targetEntity=FMessage::class, mappedBy="owner")
     */
    private $fOwnedMessages;

    /**
     * @ORM\OneToMany(targetEntity=FMessage::class, mappedBy="friend")
     */
    private $fReceivedMessages;

    /**
     * @ORM\OneToMany(targetEntity=Project::class, mappedBy="owner")
     */
    private $ownedProjects;

    /**
     * @ORM\OneToMany(targetEntity=ProjectContributor::class, mappedBy="contributor")
     */
    private $contributedProjects;

    /**
     * @ORM\Column(type="boolean")
     */
    private $public;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $surname;

    /**
     * @ORM\Column(type="bigint")
     */
    private $glory;

    /**
     * @ORM\OneToMany(targetEntity=Fields::class, mappedBy="lastUserEdit")
     */
    private $lastEditedFields;

    /**
     * @ORM\OneToMany(targetEntity=Menu::class, mappedBy="last_user_edit")
     */
    private $lastEditedMenus;

    /**
     * @ORM\OneToMany(targetEntity=ForumTopic::class, mappedBy="owner")
     */
    private $forumTopics;

    public function __construct()
    {
        $this->requestedFriends = new ArrayCollection();
        $this->acceptedFriends = new ArrayCollection();
        $this->fOwnedMessages = new ArrayCollection();
        $this->fReceivedMessages = new ArrayCollection();
        $this->ownedProjects = new ArrayCollection();
        $this->contributedProjects = new ArrayCollection();
        $this->lastEditedFields = new ArrayCollection();
        $this->lastEditedMenus = new ArrayCollection();
        $this->forumTopics = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection|Friend[]
     */
    public function getRequestedFriends(): Collection
    {
        return $this->requestedFriends;
    }

    public function addRequestedFriend(Friend $requestedFriend): self
    {
        if (!$this->requestedFriends->contains($requestedFriend)) {
            $this->requestedFriends[] = $requestedFriend;
            $requestedFriend->setUser($this);
        }

        return $this;
    }

    public function removeRequestedFriend(Friend $requestedFriend): self
    {
        if ($this->requestedFriends->removeElement($requestedFriend)) {
            // set the owning side to null (unless already changed)
            if ($requestedFriend->getUser() === $this) {
                $requestedFriend->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Friend[]
     */
    public function getAcceptedFriends(): Collection
    {
        return $this->acceptedFriends;
    }

    public function addAcceptedFriend(Friend $acceptedFriend): self
    {
        if (!$this->acceptedFriends->contains($acceptedFriend)) {
            $this->acceptedFriends[] = $acceptedFriend;
            $acceptedFriend->setFriend($this);
        }

        return $this;
    }

    public function removeAcceptedFriend(Friend $acceptedFriend): self
    {
        if ($this->acceptedFriends->removeElement($acceptedFriend)) {
            // set the owning side to null (unless already changed)
            if ($acceptedFriend->getFriend() === $this) {
                $acceptedFriend->setFriend(null);
            }
        }

        return $this;
    }

    public function getFriends(): Array
    {
        $friends = [];
        foreach (array_merge($this->requestedFriends->getValues(), $this->acceptedFriends->getValues()) as $request) {
            if(!$request->getAccepted()) continue;
            $friends[] = $request->getUser()->getId() === $this->id ? $request->getFriend() : $request->getUser();
        }


        return $friends;
    }

    public function getPpPath(): ?string
    {
        return $this->pp_path;
    }

    public function setPpPath(?string $pp_path): self
    {
        $this->pp_path = $pp_path;

        return $this;
    }

    /**
     * @return Collection|FMessage[]
     */
    public function getFOwnedMessages(): Collection
    {
        return $this->fOwnedMessages;
    }

    public function addFOwnedMessage(FMessage $fOwnedMessage): self
    {
        if (!$this->fOwnedMessages->contains($fOwnedMessage)) {
            $this->fOwnedMessages[] = $fOwnedMessage;
            $fOwnedMessage->setOwner($this);
        }

        return $this;
    }

    public function removeFOwnedMessage(FMessage $fOwnedMessage): self
    {
        if ($this->fOwnedMessages->removeElement($fOwnedMessage)) {
            // set the owning side to null (unless already changed)
            if ($fOwnedMessage->getOwner() === $this) {
                $fOwnedMessage->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|FMessage[]
     */
    public function getFReceivedMessages(): Collection
    {
        return $this->fReceivedMessages;
    }

    public function addFReceivedMessage(FMessage $fReceivedMessage): self
    {
        if (!$this->fReceivedMessages->contains($fReceivedMessage)) {
            $this->fReceivedMessages[] = $fReceivedMessage;
            $fReceivedMessage->setFriend($this);
        }

        return $this;
    }

    public function removeFReceivedMessage(FMessage $fReceivedMessage): self
    {
        if ($this->fReceivedMessages->removeElement($fReceivedMessage)) {
            // set the owning side to null (unless already changed)
            if ($fReceivedMessage->getFriend() === $this) {
                $fReceivedMessage->setFriend(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Project[]
     */
    public function getOwnedProjects(): Collection
    {
        return $this->ownedProjects;
    }

    public function addOwnedProject(Project $ownedProject): self
    {
        if (!$this->ownedProjects->contains($ownedProject)) {
            $this->ownedProjects[] = $ownedProject;
            $ownedProject->setOwner($this);
        }

        return $this;
    }

    public function removeOwnedProject(Project $ownedProject): self
    {
        if ($this->ownedProjects->removeElement($ownedProject)) {
            // set the owning side to null (unless already changed)
            if ($ownedProject->getOwner() === $this) {
                $ownedProject->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ProjectContributor[]
     */
    public function getContributedProjects(): Collection
    {
        return $this->contributedProjects;
    }

    public function addContributedProject(ProjectContributor $contributedProject): self
    {
        if (!$this->contributedProjects->contains($contributedProject)) {
            $this->contributedProjects[] = $contributedProject;
            $contributedProject->setContributor($this);
        }

        return $this;
    }

    public function removeContributedProject(ProjectContributor $contributedProject): self
    {
        if ($this->contributedProjects->removeElement($contributedProject)) {
            // set the owning side to null (unless already changed)
            if ($contributedProject->getContributor() === $this) {
                $contributedProject->setContributor(null);
            }
        }

        return $this;
    }

    public function getPublic(): ?bool
    {
        return $this->public;
    }

    public function setPublic(bool $public): self
    {
        $this->public = $public;

        return $this;
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

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getGlory(): ?string
    {
        return $this->glory;
    }

    public function setGlory(string $glory): self
    {
        $this->glory = $glory;

        return $this;
    }

    /**
     * @return Collection|Fields[]
     */
    public function getLastEditedFields(): Collection
    {
        return $this->lastEditedFields;
    }

    public function addLastEditedField(Fields $lastEditedField): self
    {
        if (!$this->lastEditedFields->contains($lastEditedField)) {
            $this->lastEditedFields[] = $lastEditedField;
            $lastEditedField->setLastUserEdit($this);
        }

        return $this;
    }

    public function removeLastEditedField(Fields $lastEditedField): self
    {
        if ($this->lastEditedFields->removeElement($lastEditedField)) {
            // set the owning side to null (unless already changed)
            if ($lastEditedField->getLastUserEdit() === $this) {
                $lastEditedField->setLastUserEdit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Menu[]
     */
    public function getLastEditedMenus(): Collection
    {
        return $this->lastEditedMenus;
    }

    public function addLastEditedMenu(Menu $lastEditedMenu): self
    {
        if (!$this->lastEditedMenus->contains($lastEditedMenu)) {
            $this->lastEditedMenus[] = $lastEditedMenu;
            $lastEditedMenu->setLastUserEdit($this);
        }

        return $this;
    }

    public function removeLastEditedMenu(Menu $lastEditedMenu): self
    {
        if ($this->lastEditedMenus->removeElement($lastEditedMenu)) {
            // set the owning side to null (unless already changed)
            if ($lastEditedMenu->getLastUserEdit() === $this) {
                $lastEditedMenu->setLastUserEdit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ForumTopic[]
     */
    public function getForumTopics(): Collection
    {
        return $this->forumTopics;
    }

    public function addForumTopic(ForumTopic $forumTopic): self
    {
        if (!$this->forumTopics->contains($forumTopic)) {
            $this->forumTopics[] = $forumTopic;
            $forumTopic->setOwner($this);
        }

        return $this;
    }

    public function removeForumTopic(ForumTopic $forumTopic): self
    {
        if ($this->forumTopics->removeElement($forumTopic)) {
            // set the owning side to null (unless already changed)
            if ($forumTopic->getOwner() === $this) {
                $forumTopic->setOwner(null);
            }
        }

        return $this;
    }
}
