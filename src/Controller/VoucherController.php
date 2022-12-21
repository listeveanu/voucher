<?php

namespace App\Controller;

use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Voucher;
use App\Repository\VoucherRepository;
use Exception;

class VoucherController extends AbstractController
{
    const USED_VOUCHER = 1;
    private $voucherRepository;

    public function __construct(VoucherRepository $voucherRepository)
    {
        $this->voucherRepository = $voucherRepository;
    }

    /**
     * @Route("/api/vouchers", name="voucher_create", methods={"POST"})
     * @throws Exception
     */
    public function createAction(Request $request): Response
    {
        try {
            $voucher = new Voucher(
                $request->get('code'),
                $request->get('name'),
                $request->get('description'),
                (int)$request->get('discount_amount'),
                new DateTimeImmutable($request->get('expires_at')),
                0
            );
        } catch (Exception $exception) {
            return new Response('At least one invalid argument', 400);
        }

        $this->voucherRepository->add($voucher, true);

        return new JsonResponse(['status' => 'Created new voucher successfully with id ' . $voucher->getId()],
            Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/vouchers/{voucherId}", name="voucher_update", methods={"PUT"})
     * @throws Exception
     */
    public function updateAction(Request $request): Response
    {
        $voucherId = $request->get('voucherId');
        $voucher = $this->voucherRepository->findOneById($voucherId);

        if (!$voucher) {
            throw new NotFoundHttpException('Voucher not found');
        }

        $voucher->setCode($request->get('code'));
        $voucher->setName($request->get('name'));
        $voucher->setDescription($request->get('description'));
        $voucher->setDiscountAmount((int)$request->get('discount_amount'));
        $voucher->setExpiresAt(new DateTimeImmutable($request->get('expires_at')));
        $voucher->setUsed($request->get('used'));

        $voucherExpirationDate = $voucher->getExpiresAt();
        $now = new DateTimeImmutable("now");

        if ($voucher->getUsed() == self::USED_VOUCHER OR
            $voucherExpirationDate->format('Y-m-d H:i:s') < $now->format('Y-m-d H:i:s')) {
            throw new UnauthorizedHttpException('Voucher is not editable');
        }

        $this->voucherRepository->add($voucher, true);

        return new JsonResponse(['status' => 'Updated voucher successfully with id ' . $voucher->getId()],
            Response::HTTP_OK);
    }

    /**
     * @Route("/api/vouchers/{voucherId}", name="voucher_delete", methods={"DELETE"})
     * @throws Exception
     */
    public function deleteAction(Request $request): Response
    {
        $voucherId = $request->get('voucherId');
        $voucher = $this->voucherRepository->findOneById($voucherId);

        if (!$voucher) {
            throw new NotFoundHttpException('Voucher not found');
        }

        $this->voucherRepository->remove($voucher, true);

        return new JsonResponse(['status' => 'Voucher with id ' . $voucherId . ' deleted'],
            Response::HTTP_OK);
    }

    /**
     * @Route("/api/vouchers/active", name="voucher_list_active", methods={"GET"})
     * @throws Exception
     */
    public function listActiveAction(): Response
    {
        $vouchers = $this->voucherRepository->findActiveVouchers();

        $data = [];
        foreach ($vouchers as $voucher) {
            $data[] = $this->createDto($voucher);
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/api/vouchers/expired", name="voucher_list_expired", methods={"GET"})
     * @throws Exception
     */
    public function listExpiredAction(): Response
    {
        $vouchers = $this->voucherRepository->findExpiredVouchers();

        $data = [];
        foreach ($vouchers as $voucher) {
            $data[] = $this->createDto($voucher);
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    private function createDto(Voucher $voucher): array
    {
        return [
            'id' => $voucher->getId(),
            'code' => $voucher->getCode(),
            'name' => $voucher->getName(),
            'description' => $voucher->getDescription(),
            'discountAmount' => $voucher->getDiscountAmount(),
            'expiresAt' => $voucher->getExpiresAt(),
            'used' => $voucher->getUsed()
        ];
    }
}
