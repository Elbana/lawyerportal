<?php

namespace App\Appointment\Domain\Model;

class Appointment
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var int
     */
    private $lawyerId;
    /**
     * @var int
     */
    private $citizenId;
    /**
     * @var string
     */
    private $requestedTime;
    /**
     * @var int
     */
    private $status;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getLawyerId(): int
    {
        return $this->lawyerId;
    }

    /**
     * @param int $lawyerId
     */
    public function setLawyerId(int $lawyerId): void
    {
        $this->lawyerId = $lawyerId;
    }

    /**
     * @return int
     */
    public function getCitizenId(): int
    {
        return $this->citizenId;
    }

    /**
     * @param int $citizenId
     */
    public function setCitizenId(int $citizenId): void
    {
        $this->citizenId = $citizenId;
    }

    /**
     * @return string
     */
    public function getDateTime(): string
    {
        return $this->requestedTime;
    }

    /**
     * @param string $requestedTime
     */
    public function setDateTime(string $requestedTime): void
    {
        $this->requestedTime = $requestedTime;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }
}
