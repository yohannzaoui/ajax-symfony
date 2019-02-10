<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\PostLike;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class AppFixtures
 * @package App\DataFixtures
 */
class AppFixtures extends Fixture
{
    /**
     * Encodeur de mot de passe
     *
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * AppFixtures constructor.
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        $users =[];

        $user = new User();
        $user->setEmail('user@symfony.com')
            ->setPassword($this->encoder->encodePassword($user, 'password'))
            ->setUsername('admin');

        $manager->persist($user);

        $users[] = $user;

        for ($i=0; $i < 20; $i++) {
            $user = new User();
            $user->setEmail($faker->email);
            $user->setUsername($faker->name);
            $user->setPassword($this->encoder->encodePassword($user, 'password'));

            $manager->persist($user);

            $users[] = $user;


        }


        for ($i = 0; $i < 20; $i++) {
            $post = new Post();
            $post->setTitle($faker->sentence(6))
                ->setIntroduction($faker->paragraph())
                ->setContent('<p>' . join(',', $faker->paragraphs()) . '</p>');

            $manager->persist($post);

            for ($j = 0; $j < mt_rand(0, 10); $j++) {
                $like = new PostLike();
                $like->setPost($post);
                $like->setUser($faker->randomElement($users));

                $manager->persist($like);
            }
        }

        $manager->flush();
    }
}
