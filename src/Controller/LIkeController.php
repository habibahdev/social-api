<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/posts')]
class LIkeController
{
    #[Route('/{postId}/likes', name: 'api_likes_collection_get', methods: ['GET'])]
    public function collection(
        #[MapEntity(mapping: ['postId' => 'id'])] Post $post,
        SerializerInterface $serializer
    ): JsonResponse {
        return new JsonResponse(
            $serializer->serialize($post->getLikedBy(), 'json', ['groups' => 'get']),
            JsonResponse::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/{postId}/likes', name: 'api_likes_collection_post', methods: ['POST'])]
    public function like(
        #[MapEntity(mapping: ['postId' => 'id'])] Post $post,
        EntityManagerInterface $em,
        #[CurrentUser] User $user
    ): JsonResponse {
        $post->likeBy($user);
        $em->flush();
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('/{postId}/likes', name: 'api_likes_item_delete', methods: ['DELETE'])]
    public function unlike(
        #[MapEntity(mapping: ['postId' => 'id'])] Post $post,
        EntityManagerInterface $em,
        #[CurrentUser] User $user
    ): JsonResponse {
        $post->dislikeBy($user);
        $em->flush();
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}