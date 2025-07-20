<?php

namespace App\Entity;

use App\Repository\PermissionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PermissionRepository::class)]
class Permission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    /**
     * @var Collection<int, RolePermission>
     */
    #[ORM\OneToMany(targetEntity: RolePermission::class, mappedBy: 'permission')]
    private Collection $rolePermissions;

    public function __construct()
    {
        $this->rolePermissions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, RolePermission>
     */
    public function getRolePermissions(): Collection
    {
        return $this->rolePermissions;
    }

    public function addRolePermission(RolePermission $rolePermission): static
    {
        if (!$this->rolePermissions->contains($rolePermission)) {
            $this->rolePermissions->add($rolePermission);
            $rolePermission->setPermission($this);
        }

        return $this;
    }

    public function removeRolePermission(RolePermission $rolePermission): static
    {
        if ($this->rolePermissions->removeElement($rolePermission)) {
            // set the owning side to null (unless already changed)
            if ($rolePermission->getPermission() === $this) {
                $rolePermission->setPermission(null);
            }
        }

        return $this;
    }
}
