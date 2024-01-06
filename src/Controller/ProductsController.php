<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Products;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api', name: 'api_')]
class ProductsController extends AbstractController
{
    #[Route('/products', name: 'products_index', methods:["get"])]
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
   
        return $this->json($data);
    }

    #[Route('/products', name: 'products_create', methods:['post'] )]
    public function create(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $entityManager = $doctrine->getManager();
   
        $product = new Products();
        $data = json_decode($request->getContent(), true);
        $product->setSku($data['sku']);
        $product->setName($data['name']);
        $product->setDescription($data['description']);

        $entityManager->persist($product);
        $entityManager->flush();
    
        $data =  [
            'id' => $product->getId(),
            'sku' => $product->getSku(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
        ];
                
        return $this->json($data);
        
    }

    #[Route('/products/{id}', name: 'product_show', methods:['get'] )]
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
           
        return $this->json($data);
    }
 
    #[Route('/products/{id}', name: 'product_update', methods:['put', 'patch'] )]
    public function update(ManagerRegistry $doctrine, Request $request, int $id): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $product = $entityManager->getRepository(Products::class)->find($id);
   
        if (!$product) {
            return $this->json('No product found for id' . $id, 404);
        }
   
        $product->setName($request->request->get('name'));
        $product->setDescription($request->request->get('description'));
        $entityManager->flush();
   
        $data =  [
            'id' => $product->getId(),
            'sku' => $product->getSku(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
        ];
           
        return $this->json($data);
    }
 
    #[Route('/products/{id}', name: 'product_delete', methods:['delete'] )]
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
