<?php

namespace App\Controller;

use App\Entity\Voucher;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Repository\VoucherRepository;
use Exception;

class OrderController extends AbstractController
{
    const USED_VOUCHER = 1;

    private $orderRepository;
    private $voucherRepository;

    public function __construct(OrderRepository $orderRepository, VoucherRepository $voucherRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->voucherRepository = $voucherRepository;
    }

    /**
     * @Route("/api/orders", name="order_create", methods={"POST"})
     * @throws Exception
     */
    public function createAction(Request $request): Response
    {
        $purchaseDate = new DateTimeImmutable($request->get('purchase_date'));
        $amount = $request->get('amount');
        $voucherId = $request->get('voucher_id');

        $order = new Order();
        $order->setPurchasedDate($purchaseDate);
        $order->setAmount($amount);

        if ($voucherId) {
            $voucher = $this->voucherRepository->getActiveVoucher($voucherId);
            if ($voucher) {
                $order->setAmount($this->calculateOrderValue($amount, $voucher->getDiscountAmount()));
                $order->setVoucherId($voucherId);

                $this->markVoucherAsUsed($voucher);
            }
        }

        $this->orderRepository->add($order, true);

        return new JsonResponse(['status' => 'Created new order successfully with id ' . $order->getId()],
            Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/orders/{page}", name="order_list", methods={"GET"})
     * @throws Exception
     */
    public function listAction($page): Response
    {
        if ((int) $page <= 0) {
            throw new BadRequestHttpException('Page is not an integer');
        }

        $orders = $this->orderRepository->findOrders($page);

        $data = [];
        foreach ($orders as $order) {
            $data[] = [
                'id' => $order->getId(),
                'purchased_date' => $order->getPurchasedDate(),
                'amount' => $order->getAmount(),
                'voucher_id' => $order->getVoucherId()
            ];
        }
        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @param Voucher $voucher
     * @return void
     */
    private function markVoucherAsUsed(Voucher $voucher): void
    {
        $voucher->setUsed(self::USED_VOUCHER);
        $this->voucherRepository->add($voucher, true);
    }

    /**
     * @param $amount
     * @param $voucherValue
     * @return int
     */
    private function calculateOrderValue($amount, $voucherValue): int
    {
        if ($amount > $voucherValue) {
            return $amount - $voucherValue;
        } else {
            return 0;
        }
    }
}