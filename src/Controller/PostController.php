<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/posts')]
class PostController
{
    #[Route(name: 'api_posts_collection_get', methods: ['GET'])]
    public function collection(
        PostRepository $postRepository,
        SerializerInterface $serializer
    ): JsonResponse {
        return new JsonResponse(
            $serializer->serialize($postRepository->findAll(), 'json', ['groups' => 'get']),
            JsonResponse::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/{id}', name: 'api_posts_item_get', methods: ['GET'])]
    public function item(Post $post, SerializerInterface $serializer): JsonResponse
    {
        return new JsonResponse(
            $serializer->serialize($post, 'json', ['groups' => 'get']),
            JsonResponse::HTTP_OK,
            [],
            true
        );
    }

    #[Route(name: 'api_posts_collection_post', methods: ['POST'])]
    public function post(
        Request $request,
        UserRepository $userRepository,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        UrlGeneratorInterface $urlGenerator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true) ?? [];
        if (empty($data['content']) || empty($data['authorId'])) {
            return new JsonResponse(
                ['error' => 'Les champs "content" et "authorId" sont obligatoires'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }
        $authorId = $userRepository->find($data['authorId']);
        if (!$authorId) {
            return new JsonResponse(
                ['error' => sprintf('Utilisateur %d introuvable', $data['authorId'])],
                JsonResponse::HTTP_NOT_FOUND
            );
        }
        $post = Post::create($data['content'], $authorId);
        $em->persist($post);
        $em->flush();
        return new JsonResponse(
            $serializer->serialize($post, 'json', ['groups' => 'get']),
            JsonResponse::HTTP_CREATED,
            ['Location' => $urlGenerator->generate('api_posts_item_get', ['id' => $post->getId()])],
            true
        );
    }

    #[Route('/{id}', name: 'api_posts_item_put', methods: ['PUT'])]
    public function put(Post $post, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];
        if (array_key_exists('content', $data)) {
            $post->setContent($data['content']);
        }
        $em->flush();
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('/{id}', name: 'api_posts_item_delete', methods: ['DELETE'])]
    public function delete(Post $post, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($post);
        $em->flush();
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}