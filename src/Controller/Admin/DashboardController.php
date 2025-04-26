<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Agency\AgencyCrudController;
use App\Controller\Admin\Calendar\CalendarController;
use App\Controller\Admin\Car\CarCrudController;
use App\Controller\Admin\Customer\CustomerCrudController;
use App\Controller\Admin\Payement\PayementCrudController;
use App\Controller\Admin\Rental\RentalCrudController;
use App\Controller\Admin\Status\StatusCrudController;
use App\Controller\Admin\Users\UserCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

// Import des entités
use App\Entity\Car;
use App\Entity\Rental;
use App\Entity\Agency;
use App\Entity\Customer;
use App\Entity\User;
use App\Entity\Status;
use App\Entity\Payement;
use App\Repository\RentalRepository;
use App\Repository\CarRepository;
use App\Repository\CustomerRepository;

class DashboardController extends AbstractDashboardController
{
    private ManagerRegistry $doctrine;
    private AdminUrlGenerator $adminUrlGenerator;
    private RentalRepository $rentalRepository;
    private CarRepository $carRepository;
    private CustomerRepository $customerRepository;

    public function __construct(
        ManagerRegistry $doctrine, 
        AdminUrlGenerator $adminUrlGenerator,
        RentalRepository $rentalRepository,
        CarRepository $carRepository,
        CustomerRepository $customerRepository
    ) {
        $this->doctrine = $doctrine;
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->rentalRepository = $rentalRepository;
        $this->carRepository = $carRepository;
        $this->customerRepository = $customerRepository;
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // Statistiques pour le tableau de bord
        $stats = [
            'activeRentals' => $this->rentalRepository->count(),
            'availableCars' => $this->carRepository->count(),
            'totalCustomers' => $this->customerRepository->count([]),
            'totalRevenue' => $this->rentalRepository->calculateTotalRevenue(),
        ];
        
        // Récentes locations
        $recentRentals = $this->rentalRepository->findRecentRentals(5);
        
        // Voitures populaires (les plus louées)
        $popularCars = $this->carRepository->findPopularCars(5);
        
        return $this->render('admin/dashboard.html.twig', [
            'stats' => $stats,
            'recentRentals' => $recentRentals,
            'popularCars' => $popularCars
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<img src="/images/car-logo.png" alt="Logo" width="32"> <span class="text-primary">Auto</span>Rental')
            ->setFaviconPath('favicon.ico')
            ->setTranslationDomain('admin')
            ->renderContentMaximized()
            ->disableUrlSignatures();
    }

    public function configureAssets(): Assets
    {
        return Assets::new()
            ->addCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css')
            ->addCssFile('css/admin-theme.css')
            ->addJsFile('js/admin-charts.js')
            ->addJsFile('js/admin-custom.js');
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)
            ->setName($user->getUserIdentifier())
            ->setGravatarEmail($user->getUserIdentifier())
            ->addMenuItems([
                MenuItem::linkToRoute('Mon profil', 'fa fa-user', 'admin_profile'),
                MenuItem::linkToRoute('Paramètres', 'fa fa-cog', 'admin_settings'),
            ]);
    }

    public function configureMenuItems(): iterable
    {
        // Dashboard
        yield MenuItem::linktoDashboard('Tableau de bord', 'fa fa-tachometer-alt');
        
        // Gestion des véhicules
        yield MenuItem::section('Gestion des véhicules');
        yield MenuItem::linkToCrud('Voitures', 'fa fa-car', Car::class)
            ->setController(CarCrudController::class);
        yield MenuItem::linkToCrud('Agences', 'fa fa-building', Agency::class)
            ->setController(AgencyCrudController::class);
        
        // Gestion des réservations
        yield MenuItem::section('Gestion des réservations');
        yield MenuItem::linkToCrud('Locations', 'fa fa-calendar-check', Rental::class)
            ->setController(RentalCrudController::class);
        yield MenuItem::linkToCrud('Statuts', 'fa fa-tags', Status::class)
            ->setController(StatusCrudController::class);
        yield MenuItem::linkToCrud('Paiements', 'fa fa-credit-card', Payement::class)
            ->setController(PayementCrudController::class);
        
        // Gestion des clients
        yield MenuItem::section('Gestion des clients');
        yield MenuItem::linkToCrud('Clients', 'fa fa-users', Customer::class)
            ->setController(CustomerCrudController::class);
        yield MenuItem::linkToCrud('Utilisateurs', 'fa fa-user-shield', User::class)
            ->setController(UserCrudController::class);
        
        // Rapports et outils
        yield MenuItem::section('Rapports et outils');
        yield MenuItem::linkToRoute('Rapport de revenus', 'fa fa-chart-line', 'admin_revenue_report');
        yield MenuItem::linkToRoute('Rapport d\'utilisation', 'fa fa-chart-bar', 'admin_usage_report');
        yield MenuItem::linkToUrl('Calendrier des réservations', 'fa fa-calendar', $this->adminUrlGenerator->setController(CalendarController::class)->generateUrl());
        
        // Configuration
        if ($this->isGranted('ROLE_ADMIN')) {
            yield MenuItem::section('Configuration');
            yield MenuItem::linkToRoute('Paramètres', 'fa fa-cog', 'admin_settings');
            yield MenuItem::subMenu('Système', 'fa fa-server')->setSubItems([
                MenuItem::linkToRoute('Journal d\'activité', 'fa fa-list', 'admin_logs'),
                MenuItem::linkToRoute('Sauvegarde', 'fa fa-database', 'admin_backups'),
            ]);
        }
    }
}