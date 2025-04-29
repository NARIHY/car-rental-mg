<?php

namespace App\Controller\Admin\Agency;

use App\Entity\Agency;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AgencyCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Agency::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(), // Montre l'id uniquement dans le listing
            TextField::new('name', 'Nom de l\'agence'),
            TextField::new('location', 'Localisation'),
            TextField::new('contact', 'Contact'),
            BooleanField::new('isActive', 'Actif')
                ->setHelp('Cochez cette case pour activer l\'agence'),
            AssociationField::new('cars', 'Véhicules')
                ->onlyOnIndex(), // Montre un compteur/association sur l'index seulement

            DateTimeField::new('createdAt', 'Créé le')
                ->hideOnForm(), // Seulement en lecture
            DateTimeField::new('updatedAt', 'Mis à jour le')
                ->hideOnForm(), // Seulement en lecture
        ];
    }
    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
