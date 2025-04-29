<?php

namespace App\Controller\Admin\Agency;

use App\Entity\Agency;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ManagerRegistry;

class AgencyCrudController extends AbstractCrudController
{
    private UserPasswordHasherInterface $passwordHasher;
    private ManagerRegistry $doctrine;

    public function __construct(UserPasswordHasherInterface $passwordHasher, ManagerRegistry $doctrine)
    {
        $this->passwordHasher = $passwordHasher;
        $this->doctrine = $doctrine;
    }
    
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
            AssociationField::new('users', 'Comptes')
                ->onlyOnDetail()
        ];
    }
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Agency) return;

        parent::persistEntity($entityManager, $entityInstance);

        // créer un utilisateur admin
        $agency = $entityInstance;
        $user = new User();
        $user->setEmail(strtolower($agency->getName()) . '@example.com');
        $plaintext = bin2hex(random_bytes(4));
        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            $plaintext
        ));
        $user->setRoles(['ROLE_AGENCY_ADMIN']);
        $user->setAgency($agency);

        $entityManager->persist($user);
        $entityManager->flush();

            $this->addFlash('success', 'Agence créée et compte admin généré. Mot de passe: ' . $plaintext);
            $this->addFlash('success', "Agence créée et compte admin généré. Mot de passe: $plaintext");
        }
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): \Doctrine\ORM\QueryBuilder
    {
            $user = $this->doctrine->getRepository(User::class)->find($this->getUser()->getUserIdentifier());
            // si non-super-admin, ne voir que sa propre agency
            $repository = $this->doctrine->getRepository($entityDto->getFqcn());
            if (!$repository instanceof \Doctrine\ORM\EntityRepository) {
                throw new \LogicException('The repository must be an instance of EntityRepository.');
            }
            $qb = $repository->createQueryBuilder('entity');
            return $qb;
        if (!$this->isGranted('ROLE_ADMIN_AGENCY') && $user && method_exists($user, 'getAgency') && $user->getAgency()) {
            $qb->andWhere('entity.agency = :aid')
               ->setParameter('aid', $user->getAgency()->getId());
        }
        if (!$this->isGranted('ROLE_ADMIN_AGENCY') && $user && method_exists($user, 'getAgency') && $user->getAgency()) {
            $qb->andWhere('entity.agency = :aid')
               ->setParameter('aid', $user->getAgency()->getId());
        }
        return $qb;
    }
}
