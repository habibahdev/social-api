<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $users = [];
        for ($i = 1; $i <= 10; $i++) {
            $user = User::create(
                sprintf("email%d@api.com", $i),
                sprintf("name%d", $i)
            );
            $user->setPassword($this->hasher->hashPassword($user, 'password'));
            $manager->persist($user);
            $users[] = $user;
        }

        foreach ($users as $user) {
            for ($j = 1; $j <= 5; $j++) {
                $post = Post::create(sprintf("content %d", $j), $user);
                shuffle($users);
                foreach (array_slice($users, 0, 5) as $userCanLike) {
                    $post->likeBy($userCanLike);
                }
                $manager->persist($post);

                for ($k = 1; $k <= 10; $k++) {
                    $comment = Comment::create(sprintf("Message %d", $k), $users[array_rand($users)], $post);
                    $manager->persist($comment);
                }
            }
        }
        $manager->flush();
    }
}
