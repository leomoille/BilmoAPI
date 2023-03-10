<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Service\ClientPropertyChecker;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    #[Route('/api/users', name: 'users', methods: ['GET'])]
    public function getUserList(UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        $productList = $userRepository->findBy(['client' => $this->getUser()]);
        $context = SerializationContext::create()->setGroups(['getUsers']);
        $jsonUserList = $serializer->serialize($productList, 'json', $context);

        return new JsonResponse($jsonUserList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/users/{id}', name: 'detailUser', methods: ['GET'])]
    public function getDetailProduct(
        User $user,
        SerializerInterface $serializer,
        ClientPropertyChecker $clientPropertyChecker
    ): JsonResponse {
        $clientPropertyChecker->control($user->getClient(), $this->getUser());

        $context = SerializationContext::create()->setGroups(['getUsers']);
        $jsonUser = $serializer->serialize($user, 'json', $context);

        return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);
    }

    #[Route('/api/users/{id}', name: 'deleteUser', methods: ['DELETE'])]
    public function deleteUser(
        User $user,
        EntityManagerInterface $entityManager,
        ClientPropertyChecker $clientPropertyChecker
    ): JsonResponse {
        $clientPropertyChecker->control($user->getClient(), $this->getUser());

        $entityManager->remove($user);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/users', name: "createUser", methods: ['POST'])]
    public function createUser(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        ValidatorInterface $validator
    ): JsonResponse {
        /** @var User $user */
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');

        $errors = $validator->validate($user);
        if ($errors->count() > 0) {
            throw new HttpException(JsonResponse::HTTP_BAD_REQUEST, $errors[0]->getMessage());
        }

        $user->setClient($this->getUser());
        $entityManager->persist($user);
        $entityManager->flush();

        $context = SerializationContext::create()->setGroups(['getUsers']);
        $jsonProduct = $serializer->serialize($user, 'json', $context);
        $location = $urlGenerator->generate(
            'detailUser',
            ['id' => $user->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new JsonResponse($jsonProduct, Response::HTTP_CREATED, ['Location' => $location], true);
    }

    #[Route('/api/users/{id}', name: 'updateUser', methods: ['PUT'])]
    public function updateProduct(
        User $currentUser,
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ProductRepository $productRepository,
        ValidatorInterface $validator
    ): JsonResponse {
        if ($currentUser->getClient() !== $this->getUser()) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        /** @var User $newUser */
        $newUser = $serializer->deserialize($request->getContent(), User::class, 'json');
            !$currentUser->getEmail() ?? $currentUser->setEmail($newUser->getEmail()());
            !$currentUser->getUsername() ?? $currentUser->setUsername($newUser->getUsername());

        $errors = $validator->validate($currentUser);
        if ($errors->count() > 0) {
            throw new HttpException(JsonResponse::HTTP_BAD_REQUEST, $errors[0]->getMessage());
        }

        $content = $request->toArray();
        $productsId = $content['productsId'] ?? -1;
        $addedProducts = $productRepository->findBy(['id' => $productsId]) ?? null;

        if ($productsId) {
            for ($i = 0; $i < count($addedProducts); $i++) {
                $currentUser->addProduct($addedProducts[$i]);
            }
        }

        $entityManager->persist($currentUser);
        $entityManager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
