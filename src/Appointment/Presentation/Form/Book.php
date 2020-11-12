<?php

namespace App\Appointment\Presentation\Form;

use App\Appointment\Infrastructure\Entity\Appointment;
use App\User\Infrastructure\Entity\User;
use App\User\Infrastructure\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Book extends AbstractType
{
    /**
     * @var UserRepository $userRepository
     */
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $requestTime = $options['data']->getRequestTime();
        $builder
            ->add('requestTime', TextType::class, [
                'label' => false,
                'data' => $requestTime ? $requestTime->format('m/d/Y H:i') : (new \DateTime())->format('d/m/Y H:i')
            ])
            ->add('lawyerId', ChoiceType::class, [
                'choices'  => $this->userRepository->getLawyers(),
                'choice_label' => 'name',
                'required' => true,
                'expanded' => false,
                'label'=> false,
                'data' => $this->userRepository->findOneBy(['id' => $options['data']->getLawyerId()])
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Appointment::class,
        ]);
    }
}