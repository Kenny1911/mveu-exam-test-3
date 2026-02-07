<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

final class AuthController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('page/login/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/register', name: 'app_register')]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        PasswordHasherFactoryInterface $hasherFactory
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $hasher = $hasherFactory->getPasswordHasher(User::class);
        $form = $this->createFormBuilder()
            ->add('username', TextType::class, [
                'required' => true,
                'label' => 'Логин',
                'constraints' => [new Assert\NotBlank(), new Assert\Length(min: 1)],
            ])
            ->add('password', RepeatedType::class, [
                'required' => true,
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Пароль'],
                'second_options' => ['label' => 'Повторите пароль'],
                'constraints' => [new Assert\NotBlank(), new Assert\Length(min: 8)],
            ])
            ->add('lastName', TextType::class, [
                'required' => true,
                'label' => 'Фамилия',
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('firstName', TextType::class, [
                'required' => true,
                'label' => 'Имя',
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('middleName', TextType::class, [
                'required' => true,
                'label' => 'Отчество',
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'label' => 'Email',
                'constraints' => [new Assert\NotBlank(), new Assert\Email()],
            ])
            ->add('phone', TextType::class, [
                'required' => true,
                'label' => 'Телефон',
                'attr' => ['data-mask' => '+{7}(000)-000-00-00'],
                'constraints' => [new Assert\NotBlank(), new Assert\Regex(pattern: '/^\+7\(\d{3}\)\-\d{3}\-\d{2}\-\d{2}$/')],
            ])
            ->add('submit', SubmitType::class, ['label' => 'Зарегистрироваться'])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $username = (string) $form->get('username')->getData();

            if ($em->getRepository(User::class)->count(['username' => $username]) > 0) {
                $this->addFlash('error', 'Пользователь с таким логином уже существует.');

                goto stopHandleForm;
            }

            $user = new User(
                id: Uuid::v7(),
                username: $username,
                password: $hasher->hash((string) $form->get('password')->getData()),
                firstName: (string) $form->get('firstName')->getData(),
                lastName: (string) $form->get('lastName')->getData(),
                middleName: (string) $form->get('middleName')->getData(),
                email: (string) $form->get('email')->getData(),
                phone: (string) $form->get('phone')->getData(),
                roles: [User::ROLE_USER],
            );
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Регистрация прошла успешно');

            return $this->redirectToRoute('app_login');
        }

        stopHandleForm:

        return $this->render('page/login/register.html.twig', ['form' => $form]);
    }
}
