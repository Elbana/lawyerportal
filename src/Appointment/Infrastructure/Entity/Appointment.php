<?php

namespace App\Appointment\Infrastructure\Entity;

use App\Appointment\Infrastructure\Repository\AppointmentRepository;
use App\User\Infrastructure\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AppointmentRepository::class)
 */
class Appointment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $lawyerId;

    /**
     * @ORM\Column(type="integer")
     */
    private $citizenId;

    /**
     * @ORM\Column(type="datetime")
     */
    private $request_time;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $proposed_time;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;


    /**
     * @ORM\ManyToOne(targetEntity="App\User\Infrastructure\Entity\User", inversedBy="Appointment")
     */
    private $lawyer;

    /**
     * @ORM\ManyToOne(targetEntity="App\User\Infrastructure\Entity\User", inversedBy="Appointment")
     */
    private $citizen;


    /**
     * @return mixed
     */
    public function getLawyer()
    {
        return $this->lawyer;
    }

    /**
     * @return mixed
     */
    public function getCitizen()
    {
        return $this->citizen;
    }

    public function setLawyer($lawyer)
    {
        $this->lawyer = $lawyer;
        $this->lawyerId = $lawyer->getId();


        return $this;
    }

    public function setCitizen($citizen)
    {
        $this->citizen = $citizen;
        $this->citizenId = $citizen->getId();
        
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLawyerId(): ?int
    {
        return $this->lawyerId;
    }

    public function setLawyerId($lawyerId): self
    {
        if ($lawyerId instanceof User) {
            $lawyerId = $lawyerId->getId();
        }

        $this->lawyerId = $lawyerId;

        return $this;
    }

    public function getCitizenId(): ?int
    {
        return $this->citizenId;
    }

    public function setCitizenId(int $citizenId): self
    {
        $this->citizenId = $citizenId;

        return $this;
    }

    public function getRequestTime() : ?\DateTimeInterface
    {
        return $this->request_time;
    }

    public function setRequestTime(string $requestTime): self
    {
        $this->request_time = new \DateTimeImmutable($requestTime);

        return $this;
    }

    public function getProposedTime(): ?\DateTimeInterface
    {
        return $this->proposed_time;
    }

    public function setProposedTime(?\DateTimeInterface $proposedTime): self
    {
        $this->proposedTime = $proposedTime;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }
}
