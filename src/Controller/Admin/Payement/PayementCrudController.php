<?php

namespace App\Controller\Admin\Payement;

use App\Entity\Payement;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PayementCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Payement::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('rental', 'Rental'),
            MoneyField::new('amount', 'Amount')->setCurrency('USD'),
            TextField::new('method', 'Payment Method'),
            AssociationField::new('status', 'Status'),
            DateTimeField::new('paidAt', 'Paid At')->hideOnForm(),
            DateTimeField::new('createdAt', 'Created At')->hideOnForm(),
            DateTimeField::new('updatedAt', 'Updated At')->hideOnForm(),
        ];
    }
}
