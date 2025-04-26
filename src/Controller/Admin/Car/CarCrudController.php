<?php

namespace App\Controller\Admin\Car;

use App\Entity\Car;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;

use Doctrine\ORM\EntityManagerInterface;

class CarCrudController extends AbstractCrudController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    public static function getEntityFqcn(): string
    {
        return Car::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Voiture')
            ->setEntityLabelInPlural('Voitures')
            ->setPageTitle('index', 'Gestion des véhicules')
            ->setPageTitle('new', 'Ajouter une voiture')
            ->setPageTitle('edit', fn (Car $car) => sprintf('Modifier %s %s', $car->getBrand(), $car->getModel()))
            ->setPageTitle('detail', fn (Car $car) => sprintf('%s %s - %s', $car->getBrand(), $car->getModel(), $car->getLicensePlate()))
            ->setDefaultSort(['brand' => 'ASC', 'model' => 'ASC'])
            ->showEntityActionsInlined();
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        
        yield ImageField::new('image')
            ->setBasePath('uploads/cars')
            ->setUploadDir('public/uploads/cars')
            ->setUploadedFileNamePattern('[randomhash].[extension]')
            ->setFormTypeOptions([
                'attr' => [
                    'accept' => 'image/*'
                ]
            ])
            ->hideOnIndex();
            
        yield ImageField::new('image')
            ->onlyOnIndex()
            ->setBasePath('uploads/cars');
            
        yield TextField::new('brand', 'Marque')
            ->setColumns(6);
            
        yield TextField::new('model', 'Modèle')
            ->setColumns(6);
            
        yield TextField::new('licensePlate', 'Plaque d\'immatriculation')
            ->setColumns(6);
            
        yield ChoiceField::new('fuelType', 'Type de carburant')
            ->setChoices([
                'Essence' => 'essence',
                'Diesel' => 'diesel',
                'Électrique' => 'electrique',
                'Hybride' => 'hybride',
            ])
            ->setColumns(6);
            
        yield ChoiceField::new('transmission', 'Transmission')
            ->setChoices([
                'Manuelle' => 'manuelle',
                'Automatique' => 'automatique',
            ])
            ->setColumns(6);
            
        yield NumberField::new('dayilyRate', 'Tarif journalier')
            ->setNumDecimals(2)
            ->setColumns(6);
            
        yield BooleanField::new('isAvailable', 'Disponible')
            ->renderAsSwitch(true)
            ->setColumns(6);
            
        yield AssociationField::new('agency', 'Agence')
            ->setColumns(6);
            
        yield AssociationField::new('rentals', 'Locations')
            ->onlyOnDetail();
            
        yield DateTimeField::new('createdAt', 'Date de création')
            ->hideOnForm()
            ->onlyOnDetail();
            
        yield DateTimeField::new('updatedAt', 'Dernière mise à jour')
            ->hideOnForm()
            ->onlyOnDetail();
    }

    public function configureActions(Actions $actions): Actions
    {
        $viewRentals = Action::new('viewRentals', 'Voir les locations')
            ->setIcon('fa fa-calendar-check')
            ->linkToCrudAction('viewCarRentals');
            
        $toggleAvailability = Action::new('toggleAvailability', 'Changer disponibilité')
            ->setIcon('fa fa-exchange-alt')
            ->linkToCrudAction('toggleCarAvailability')
            ->displayIf(static function (Car $car) {
                return true; // Toujours affiché
            });

        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_DETAIL, $viewRentals)
            ->add(Crud::PAGE_INDEX, $toggleAvailability)
            ->reorder(Crud::PAGE_INDEX, [Action::DETAIL, 'toggleAvailability', Action::EDIT, Action::DELETE]);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('brand', 'Marque'))
            ->add(TextFilter::new('model', 'Modèle'))
            ->add(TextFilter::new('licensePlate', 'Plaque d\'immatriculation'))
            ->add(ChoiceFilter::new('fuelType', 'Type de carburant')
                ->setChoices([
                    'Essence' => 'essence',
                    'Diesel' => 'diesel',
                    'Électrique' => 'electrique',
                    'Hybride' => 'hybride',
                ]))
            ->add(NumericFilter::new('dayilyRate', 'Tarif journalier'))
            ->add(BooleanFilter::new('isAvailable', 'Disponible'))
            ->add(EntityFilter::new('agency', 'Agence'));
    }
    
    public function viewCarRentals(AdminContext $context)
    {
        $car = $context->getEntity()->getInstance();
        $rentals = $car->getRentals();
        
        return $this->render('admin/car/rentals.html.twig', [
            'car' => $car,
            'rentals' => $rentals,
        ]);
        $this->entityManager->flush();
    }
    
    public function toggleCarAvailability(AdminContext $context)
    {
        $car = $context->getEntity()->getInstance();
        $car->setIsAvailable(!$car->isAvailable());
        
        $this->entityManager->flush();
        
        $this->addFlash('success', sprintf(
            'La disponibilité de la voiture %s %s a été mise à jour.',
            $car->getBrand(),
            $car->getModel()
        ));
        
        return $this->redirect($context->getReferrer());
    }
}
