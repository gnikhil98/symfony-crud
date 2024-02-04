<?php
// src/Entity/Demo.php

namespace App\Entity;

use App\Repository\DemoRepository;
use Doctrine\ORM\Mapping as ORM;
use Andante\SoftDeletableBundle\SoftDeletable\SoftDeletableInterface;
use Andante\SoftDeletableBundle\SoftDeletable\SoftDeletableTrait;

/**
 * @ORM\Entity(repositoryClass=DemoRepository::class)
 */
class Demo implements SoftDeletableInterface 
{
    use SoftDeletableTrait;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;
      /**
     * @ORM\Column(type="string", length=255)
     */
    private $address;
      /**
     * @ORM\Column(type="string", length=255)
     */
    private $city;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $schoolname;
            /**
     * @ORM\Column(type="json")
     */
    private $quotationData = [];
//    /**
//      * @ORM\Column(type="datetime", nullable=true)
//      */
//     private $deletedAt;

    /**
     * @ORM\PrePersist
     */
    public function setTimestampsOnCreate(): void
    {
        $this->created_at = new \DateTime();
        $this->updated = new \DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function setTimestampsOnUpdate(): void
    {
        $this->updated = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    public function getSchoolname(): ?string
    {
        return $this->schoolname;
    }

    public function setSchoolname(?string $schoolname): self
    {
        $this->schoolname = $schoolname;

        return $this;
    }

    //  /**
    //  * Get the value of deletedAt
    //  *
    //  * @return \DateTimeInterface|null
    //  */
    // public function getDeletedAt(): ?\DateTimeInterface
    // {
    //     return $this->deletedAt;
    // }
// hello
// hello
    // /**
    //  * Set the value of deletedAt
    //  *
    //  * @param \DateTimeInterface|null $deletedAt
    //  * @return self
    //  */
    // public function setDeletedAt(?\DateTimeInterface $deletedAt): self
    // {
    //     $this->deletedAt = $deletedAt;

    //     return $this;
    // }
}
