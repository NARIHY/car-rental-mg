<?php

namespace App\DataFixtures\Status;

use App\Entity\Status;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StatusFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $statuses = [
            'Disponible',
            'Loué',
            'En maintenance',
            'Réservé',
            'Indisponible',
            'Payé en totalité',
            'Payé en partie',
            'Non payé',
        ];

        foreach ($statuses as $statusName) {
            $status = new Status();
            $status->setStatusName($statusName);
            $manager->persist($status);
        }

        $manager->flush();
    }
}