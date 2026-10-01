<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/posts')]
class CommentController
{
    #[Route('/{postId}/comments', name: 'api_comment_collection_get', methods: ['GET'])]
    public function collection(
        Post $post,
        SerializerInterface $serializer,
        CommentRepository $commentRepository
    ): JsonResponse {
        $comments = $commentRepository->findBy(['post' => $post]);
        return new JsonResponse(
            $serializer->serialize($comments, 'json', ['groups' => 'get']),
            JsonResponse::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/comments/{id}', name: 'api_comments_item_get', methods: ['GET'])]
    public function item(Comment $comment, SerializerInterface $serializer): JsonResponse
    {
        return new JsonResponse(
            $serializer->serialize($comment, 'json', ['groups' => 'get']),
            JsonResponse::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/{postId}/comments', name: 'api_comments_collection_post', methods: ['POST'])]
    public function post(
        #[MapEntity(mapping: ['postId' => 'id'])] Post $post,
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        UserRepository $userRepository,
        UrlGeneratorInterface $urlGenerator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true) ?? [];
        if (empty($data['message']) || empty($data['authorId'])) {
            return new JsonResponse(
                ['error' => 'Les champs "message" et "authorId" sont obligatoires'],
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
        $comment = Comment::create($data['message'], $authorId, $post);
        $em->persist($comment);
        $em->flush();

        return new JsonResponse(
            $serializer->serialize($comment, 'json', ['groups' => 'get']),
            JsonResponse::HTTP_CREATED,
            ['Location' => $urlGenerator->generate('api_comments_item_get', ['id' => $comment->getId()])],
            true
        );
    }

    #[Route('/comments/{id}', name: 'api_comments_item_put', methods: ['PUT'])]
    public function put(
        Comment $comment,
        Request $request,
        EntityManagerInterface $em,
        #[CurrentUser] User $user
    ): JsonResponse {
        if ($comment->getAuthor() !== $user) {
            return new JsonResponse(
                ['error' => 'Vous n\'êtes pas l\'auteur de ce commentaire'],
                JsonResponse::HTTP_FORBIDDEN
            );
        }
        $data = json_decode($request->getContent(), true) ?? [];
        if (array_key_exists('message', $data)) {
            $comment->setMessage($data['message']);
        }
        $em->flush();
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('/comments/{id}', name: 'api_comments_item_delete', methods: ['DELETE'])]
    public function delete(
        Comment $comment,
        EntityManagerInterface $em,
        #[CurrentUser] User $user
    ): JsonResponse {
        if ($comment->getAuthor() !== $user) {
            return new JsonResponse(
                ['error' => 'Vous n\'êtes pas l\'auteur de ce commentaire'],
                JsonResponse::HTTP_FORBIDDEN
            );
        }
        $em->remove($comment);
        $em->flush();
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}