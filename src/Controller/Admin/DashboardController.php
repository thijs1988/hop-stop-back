<?php

namespace App\Controller\Admin;

use App\Entity\Brands;
use App\Entity\CartItems;
use App\Entity\CartProducts;
use App\Entity\Categories;
use App\Entity\Coupons;
use App\Entity\Media;
use App\Entity\Products;
use App\Entity\Transactions;
use App\Entity\User;
use App\Repository\CartProductsRepository;
use App\Repository\ProductsRepository;
use App\Repository\TransactionsRepository;
use DateTimeImmutable;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class DashboardController extends AbstractDashboardController
{
    private $transactionsRepository;
    private $cartProductsRepository;
    private $productsRepository;
    private $chartBuilder;

    public function __construct(TransactionsRepository $transactionsRepository, CartProductsRepository $cartProductsRepository, ProductsRepository $productsRepository, ChartBuilderInterface $chartBuilder)
    {
        $this->transactionsRepository = $transactionsRepository;
        $this->cartProductsRepository = $cartProductsRepository;
        $this->productsRepository = $productsRepository;
        $this->chartBuilder = $chartBuilder;
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        $allTransactions = $this->transactionsRepository->findTransactionsLastYear();

        $allTransactionsData = ['januari' => 0, 'februari' => 0, 'maart' => 0, 'april' => 0, 'mei' => 0, 'juni' => 0, 'juli' => 0, 'augustus' => 0, 'september' => 0, 'oktober' => 0, 'november' => 0, 'december' => 0 ];
        $months = ['januari', 'februari', 'maart', 'april', 'mei', 'juni', 'juli', 'augustus', 'september', 'oktober', 'november', 'december' ];

        foreach($allTransactions as $transaction){
            $date = substr($transaction->getCreatedAt()->format('d-m-Y'), 3, 2);
            $allTransactionsData[$months[$date-1]] += $transaction->getAmount();
        }

        $transactions = $this->transactionsRepository->findBy([
            'status' => 'paid',
            'shipped' => 0
            ]);

        $labels = [];
        $data = [];
        foreach($transactions as $item){
            $data[] = $item->getAmount();
            $labels[] = $item->getName();
        }

        $userCount = 0;
        $sessionCount = 0;
        foreach($transactions as $order){
           if($order->getCartId() !== 0){
               $userCount += 1;
           }else{
               $sessionCount += 1;
           }
        }

        return $this->render('homepage/content.html.twig', [
            'total' => count($allTransactions),
            'allTransactionsData' => $allTransactionsData,
            'months' => $months,
            'data' => $data,
            'labels' => $labels,
            'transactions' => $transactions,
            'userCount' => $userCount,
            'sessionCount' => $sessionCount
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Hop-Stop');
    }

    public function configureMenuItems(): iterable
    {
         yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');

         yield MenuItem::section('Unprocessed Orders');
             yield MenuItem::linkToCrud('Transactions To Be Shipped', 'fas fa-list', Transactions::class)
                    ->setPermission('ROLE_ADMIN')
                    ->setController(TransactionsToBeShippedCrudController::class);
             yield MenuItem::linkToCrud('Cart Orders', 'fas fa-list', CartProducts::class)
                    ->setController(CartOrdersCrudController::class);
             yield MenuItem::linkToCrud('Session Orders', 'fas fa-list', Transactions::class)
                    ->setController(SessionOrdersCrudController::class);

         yield MenuItem::section('User Settings');
            yield MenuItem::linkToCrud('User', 'fas fa-user', User::class);

         yield MenuItem::section('Product Settings');
            yield MenuItem::linkToCrud('Media', 'fas fa-image', Media::class);
            yield MenuItem::linkToCrud('Brands', 'fas fa-list', Brands::class);
            yield MenuItem::linkToCrud('Categories', 'fas fa-list', Categories::class);
            yield MenuItem::linkToCrud('Products', 'fas fa-list', Products::class)
                ->setController(ProductsCrudController::class);

         yield MenuItem::section('Cart Items');
            yield MenuItem::linkToCrud('CartItems', 'fas fa-shopping-cart', CartItems::class);
            yield MenuItem::linkToCrud('CartProducts', 'fas fa-cart-plus', CartProducts::class)
                    ->setController(CartProductsCrudController::class);

         yield MenuItem::section('Coupons');
            yield MenuItem::linkToCrud('Coupons', 'fas fa-list', Coupons::class);

         yield MenuItem::section('Transactions');
             yield MenuItem::linkToCrud('Transactions', 'fas fa-receipt', Transactions::class)
                    ->setController(TransactionsCrudController::class);
    }
}
