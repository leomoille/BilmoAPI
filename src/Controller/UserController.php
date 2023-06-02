<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    #[Route('/api/users', name: 'users', methods: ['GET'])]
    public function getUserList(): JsonResponse
    {
        // Todo - Switch to API Platform

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }


    #[Route('/api/users/{id}', name: 'detailUser', methods: ['GET'])]
    public function getDetailProduct(): JsonResponse
    {
        // Todo - Switch to API Platform

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }


    #[Route('/api/users/{id}', name: 'deleteUser', methods: ['DELETE'])]
    public function deleteUser(): JsonResponse
    {
        // Todo - Switch to API Platform

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }


    #[Route('/api/users', name: 'createUser', methods: ['POST'])]
    public function createUser(): JsonResponse
    {
        // Todo - Switch to API Platform

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }


    #[Route('/api/users/{id}', name: 'updateUser', methods: ['PUT'])]
    public function updateProduct(): JsonResponse
    {
        // Todo - Switch to API Platform

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}
