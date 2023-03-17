<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Service\ClientPropertyChecker;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductController extends AbstractController
{
    /**
     * Cette méthode permet de récupérer l'ensemble de vos produits.
     *
     * @OA\Response(
     *     response=200,
     *     description="Retourne la liste des produits",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Product::class, groups={"getProducts"}))
     *     )
     * )
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="La page que l'on veut récupérer",
     *     @OA\Schema(type="int")
     * )
     *
     * @OA\Parameter(
     *     name="limit",
     *     in="query",
     *     description="Le nombre d'éléments que l'on veut récupérer",
     *     @OA\Schema(type="int")
     * )
     * @OA\Tag(name="Produits")
     *
     *
     * @param ProductRepository $productRepository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/api/products', name: 'products', methods: ['GET'])]
    public function getProductList(ProductRepository $productRepository, SerializerInterface $serializer): JsonResponse
    {
        $productList = $productRepository->findBy(['client' => $this->getUser()]);
        $context = SerializationContext::create()->setGroups(['getProducts']);
        $jsonProductList = $serializer->serialize($productList, 'json', $context);

        return new JsonResponse($jsonProductList, Response::HTTP_OK, [], true);
    }

    /**
     * Cette méthode permet de récupérer les informations d'un de vos produits.
     *
     * @OA\Response(
     *     response=200,
     *     description="Retourne les informations du produit",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Product::class, groups={"getProducts"}))
     *     )
     * )
     * @OA\Tag(name="Produits")
     *
     *
     * @param Product $product
     * @param SerializerInterface $serializer
     * @param ClientPropertyChecker $clientPropertyChecker
     * @return JsonResponse
     */
    #[Route('/api/products/{id}', name: 'detailProduct', methods: ['GET'])]
    public function getDetailProduct(
        Product $product,
        SerializerInterface $serializer,
        ClientPropertyChecker $clientPropertyChecker
    ): JsonResponse {
        $clientPropertyChecker->control($product->getClient(), $this->getUser());

        $context = SerializationContext::create()->setGroups(['getProducts']);
        $jsonProduct = $serializer->serialize($product, 'json', $context);

        return new JsonResponse($jsonProduct, Response::HTTP_OK, [], true);
    }

    /**
     * Cette méthode permet de supprimer un de vos produits.
     *
     * @OA\Response(
     *     response=204,
     *     description="Supprime un produit",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Product::class, groups={"getProducts"}))
     *     )
     * )
     * @OA\Tag(name="Produits")
     *
     *
     * @param Product $product
     * @param EntityManagerInterface $entityManager
     * @param ClientPropertyChecker $clientPropertyChecker
     * @return JsonResponse
     */
    #[Route('/api/products/{id}', name: 'deleteProduct', methods: ['DELETE'])]
    public function deleteProduct(
        Product $product,
        EntityManagerInterface $entityManager,
        ClientPropertyChecker $clientPropertyChecker
    ): JsonResponse {
        $clientPropertyChecker->control($product->getClient(), $this->getUser());

        $entityManager->remove($product);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Cette méthode permet de créer un produit.
     *
     * @OA\Response(
     *     response=201,
     *     description="Créer un produit",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Product::class, groups={"getProducts"}))
     *     )
     * )
     * @OA\Parameter(
     *     name="name",
     *     in="query",
     *     description="Nom du produit",
     *     @OA\Schema(type="string")
     * )
     * @OA\Parameter(
     *     name="price",
     *     in="query",
     *     description="Prix du produit",
     *     @OA\Schema(type="float")
     * )
     * @OA\Tag(name="Produits")
     *
     *
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param UrlGeneratorInterface $urlGenerator
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    #[Route('/api/products', name: "createProduct", methods: ['POST'])]
    public function createProduct(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        ValidatorInterface $validator
    ): JsonResponse {
        /** @var Product $product */
        $product = $serializer->deserialize($request->getContent(), Product::class, 'json');

        $errors = $validator->validate($product);
        if ($errors->count() > 0) {
            throw new HttpException(JsonResponse::HTTP_BAD_REQUEST, $errors[0]->getmessage());
        }

        $product->setClient($this->getUser());
        $entityManager->persist($product);
        $entityManager->flush();

        $context = SerializationContext::create()->setGroups(['getProducts']);
        $jsonProduct = $serializer->serialize($product, 'json', $context);
        $location = $urlGenerator->generate(
            'detailProduct',
            ['id' => $product->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new JsonResponse($jsonProduct, Response::HTTP_CREATED, ['Location' => $location], true);
    }

    /**
     * Cette méthode permet de mettre à jour un produit.
     *
     * @OA\Response(
     *     response=204,
     *     description="Met à jour un produit",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Product::class, groups={"getProducts"}))
     *     )
     * )
     * @OA\Parameter(
     *     name="name",
     *     in="query",
     *     description="Nom du produit",
     *     @OA\Schema(type="string")
     * )
     * @OA\Parameter(
     *     name="price",
     *     in="query",
     *     description="Prix du produit",
     *     @OA\Schema(type="float")
     * )
     * @OA\Parameter(
     *     name="usersId",
     *     in="query",
     *     description="Liste des id utilisateurs",
     *     @OA\Schema(
     *         schema="ExampleResponse",
     *         type="array",
     *         @OA\Items(
     *             type="int",
     *             @OA\Property(property="id", type="int")
     *         )
     *     )
     * )
     * @OA\Tag(name="Produits")
     *
     *
     * @param Product $currentProduct
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param UserRepository $userRepository
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    #[Route('/api/products/{id}', name: 'updateProduct', methods: ['PUT'])]
    public function updateProduct(
        Product $currentProduct,
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        UserRepository $userRepository,
        ValidatorInterface $validator
    ): JsonResponse {
        if ($currentProduct->getClient() !== $this->getUser()) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        /** @var Product $newProduct */
        $newProduct = $serializer->deserialize($request->getContent(), Product::class, 'json');
            !$currentProduct->getName() ?? $currentProduct->setName($newProduct->getName());
            !$currentProduct->getPrice() ?? $currentProduct->setPrice($newProduct->getPrice());

        $errors = $validator->validate($currentProduct);
        if ($errors->count() > 0) {
            throw new HttpException(JsonResponse::HTTP_BAD_REQUEST, $errors[0]->getMessage());
        }

        $content = $request->toArray();
        $usersId = $content['usersId'] ?? -1;
        $newUsers = $userRepository->findBy(['id' => $usersId]) ?? null;

        if ($usersId) {
            for ($i = 0; $i < count($newUsers); $i++) {
                $currentProduct->addUser($newUsers[$i]);
            }
        }

        $entityManager->persist($currentProduct);
        $entityManager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
