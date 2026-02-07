<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Order;
use App\Entity\PayType;
use App\Entity\Service;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[Route(path: '/order')]
final class OrderController extends AbstractController
{
    #[Route(name: 'app_order_list')]
    public function list(EntityManagerInterface $em): Response
    {
        $user = $this->getUser() ?? throw $this->createAccessDeniedException();
        $orders = $em->getRepository(Order::class)->findBy(['customer' => $user], ['orderTimestamp' => 'DESC']);

        return $this->render('page/order/list.html.twig', ['orders' => $orders]);
    }

    #[Route(path: '/new', name: 'app_order_new')]
    public function booking(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser() ?? throw $this->createAccessDeniedException();
        \assert($user instanceof User);

        $form = $this->createFormBuilder()
            ->add('service', EnumType::class, [
                'required' => true,
                'label' => 'Услуга',
                'class' => Service::class,
                'choice_label' => 'title',
            ])
            ->add('address', TextType::class, [
                'required' => true,
                'label' => 'Адрес',
                'constraints' => [
                    new Assert\NotBlank(),
                ]
            ])
            ->add('orderTimestamp', DateTimeType::class, [
                'required' => true,
                'label' => 'Дата и время',
                'input' => 'datetime_immutable',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Range(min: new DateTimeType()),
                ]
            ])
            ->add('contactPhone', TextType::class, [
                'required' => true,
                'data' => $user->getPhone(),
                'label' => 'Контактный телефон',
                'attr' => ['data-mask' => '+{7}(000)-000-00-00'],
                'constraints' => [new Assert\NotBlank(), new Assert\Regex(pattern: '/^\+7\(\d{3}\)\-\d{3}\-\d{2}\-\d{2}$/')],
            ])
            ->add('payType', EnumType::class, [
                'required' => true,
                'label' => 'Способ оплаты',
                'class' => PayType::class,
                'choice_label' => 'title',
            ])
            ->add('submit', SubmitType::class, ['label' => 'Забронировать'])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $booking = Order::new(
                id: Uuid::v7(),
                customer: $user,
                orderTimestamp: $form->get('orderTimestamp')->getData(),
                address: $form->get('address')->getData(),
                contactPhone: $form->get('contactPhone')->getData(),
                service: $form->get('service')->getData(),
                payType: $form->get('payType')->getData(),
                timestamp: new \DateTimeImmutable(),
            );
            $em->persist($booking);
            $em->flush();
            $this->addFlash('success', 'Бронирование успешно создано');

            return $this->redirectToRoute('app_order_list');
        }

        return $this->render('page/order/item.html.twig', ['form' => $form->createView()]);
    }
}
