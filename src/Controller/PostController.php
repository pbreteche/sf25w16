<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use function Symfony\Component\Translation\t;

class PostController extends AbstractController
{
    #[Route('/', methods: 'GET')]
    #[Cache(maxage: 3600, public: true)]
    public function index(
        #[MapQueryParameter(name: 'cat', filter: \FILTER_VALIDATE_INT)]
        ?int $categoryId,
        Request $request,
        PostRepository $postRepository,
        CategoryRepository $categoryRepository,
        TagRepository $tagRepository,
    ): Response {
        $criteria = [];
        if ($categoryId) {
            $category = $categoryRepository->find($categoryId);
            if (!$category) {
                $this->addFlash('error', t('category.flash.error.not_found', ['id' => $categoryId]));

                return $this->redirectToRoute('app_post_index');
            }
            $criteria['category'] = $category;
        }
        $tagIds = $request->query->all('tag-choice');
        if (!empty($tagIds)) {
            $posts = $postRepository->findHavingTag($tagIds);
        } else {
            // La méthode findBy permet de définir des critères de filtre sur les propriétés de l'entité Post
            // On filtre ici avec une *instance* de catégorie (l'id ne suffit pas)
            $posts = $postRepository->findBy($criteria, orderBy: ['createdAt' => 'DESC'], limit: 10);
        }

        return $this->render('post/index.html.twig', [
            'posts' => $posts,
            'categories' => $categoryRepository->findAll(),
            'tags' => $tagRepository->findAll(),
        ]);
    }

    #[Route('/by-month/{month}', requirements: ['month' => '\d{4}-\d{2}'], methods: 'GET',)]
    public function indexByMonth(
        PostRepository $postRepository,
        ?\DateTimeImmutable $month = null,
    ): Response {
        $month ??= new \DateTimeImmutable();
        $posts = $postRepository->findByMonthDql($month);

        return $this->render('post/index_by_month.html.twig', [
            'month' => $month,
            'posts' => $posts,
        ]);
    }

    #[Route('/post-{id}', requirements: ['id'=>'\d+'], methods: 'GET')]
    public function show(Post $post): Response {
        // Résolution automatique de l'argument, car :
        // * de type Entité Doctrine
        // * le paramètre à convertir est "id"
        // Dans les autres cas, utiliser #[MapEntity(...)]
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }
}
