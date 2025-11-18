<?php

namespace App\Entity;

use App\Repository\SkillRepository;
use App\Trait\SluggableTrait;
use App\Trait\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ORM\Entity(repositoryClass: SkillRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Skill
{
    use TimestampableTrait;
    use SluggableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['skill.read'])]
    private ?int $id = null;

    /**
     * @var Collection<int, Branch>
     */
    #[ORM\ManyToMany(targetEntity: Branch::class, mappedBy: 'skills')]
    #[Ignore]
    private Collection $branches;

    /**
     * @var Collection<int, Objective>
     */
    #[ORM\OneToMany(targetEntity: Objective::class, mappedBy: 'skill')]
    #[Ignore]
    private Collection $objectives;

    public function __construct()
    {
        $this->branches = new ArrayCollection();
        $this->objectives = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Branch>
     */
    public function getBranches(): Collection
    {
        return $this->branches;
    }

    public function addBranch(Branch $branch): static
    {
        if (!$this->branches->contains($branch)) {
            $this->branches->add($branch);
            $branch->addSkill($this);
        }

        return $this;
    }

    public function removeBranch(Branch $branch): static
    {
        if ($this->branches->removeElement($branch)) {
            $branch->removeSkill($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Objective>
     */
    public function getObjectives(): Collection
    {
        return $this->objectives;
    }

    public function addObjective(Objective $objective): static
    {
        if (!$this->objectives->contains($objective)) {
            $this->objectives->add($objective);
            $objective->setSkill($this);
        }

        return $this;
    }

    public function removeObjective(Objective $objective): static
    {
        if ($this->objectives->removeElement($objective)) {
            if ($objective->getSkill() === $this) {
                $objective->setSkill(null);
            }
        }

        return $this;
    }
}
