<?php

namespace App\Controller\Api;

use App\Entity\Customer;
use App\Entity\RepairOrder;
use App\Repository\QuoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/repair-orders')]
class RepairOrderController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function list(EntityManagerInterface $em)
    {
        $repairOrders = $em->getRepository(RepairOrder::class)->findAll();

        $data = [];
        foreach ($repairOrders as $repairOrder) {
            $data[] = [
                'id'          => $repairOrder->id,
                'reference'   => $repairOrder->reference,
                'status'      => $repairOrder->status,
                'totalAmount' => $repairOrder->totalAmount,
                'createdAt'   => $repairOrder->createdAt->format('Y-m-d H:i:s'),
                'description' => $repairOrder->description,
                'customer'    => $repairOrder->customer ? [
                    'id'    => $repairOrder->customer->id,
                    'name'  => $repairOrder->customer->name,
                    'email' => $repairOrder->customer->email,
                ] : null,
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $em, QuoteRepository $quoteRepository)
    {
        $repairOrder = $em->find(RepairOrder::class, $id);

        if (!$repairOrder) {
            return new JsonResponse(['error' => 'Ordre de réparation introuvable'], 404);
        }

        $latestQuote = $quoteRepository->findLatestByRepairOrderId($repairOrder->id);
        // Non fait car manque de temps:
        // - Injecter le QuoteService
        // - Calculer les totaux de chaque ligne (en utilisant QuoteService) et les ajouter dans un tableau ($quote['lines'])
        // - Calculer les montants HT et TTC (en utilisant QuoteService) et les ajouter dans un tableau ($quote)
        $quote = [
            // A remplir ...
        ];
        
        return new JsonResponse([
            'id'          => $repairOrder->id,
            'reference'   => $repairOrder->reference,
            'status'      => $repairOrder->status,
            'totalAmount' => $repairOrder->totalAmount,
            'createdAt'   => $repairOrder->createdAt->format('Y-m-d H:i:s'),
            'description' => $repairOrder->description,
            'customer'    => $repairOrder->customer ? [
                'id'    => $repairOrder->customer->id,
                'name'  => $repairOrder->customer->name,
                'email' => $repairOrder->customer->email,
                'phone' => $repairOrder->customer->phone,
            ] : null,
            'quote'     => $quote,
        ]);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em)
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['description'])) {
            return new JsonResponse(['error' => 'La description est obligatoire'], 400);
        }

        // Validation du statut — DUPLIQUER : même logique dans updateStatus()
        $validStatuses = ['PENDING', 'IN_PROGRESS', 'WAITING_PARTS', 'DONE', 'DELIVERED', 'CANCELLED'];
        $status = $data['status'] ?? 'PENDING';
        if (!in_array($status, $validStatuses)) {
            return new JsonResponse(['error' => 'Statut invalide : ' . $status . '. Valeurs acceptées : ' . implode(', ', $validStatuses)], 400);
        }

        // Résolution du client — logique métier dans le contrôleur
        $customer = null;
        if (isset($data['customer'])) {
            if (isset($data['customer']['id'])) {
                $customer = $em->find(Customer::class, $data['customer']['id']);
                if (!$customer) {
                    return new JsonResponse(['error' => 'Client introuvable'], 404);
                }
            } else {
                if (empty($data['customer']['name'])) {
                    return new JsonResponse(['error' => 'Le nom du client est obligatoire'], 400);
                }
                $customer = new Customer();
                $customer->name  = $data['customer']['name'];
                $customer->email = $data['customer']['email'] ?? null;
                $customer->phone = $data['customer']['phone'] ?? null;
                $em->persist($customer);
            }
        }

        $repairOrder              = new RepairOrder();
        $repairOrder->reference   = 'OR-' . strtoupper(substr(md5(uniqid()), 0, 8));
        $repairOrder->status      = $status;
        $repairOrder->customer    = $customer;
        $repairOrder->description = $data['description'];

        $em->persist($repairOrder);
        $em->flush();

        return new JsonResponse([
            'id'        => $repairOrder->id,
            'reference' => $repairOrder->reference,
            'status'    => $repairOrder->status,
        ], 201);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(int $id, Request $request, EntityManagerInterface $em)
    {
        $repairOrder = $em->find(RepairOrder::class, $id);

        if (!$repairOrder) {
            return new JsonResponse(['error' => 'Ordre de réparation introuvable'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['description'])) {
            if (empty($data['description'])) {
                return new JsonResponse(['error' => 'La description ne peut pas être vide'], 400);
            }
            $repairOrder->description = $data['description'];
        }

        // Validation du statut — DUPLIQUÉ : même logique dans create() et updateStatus()
        if (isset($data['status'])) {
            $validStatuses = ['PENDING', 'IN_PROGRESS', 'WAITING_PARTS', 'DONE', 'DELIVERED', 'CANCELLED'];
            if (!in_array($data['status'], $validStatuses)) {
                return new JsonResponse(['error' => 'Statut invalide : ' . $data['status'] . '. Valeurs acceptées : ' . implode(', ', $validStatuses)], 400);
            }

            // Règles de transition — logique métier dans le contrôleur
            if ($repairOrder->status === 'CANCELLED') {
                return new JsonResponse(['error' => 'Impossible de modifier un ordre annulé'], 400);
            }
            if ($repairOrder->status === 'DELIVERED' && $data['status'] !== 'CANCELLED') {
                return new JsonResponse(['error' => 'Un ordre livré ne peut être que annulé'], 400);
            }

            $repairOrder->status = $data['status'];
        }

        $em->flush();

        return new JsonResponse([
            'id'          => $repairOrder->id,
            'reference'   => $repairOrder->reference,
            'status'      => $repairOrder->status,
            'description' => $repairOrder->description,
        ]);
    }

    #[Route('/{id}/status', methods: ['PATCH'])]
    public function updateStatus($id, Request $request, EntityManagerInterface $em)
    {
        $repairOrder = $em->find(RepairOrder::class, $id);

        if (!$repairOrder) {
            return new JsonResponse(['error' => 'Ordre de réparation introuvable'], 404);
        }

        $data      = json_decode($request->getContent(), true);
        $newStatus = $data['status'] ?? null;

        // Validation du statut — DUPLIQUÉ : même logique dans create() et update()
        $validStatuses = ['PENDING', 'IN_PROGRESS', 'WAITING_PARTS', 'DONE', 'DELIVERED', 'CANCELLED'];
        if (!$newStatus || !in_array($newStatus, $validStatuses)) {
            return new JsonResponse(['error' => 'Statut invalide : ' . $newStatus . '. Valeurs acceptées : ' . implode(', ', $validStatuses)], 400);
        }

        // Règles de transition — DUPLIQUÉ : même logique dans update()
        if ($repairOrder->status === 'CANCELLED') {
            return new JsonResponse(['error' => 'Impossible de modifier un ordre annulé'], 400);
        }
        if ($repairOrder->status === 'DELIVERED' && $newStatus !== 'CANCELLED') {
            return new JsonResponse(['error' => 'Un ordre livré ne peut être que annulé'], 400);
        }

        $repairOrder->status = $newStatus;
        $em->flush();

        return new JsonResponse(['status' => $repairOrder->status]);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete($id, EntityManagerInterface $em)
    {
        $repairOrder = $em->find(RepairOrder::class, $id);

        if (!$repairOrder) {
            return new JsonResponse(['error' => 'Ordre de réparation introuvable'], 404);
        }

        if ($repairOrder->status === 'DELIVERED') {
            return new JsonResponse(['error' => 'Impossible de supprimer un ordre livré'], 400);
        }

        $em->remove($repairOrder);
        $em->flush();

        return new JsonResponse(null, 204);
    }
}
