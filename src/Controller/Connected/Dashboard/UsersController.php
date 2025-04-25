<?php

namespace App\Controller\Connected\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UsersController extends AbstractController
{
    #[Route('/connected/dashboard/users', name: 'app_connected_dashboard_users')]
    public function index(): Response
    {
        return $this->render('connected/dashboard/users/index.html.twig', [
            'controller_name' => 'UsersController',
        ]);
    }
}
