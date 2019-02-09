<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\PostLike;
use App\Repository\PostLikeRepository;
use App\Repository\PostRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PostController
 * @package App\Controller
 */
class PostController extends AbstractController
{
    /**
     * @Route(path="/", name="homepage", methods={"GET"})
     * @param PostRepository $repo
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(PostRepository $repo)
    {
        $posts = $repo->findAll();

        return $this->render('post/index.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @Route(path="/post/{id}/like", name="post_like", methods={"GET"})
     * @param Post $post
     * @param ObjectManager $manager
     * @param PostLikeRepository $repository
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function like(
        Post $post,
        ObjectManager $manager,
        PostLikeRepository $repository
    ){
        $user = $this->getUser();

        if (!$user) return $this->json([
            'code' => 403,
            'message' => 'Unauthorized'
            ], 403);

        if ($post->isLikeByUser($user)) {
            $like = $repository->findOneBy([
                'post' => $post,
                'user' => $user
                ]);

            $manager->remove($like);
            $manager->flush();

            return $this->json([
                'code' => 200,
                'message' => 'Like supprimer',
                'likes' => $repository->count(['post' => $post])
            ], 200);
        }

        $like = new PostLike();
        $like->setPost($post);
        $like->setUser($user);

        $manager->persist($like);
        $manager->flush();

        return $this->json([
            'code' => 200,
            'message' => 'Like bien ajouté',
            'likes' => $repository->count(['post' => $post])
            ], 200);
    }
}
