<?php

namespace App\Controller\Admin\Rental;

use App\Entity\Rental;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class RentalCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Rental::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('car')->setLabel('Car'),
            AssociationField::new('customer')->setLabel('Customer')->setFormTypeOption('by_reference', false),
            DateField::new('startDate')->setLabel('Start Date'),
            DateField::new('endDate')->setLabel('End Date'),
            MoneyField::new('totalAmount')->setCurrency('USD')->setLabel('Total Amount'),
            DateField::new('createdAt')->setLabel('Created At')->hideOnForm(),
            DateField::new('updatedAt')->setLabel('Updated At')->hideOnForm(),
            AssociationField::new('status')->setLabel('Status'),
        ];
    }
}