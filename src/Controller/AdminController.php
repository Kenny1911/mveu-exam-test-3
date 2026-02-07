<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[Route(path: '/admin')]
final class AdminController extends AbstractController
{
    #[Route(name: 'app_admin_order_list')]
    public function bookingList(EntityManagerInterface $em): Response
    {
        $orders = $em->getRepository(Order::class)->findBy([], ['createdAt' => 'DESC']);

        return $this->render('page/admin/order-list.html.twig', ['orders' => $orders]);
    }

    #[Route(path: '/order/{id}', name: 'app_admin_order', requirements: ['id' => Requirement::UUID])]
    public function booking(Uuid $id, Request $request, EntityManagerInterface $em): Response
    {
        $order = $em->getRepository(Order::class)->find($id) ?? throw $this->createNotFoundException();

        $form = $this->createFormBuilder()
            ->add('status', EnumType::class, [
                'required' => true,
                'label' => 'Статус',
                'class' => OrderStatus::class,
                'choices' => [OrderStatus::APPROVE, OrderStatus::COMPLETE, OrderStatus::CANCEL],
                'choice_label' => 'title',
                'placeholder' => 'Выберите статус',
                'constraints' => [new Assert\NotBlank(),]
            ])
            ->add('comment', TextareaType::class, [
                'required' => false,
                'label' => 'Комментарий',
            ])
            ->add('submit', SubmitType::class, ['label' => 'Сменить статус'])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $status = $form->get('status')->getData();
            \assert($status instanceof OrderStatus);
            $comment = (string) $form->get('comment')->getData();

            if (OrderStatus::CANCEL === $status && '' === $comment) {
                $form->get('comment')->addError(new FormError('Обязательно при отмене заявки.'));
                goto stopHandleForm;
            }

            $order->changeStatus(
                status: $status,
                comment: $comment,
                timestamp: new \DateTimeImmutable(),
            );
            $em->persist($order);
            $em->flush();

            $this->addFlash('success', \sprintf('Заявка %s', $status->title()));

            return $this->redirectToRoute('app_admin_order', ['id' => $id]);
        }

        stopHandleForm:

        return $this->render('page/admin/order.html.twig', [
            'order' => $order,
            'form' => in_array($order->status, [OrderStatus::NEW, OrderStatus::APPROVE], true) ? $form->createView() : null,
        ]);
    }
}
