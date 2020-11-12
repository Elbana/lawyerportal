<?php


namespace App\Appointment\Presentation\Controller;

use App\Appointment\Domain\Model\AppointmentStatus;
use App\Appointment\Domain\Repository\AppointmentRepositoryInterface;
use App\Appointment\Domain\Service\AppointmentServiceInterface;
use App\Appointment\Infrastructure\Entity\Appointment;
use App\Appointment\Presentation\Form\Book;
use App\User\Infrastructure\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class AppointmentController extends AbstractController
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @Route("appointment/list", name="appointment/list")
     */
    public function list(Request $request, AppointmentRepositoryInterface $appointmentRepository, UserRepository $userRepository)
    {
        $currentPage = $request->query->get('page', 1);
        $limit = 10;
        $appointments = $appointmentRepository->fetchAll($currentPage);
        $maxPages = ceil($appointments->count() / $limit);
        $thisPage = $currentPage;
        // Pass through the 3 above variables to calculate pages in twig

        return $this->render('list/index.html.twig', [
            'appointments' => $appointments,
            'lawyers' => $userRepository->getLawyers(),
            'citizens' => $userRepository->getCitizens(),
            'maxPages'=> $maxPages,
            'thisPage' => $thisPage
        ]);
    }

    /**
     * @Route("appointment/edit", name="appointment/edit")
     */
    public function edit(Request $request, AppointmentRepositoryInterface $appointmentRepository)
    {
        $appointmentId = $request->query->get('id');
        $action = $request->query->get('action');

        if (null !== $appointmentId) {
            $appointment = $appointmentRepository->findOneBy(['id' => $appointmentId]);
            if ('reject' === $action) {
                $appointment->setStatus(AppointmentStatus::REJECTED);
            } elseif ('approve' === $action) {
                $appointment->setStatus(AppointmentStatus::APPROVED);
            }

            $appointmentRepository->save($appointment);

            return $this->redirect('list');
        }
    }

    /**
     * @Route("appointment/save", name="appointment/save")
     */
    public function save(Request $request, AppointmentRepositoryInterface $appointmentRepository, UserRepository $userRepository)
    {
        $lawyerId = (int) $request->query->get('lawyer');
        $citizenId = (int) $request->query->get('citizen');
        $requestTime = htmlspecialchars($request->query->get('requestTime'), ENT_COMPAT);

        if (empty($lawyerId) || empty($citizenId) || empty($requestTime)) {
            return new Response('Please fill all the required fields!', 404);
        }

        $appointment = new Appointment();

        $lawyer = $userRepository->findOneBy(['id' => (int) $lawyerId]);
        $citizen = $userRepository->findOneBy(['id' => (int) $citizenId]);

        $appointment->setRequestTime($requestTime)
            ->setLawyer($lawyer)
            ->setCitizen($citizen)
            ->setStatus(AppointmentStatus::PENDING);
        $appointmentRepository->save($appointment);

        return new Response('Appointment saved!');
    }

    /**
     * @Route("appointment/book", name="appointment/book")
     */
    public function book(Request $request, AppointmentServiceInterface $appointmentService, AppointmentRepositoryInterface $appointmentRepository, UserRepository $userRepository)
    {
        $user = $this->security->getUser();

        $appointment = $appointmentRepository->findOneBy(['citizenId' => $user->getId(), 'status' => AppointmentStatus::PENDING]) ?? new Appointment();

        $form = $this->createForm(Book::class, $appointment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (null !== $request->request->get('update') && $appointment->getId()) {
                $appointmentRepository->save($appointment);
            }

            if (null !== $request->request->get('cancel') && $appointment->getId()) {
                $appointment->setStatus(AppointmentStatus::REJECTED);
                $appointmentRepository->save($appointment);

                return $this->redirectToRoute('appointment/book');
            }

            if (null !== $request->request->get('save')) {
                $lawyer = $userRepository->findOneBy(['id' => $appointment->getLawyerId()]);

                $appointment->setStatus(AppointmentStatus::PENDING)
                    ->setCitizen($user)
                    ->setLawyer($lawyer);
                $response = $appointmentService->book($appointment);

                if ('error' === $response['status']) {
                    $form->addError(new FormError($response['message']));
                }
            }
        }

        return $this->render('appointment/book.html.twig', [
            'form' => $form->createView(),
            'booked' => $appointment->getId() ? true : false,
            'recentAppointments' => $appointmentRepository->fetchRecent($user->getId())
        ]);
    }
}