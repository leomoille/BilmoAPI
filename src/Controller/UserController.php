<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    #[Route('/api/users', name: 'users', methods: ['GET'])]
    public function getUserList(UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        $productList = $userRepository->findAll();
        $jsonUserList = $serializer->serialize($productList, 'json', ['groups' => 'getUsers']);

        return new JsonResponse($jsonUserList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/users/{id}', name: 'detailUser', methods: ['GET'])]
    public function getDetailProduct(
        User $user,
        SerializerInterface $serializer,
    ): JsonResponse {
        $jsonUser = $serializer->serialize($user, 'json', ['groups' => 'getUsers']);

        return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);
    }

    #[Route('/api/users/{id}', name: 'deleteUser', methods: ['DELETE'])]
    public function deleteUser(User $user, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($user);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/users', name: "createUser", methods: ['POST'])]
    public function createUser(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator
    ): JsonResponse {
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');
        $entityManager->persist($user);
        $entityManager->flush();

        $jsonProduct = $serializer->serialize($user, 'json', ['groups' => 'getUsers']);
        $location = $urlGenerator->generate(
            'detailUser',
            ['id' => $user->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new JsonResponse($jsonProduct, Response::HTTP_CREATED, ['Location' => $location], true);
    }

    #[Route('/api/users/{id}', name: 'updateUser', methods: ['PUT'])]
    public function updateProduct(
        User $user,
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ProductRepository $productRepository
    ): JsonResponse {
        /** @var User $updatedUser */
        $updatedUser = $serializer->deserialize(
            $request->getContent(),
            User::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $user]
        );

        $content = $request->toArray();
        $productsId = $content['productsId'] ?? -1;
        $addedProducts = $productRepository->findBy(['id' => $productsId]) ?? null;

        if ($productsId) {
            for ($i = 0; $i < count($addedProducts); $i++) {
                $updatedUser->addProduct($addedProducts[$i]);
            }
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
