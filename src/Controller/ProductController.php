<?php

namespace App\Controller;

use App\Entity\Product;
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

class ProductController extends AbstractController
{
    #[Route('/api/products', name: 'products', methods: ['GET'])]
    public function getProductList(ProductRepository $productRepository, SerializerInterface $serializer): JsonResponse
    {
        $productList = $productRepository->findAll();
        $jsonProductList = $serializer->serialize($productList, 'json', ['groups' => 'getProducts']);

        return new JsonResponse($jsonProductList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/products/{id}', name: 'detailProduct', methods: ['GET'])]
    public function getDetailProduct(
        Product $product,
        SerializerInterface $serializer,
    ): JsonResponse {
        $jsonProduct = $serializer->serialize($product, 'json', ['groups' => 'getProducts']);

        return new JsonResponse($jsonProduct, Response::HTTP_OK, [], true);
    }

    #[Route('/api/products/{id}', name: 'deleteProduct', methods: ['DELETE'])]
    public function deleteProduct(Product $product, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($product);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/products', name: "createProduct", methods: ['POST'])]
    public function createProduct(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator
    ): JsonResponse {
        $product = $serializer->deserialize($request->getContent(), Product::class, 'json');
        $entityManager->persist($product);
        $entityManager->flush();

        $jsonProduct = $serializer->serialize($product, 'json', ['groups' => 'getProducts']);
        $location = $urlGenerator->generate(
            'detailProduct',
            ['id' => $product->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new JsonResponse($jsonProduct, Response::HTTP_CREATED, ['Location' => $location], true);
    }

    #[Route('/api/products/{id}', name: 'updateProduct', methods: ['PUT'])]
    public function updateProduct(
        Product $product,
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        UserRepository $userRepository
    ): JsonResponse {
        /** @var Product $updatedProduct */
        $updatedProduct = $serializer->deserialize(
            $request->getContent(),
            Product::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $product]
        );

        $content = $request->toArray();
        $usersId = $content['usersId'] ?? -1;
        $newUsers = $userRepository->findBy(['id' => $usersId]) ?? null;

        if ($usersId) {
            for ($i = 0; $i < count($newUsers); $i++) {
                $updatedProduct->addUser($newUsers[$i]);
            }
        }

        $entityManager->persist($product);
        $entityManager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
