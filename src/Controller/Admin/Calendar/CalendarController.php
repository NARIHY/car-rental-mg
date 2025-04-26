<?php

namespace App\Controller\Admin\Calendar;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CalendarController extends AbstractController
{
    #[Route('/admin/calendar/calendar', name: 'app_admin_calendar_calendar')]
    public function index(): Response
    {
        return $this->render('admin/calendar/calendar/index.html.twig', [
            'controller_name' => 'CalendarController',
        ]);
    }
}
