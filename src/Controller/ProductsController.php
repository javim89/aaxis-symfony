<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Products;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

#[Route('/api', name: 'api_')]
class ProductsController extends AbstractController
{
    #[Route('/products', name: 'products_index', methods:["get"])]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: "Product list",
        content: new OA\JsonContent(
            type: "array",
            items: new OA\Items(ref: "#/components/schemas/ProductResponse")
        )
    )]
    // #[Nelmio\Security(name: 'Bearer')]
    public function index(ManagerRegistry $doctrine): JsonResponse
    {
        $products = $doctrine
            ->getRepository(Products::class)
            ->findAll();
   
        $data = [];
   
        foreach ($products as $product) {
           $data[] = [
               'id' => $product->getId(),
               'name' => $product->getName(),
               'description' => $product->getDescription(),
           ];
        }
   
        return $this->json([
            "data" => $data
        ], 202);
    }

    #[Route('/products', name: 'products_create', methods:['post'] )]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(
        type: "array",
        items: new OA\Items(ref: "#/components/schemas/ProductRequest")
    ))]
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: "Products created",
        content: new OA\JsonContent(
            type: "array",
            items: new OA\Items(ref: "#/components/schemas/ProductResponse")
        )
    )]
    public function create(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        
        $requestData = json_decode($request->getContent(), true);
        $createdProducts = [];
        try{
            foreach ($requestData as $data) {
                $product = new Products();
                $product->setSku($data['sku']);
                $product->setName($data['name']);
                $product->setDescription($data['description'] ?? null);
        
                $entityManager->persist($product);
                $createdProducts[] = [
                    'id' => $product->getId(),
                    'sku' => $product->getSku(),
                    'name' => $product->getName(),
                    'description' => $product->getDescription(),
                ];
                
            }
            $entityManager->flush();
            return $this->json([
                "data" => $createdProducts
            ]);
        }
        catch (\Exception $e) {
            return $this->json([
                "error" => $e->getMessage()
            ]);
        }
    }

    #[Route('/products/{id}', name: 'product_show', methods:['get'] )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: "Product details",
        content: new OA\JsonContent(ref: "#/components/schemas/ProductResponse")
    )]
    public function show(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $product = $doctrine->getRepository(Products::class)->find($id);
   
        if (!$product) {
   
            return $this->json('No product found for id ' . $id, 404);
        }
   
        $data =  [
            'id' => $product->getId(),
            'sku' => $product->getSku(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
        ];
           
        return $this->json([
            "data" => $data
        ]);
    }
 
    #[Route('/products', name: 'product_update', methods:['put', 'patch'] )]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(
        type: "array",
        items: new OA\Items(ref: "#/components/schemas/ProductRequest")
    ))]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: "Products updated",
        content: new OA\JsonContent(
            type: "array",
            items: new OA\Items(ref: "#/components/schemas/ProductResponse")
        )
    )]
    public function update(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $requestData = json_decode($request->getContent(), true);
        $updatedProducts = [];
        try {
            foreach ($requestData as $data) {
                $product = $entityManager->getRepository(Products::class)->findOneBy(['sku' => $data['sku']]);
                if (!$product) {
                    return $this->json('No product found for sku ' . $data['sku'], 404);
                }
                $product->setName($data['name']);
                $product->setDescription($data['description'] ?? null);

                $updatedProducts[] = [
                    'id' => $product->getId(),
                    'sku' => $product->getSku(),
                    'name' => $product->getName(),
                    'description' => $product->getDescription(),
                ];
            }
            $entityManager->flush();
        
            return $this->json([
                "data" => $updatedProducts
            ]);
        }catch (\Exception $e) {
            return $this->json([
                "error" => $e->getMessage()
            ]);
        }
    }
 
    #[Route('/products/{id}', name: 'product_delete', methods:['delete'] )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: "Products deleted",
        content: null
    )]
    public function delete(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $product = $entityManager->getRepository(Products::class)->find($id);
   
        if (!$product) {
            return $this->json('No product found for id' . $id, 404);
        }
   
        $entityManager->remove($product);
        $entityManager->flush();
   
        return $this->json('Deleted a project successfully with id ' . $id);
    }
}
