<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Service\ClientPropertyChecker;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class UserController extends AbstractController
{
    /**
     * Cette méthode permet de récupérer l'ensemble de vos utilisateurs.
     *
     * @OA\Response(
     *     response=200,
     *     description="Retourne la liste des utilisateurs",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"getUsers"}))
     *     )
     * )
     * @OA\Tag(name="Utilisateurs")
     *
     *
     * @param UserRepository $userRepository
     * @param SerializerInterface $serializer
     * @param TagAwareCacheInterface $cache
     * @return JsonResponse
     * @throws InvalidArgumentException
     */
    #[Route('/api/users', name: 'users', methods: ['GET'])]
    public function getUserList(
        UserRepository $userRepository,
        SerializerInterface $serializer,
        TagAwareCacheInterface $cache
    ): JsonResponse {
        $idCache = "getAllUsers";

        $jsonUserList = $cache->get($idCache, function (ItemInterface $item) use ($userRepository, $serializer) {
            $item->tag("usersCache");
            $userList = $userRepository->findBy(['client' => $this->getUser()]);
            $context = SerializationContext::create()->setGroups(['getUsers']);

            return $serializer->serialize($userList, 'json', $context);
        });

        return new JsonResponse($jsonUserList, Response::HTTP_OK, [], true);
    }

    /**
     * Cette méthode permet de récupérer les informations d'un de vos utilisateurs.
     *
     * @OA\Response(
     *     response=200,
     *     description="Retourne les informations de l'utilisateur",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"getUsers"}))
     *     )
     * )
     * @OA\Tag(name="Utilisateurs")
     *
     *
     * @param User $user
     * @param SerializerInterface $serializer
     * @param ClientPropertyChecker $clientPropertyChecker
     * @param TagAwareCacheInterface $cache
     * @return JsonResponse
     * @throws InvalidArgumentException
     */
    #[Route('/api/users/{id}', name: 'detailUser', methods: ['GET'])]
    public function getDetailProduct(
        User $user,
        SerializerInterface $serializer,
        ClientPropertyChecker $clientPropertyChecker,
        TagAwareCacheInterface $cache
    ): JsonResponse {
        $clientPropertyChecker->control($user->getClient(), $this->getUser());

        $idCache = "getUser-".$user->getId();

        $jsonUser = $cache->get($idCache, function (ItemInterface $item) use ($user, $serializer) {
            $item->tag("user".$user->getId()."Cache");
            $context = SerializationContext::create()->setGroups(['getUsers']);

            return $serializer->serialize($user, 'json', $context);
        });

        return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);
    }

    /**
     * Cette méthode permet de supprimer un de vos utilisateurs.
     *
     * @OA\Response(
     *     response=204,
     *     description="Supprime un utilisateur",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"getUsers"}))
     *     )
     * )
     * @OA\Tag(name="Utilisateurs")
     *
     *
     * @param User $user
     * @param EntityManagerInterface $entityManager
     * @param ClientPropertyChecker $clientPropertyChecker
     * @param TagAwareCacheInterface $cachePool
     * @return JsonResponse
     * @throws InvalidArgumentException
     */
    #[Route('/api/users/{id}', name: 'deleteUser', methods: ['DELETE'])]
    public function deleteUser(
        User $user,
        EntityManagerInterface $entityManager,
        ClientPropertyChecker $clientPropertyChecker,
        TagAwareCacheInterface $cachePool
    ): JsonResponse {
        $clientPropertyChecker->control($user->getClient(), $this->getUser());

        $cachePool->invalidateTags([
            'usersCache',
            'user'.$user->getId().'Cache',
        ]);

        $entityManager->remove($user);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Cette méthode permet de créer un utilisateur.
     *
     * @OA\Response(
     *     response=201,
     *     description="Créer un utilisateur",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"getUsers"}))
     *     )
     * )
     * @OA\RequestBody(
     *     @OA\JsonContent(
     *         example={
     *             "username": "johndoe",
     *             "email": "john@doe.",
     *             "usersId": {21, 58}
     *         },
     *         @OA\Property(property="name", description="Nom du produit", type="string"),
     *         @OA\Property(property="price", description="Prix du produit", type="float"),
     *         @OA\Property(property="usersId", description="Liste des utilisateurs possedant le produit",
     *             type="array",
     *             @OA\Items(
     *                 type="int",
     *                 format="id"
     *             )
     *         ),
     *     )
     * )
     * @OA\Tag(name="Utilisateurs")
     *
     *
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param UrlGeneratorInterface $urlGenerator
     * @param ValidatorInterface $validator
     * @param TagAwareCacheInterface $cachePool
     * @return JsonResponse
     * @throws InvalidArgumentException
     */
    #[Route('/api/users', name: "createUser", methods: ['POST'])]
    public function createUser(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        ValidatorInterface $validator,
        TagAwareCacheInterface $cachePool
    ): JsonResponse {
        /** @var User $user */
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');

        $errors = $validator->validate($user);
        if ($errors->count() > 0) {
            throw new HttpException(JsonResponse::HTTP_BAD_REQUEST, $errors[0]->getMessage());
        }

        $cachePool->invalidateTags(['usersCache']);

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

    /**
     * Cette méthode permet de mettre à jour un utilisateur.
     *
     * @OA\Response(
     *     response=204,
     *     description="Met à jour un utilisateur",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"getUsers"}))
     *     )
     * )
     * @OA\RequestBody(
     *     @OA\JsonContent(
     *         example={
     *             "username": "username",
     *             "email": "email@mail.com",
     *             "productsId": {12, 85}
     *         },
     *         @OA\Property(property="name", description="Nom du produit", type="string"),
     *         @OA\Property(property="price", description="Prix du produit", type="float"),
     *         @OA\Property(property="usersId", description="Liste des utilisateurs possedant le produit",
     *             type="array",
     *             @OA\Items(
     *                 type="int",
     *                 format="id"
     *             )
     *         ),
     *     )
     * )
     * @OA\Tag(name="Utilisateurs")
     *
     *
     * @param User $currentUser
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param ProductRepository $productRepository
     * @param ValidatorInterface $validator
     * @param TagAwareCacheInterface $cachePool
     * @return JsonResponse
     * @throws InvalidArgumentException
     */
    #[Route('/api/users/{id}', name: 'updateUser', methods: ['PUT'])]
    public function updateProduct(
        User $currentUser,
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ProductRepository $productRepository,
        ValidatorInterface $validator,
        TagAwareCacheInterface $cachePool,
        ClientPropertyChecker $clientPropertyChecker,
    ): JsonResponse {
        $clientPropertyChecker->control($currentUser->getClient(), $this->getUser());

        /** @var User $newUser */
        $newUser = $serializer->deserialize($request->getContent(), User::class, 'json');
        if ($newUser->getEmail()) {
            $currentUser->setEmail($newUser->getEmail()());
        }
        if ($newUser->getUsername()) {
            $currentUser->setUsername($newUser->getUsername());
        }

        $errors = $validator->validate($currentUser);
        if ($errors->count() > 0) {
            throw new HttpException(JsonResponse::HTTP_BAD_REQUEST, $errors[0]->getMessage());
        }

        $cachePool->invalidateTags([
            'usersCache',
            'user'.$currentUser->getId().'Cache',
        ]);

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
