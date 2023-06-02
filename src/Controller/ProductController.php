<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/api/products', name: 'products', methods: ['GET'])]
    public function getProductList(): JsonResponse
    {
        // Todo - Switch to API Platform

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/api/products/{id}', name: 'detailProduct', methods: ['GET'])]
    public function getDetailProduct(): JsonResponse
    {
        // Todo - Switch to API Platform

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/api/products/{id}', name: 'deleteProduct', methods: ['DELETE'])]
    public function deleteProduct(): JsonResponse
    {
        // Todo - Switch to API Platform

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }


    #[Route('/api/products', name: 'createProduct', methods: ['POST'])]
    public function createProduct(): JsonResponse
    {
        // Todo - Switch to API Platform

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }


    #[Route('/api/products/{id}', name: 'updateProduct', methods: ['PUT'])]
    public function updateProduct(): JsonResponse
    {
        // Todo - Switch to API Platform

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}
