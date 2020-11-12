<?php


namespace App\Appointment\Infrastructure\Service;

use App\Appointment\Domain\Repository\AppointmentRepositoryInterface;
use App\Appointment\Domain\Service\AppointmentServiceInterface;
use App\Appointment\Infrastructure\Entity\Appointment;

class AppointmentService implements AppointmentServiceInterface
{
    private $appointmentRepository;

    public function __construct(AppointmentRepositoryInterface $appointmentRepository)
    {
        $this->appointmentRepository = $appointmentRepository;
    }

    public function book(Appointment $appointment)
    {
        $canBook = $this->appointmentRepository->checkAvailability($appointment);

        if (!$canBook) {
            return [
                'status' => 'error',
                'message' => 'The selected time is not available, please pick another time.',
            ];
        }

        $this->appointmentRepository->save($appointment);

        return [
            'status' => 'success',
            'message' => '',
        ];

    }
}