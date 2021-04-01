<?php

namespace App\Controller;

use App\Entity\Restaurant;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * @Route("/", name="app_default_")
 */
class DefaultController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var PaginatorInterface
     */
    private PaginatorInterface $paginator;

    /**
     * @var Stopwatch
     */
    private Stopwatch $stopwatch;

    public function __construct(EntityManagerInterface $entityManager, PaginatorInterface $paginator, Stopwatch $stopwatch)
    {
        $this->entityManager = $entityManager;
        $this->paginator = $paginator;
        $this->stopwatch = $stopwatch;
    }

    /**
     * @Route("/", name="index")
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $term = $request->get('term');
        $page = (int) $request->get('page', 1);

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('r')
            ->from(Restaurant::class, 'r')
            ->addSelect('
                MAX(
                    CASE WHEN                     
                        :currentDate >= oh.open AND :currentDate <= oh.close
                    THEN 1 ELSE 0
                    END
                ) as HIDDEN opened
            ')
            ->leftJoin('r.openingHours', 'oh')
            ->leftJoin('r.cuisine', 'c')
            ->setParameters([
                'currentDate' => DateTimeImmutable::createFromFormat('!D H:i', date('D H:i'))
            ])
            ->groupBy('r')
            ->addOrderBy('opened', 'DESC')
            ->addOrderBy('r.rating', 'DESC')
            ->addOrderBy('r.id', 'DESC');

        if ($term !== null) {
            $queryBuilder->andWhere('
                MATCH(r.title, r.location) AGAINST(:term) > 0
                OR r.title LIKE :termEscaped
                OR c.title LIKE :termEscaped
            ')
            ->setParameter('term', $term)
            ->setParameter('termEscaped', '%'.addcslashes($term, "%_").'%');
        }

        $this->stopwatch->start('query');
        $pagination = $this->paginator->paginate(
            $queryBuilder->getQuery(),
            $page,
            10
        );

        // Preload restaurants relations in one query
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('r, c, oh')
            ->from(Restaurant::class, 'r')
            ->leftJoin('r.openingHours', 'oh')
            ->leftJoin('r.cuisine', 'c')
            ->where('r IN(:restaurants)')
            ->setParameters([
                'restaurants' => $pagination
            ]);
        $queryBuilder->getQuery()->execute();
        $queryTime = $this->stopwatch->stop('query');

        return $this->render('default/index.html.twig', [
            'restaurants' => $pagination,
            'query_time' => $queryTime->getDuration(),
            'term' => $term
        ]);
    }

    /**
     * @Route("/view/{restaurant}", requirements={"restaurant": "[0-9]+"}, name="view")
     *
     * @param Request $request
     * @return Response
     */
    public function view(Request $request, Restaurant $restaurant)
    {
        return $this->render('default/view.html.twig', [
            'restaurant' => $restaurant
        ]);
    }

}
