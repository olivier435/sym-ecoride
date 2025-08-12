<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\AvatarForm;
use App\Service\PseudoGeneratorService;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use libphonenumber\PhoneNumberFormat;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Misd\PhoneNumberBundle\Templating\Helper\PhoneNumberHelper;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCrudController extends AbstractCrudController
{
    public function __construct(protected PhoneNumberHelper $phoneNumberHelper, protected SendMailService $sendMailService, protected PseudoGeneratorService $pseudoGeneratorService, protected UserPasswordHasherInterface $passwordHasher) {}

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission('ROLE_ADMIN')
            ->setPageTitle('index', 'Utilisateurs :')
            ->setPageTitle('new', 'Créer un utilisateur')
            ->setPageTitle('edit', fn(User $user) => (string) $user->getFullname())
            ->setPageTitle('detail', fn(User $user) => (string) $user->getFullname())
            ->setEntityLabelInSingular('un utilisateur')
            ->setDefaultSort(['id' => 'ASC'])
            ->setPaginatorPageSize(10);
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions = parent::configureActions($actions);
        // $actions->disable(Action::NEW);
        return $actions;
    }

    public function configureAssets(Assets $assets): Assets
    {
        return $assets->addAssetMapperEntry('admin_pseudo')
            ->addAssetMapperEntry('admin_password_generator')
            ->addCssFile('vendor/bootstrap-icons/font/bootstrap-icons.min.css');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            FormField::addFieldset('Détails de l\'utilisateur'),
            TextField::new('pseudo', 'Pseudo')
                ->setFormTypeOptions(['attr' => ['readonly' => true, 'id' => 'user_pseudo']])
                ->onlyWhenCreating(),
            TextField::new('firstname', 'Prénom :')
                ->setFormTypeOptions(['attr' => ['placeholder' => 'Prénom de l\'utilisateur']])
                ->setColumns(6),
            TextField::new('lastname', 'Nom :')
                ->setFormTypeOptions(['attr' => ['placeholder' => 'Nom de l\'utilisateur']])
                ->setColumns(6),
            EmailField::new('email', 'Email :')
                ->setFormTypeOptions(['attr' => ['placeholder' => 'Email de l\'utilisateur']]),
            FormField::addFieldset('Mot de passe (uniquement à la création)')->onlyWhenCreating(),
            TextField::new('plainPassword', 'Mot de passe')
                ->setFormType(PasswordType::class)
                ->setFormTypeOptions([
                    'mapped' => false, // CLÉ !
                    'required' => $pageName === Crud::PAGE_NEW,
                    'attr' => ['autocomplete' => 'new-password'],
                ])
                ->onlyWhenCreating(),
            FormField::addFieldset('Adresse de l\'utilisateur'),
            TextField::new('adress', 'Adresse :')
                ->setFormTypeOptions(['attr' => ['placeholder' => 'Adresse de l\'utilisateur']])
                ->setColumns(6)
                ->hideOnIndex(),
            TextField::new('postalCode', 'Code postal :')
                ->setFormTypeOptions(['attr' => ['placeholder' => 'Code postal de l\'utilisateur']])
                ->setColumns(6)
                ->hideOnIndex(),
            TextField::new('city', 'Ville :')
                ->setFormTypeOptions(['attr' => ['placeholder' => 'Ville de l\'utilisateur']])
                ->setColumns(6)
                ->hideOnIndex(),
            TextField::new('phone', 'Téléphone')
                ->setFormType(PhoneNumberType::class)
                ->setFormTypeOptions([
                    'default_region' => 'FR',
                    'format' => PhoneNumberFormat::NATIONAL,
                    'attr' => ['placeholder' => 'Téléphone de l\'utilisateur']
                ])
                ->setColumns(6)
                ->onlyOnForms(),
            TextField::new('phone', 'Téléphone')
                ->formatValue(function ($value, $entity) {
                    $value = $entity->getPhone();
                    $formattedValue = $this->phoneNumberHelper->format($value, 2);
                    return $formattedValue;
                })
                ->onlyOnIndex(),
            FormField::addFieldset('Avatar de l\'utilisateur'),
            ImageField::new('avatar.imageName', 'Avatar')
                ->setBasePath('images/avatars')
                ->setUploadDir('public/images/avatars')
                ->onlyOnIndex()
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setRequired(false),
            TextField::new('avatar', 'Avatar :')
                ->setFormType(AvatarForm::class)
                ->setTranslationParameters(['form.label.delete' => 'Supprimer l\'image'])
                ->hideOnIndex(),
            FormField::addFieldset('Rôles de l\'utilisateur'),
            ChoiceField::new('roles')
                ->setChoices([
                    'Administrateur' => 'ROLE_ADMIN',
                    'Employé' => 'ROLE_EMPLOYE',
                    // Pas d'affichage de ROLE_USER afin d'éviter qu'il soit décoché
                ])
                ->allowMultipleChoices()
                ->renderExpanded()
                ->renderAsBadges()
                ->hideOnIndex(),
            ChoiceField::new('roles')
                ->setChoices([
                    'Administrateur' => 'ROLE_ADMIN',
                    'Employé' => 'ROLE_EMPLOYE',
                    'Utilisateur' => 'ROLE_USER',
                ])
                ->renderAsBadges()
                ->setChoices(array_flip([
                    'ROLE_ADMIN' => 'Administrateur',
                    'ROLE_EMPLOYE' => 'Employé',
                    'ROLE_USER' => 'Utilisateur',
                ])) // Inverse clés/valeurs afin d'éviter le formatValue()
                ->onlyOnIndex(),
            BooleanField::new('isSuspended', 'Compte suspendu ?')
                ->renderAsSwitch(false)
                ->setHelp('Si activé, l\'utilisateur ne pourra plus se connecter.')
                ->onlyOnForms(),
            BooleanField::new('isSuspended', 'Suspendu')
                ->renderAsSwitch(false)
                ->onlyOnIndex(),
        ];
    }

    private ?string $plainPasswordForMail = null;

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance->getPseudo()) {
            $pseudo = $this->pseudoGeneratorService->generate(
                $entityInstance->getFirstname(),
                $entityInstance->getLastname()
            );
            $entityInstance->setPseudo($pseudo);
        }

        $roles = $entityInstance->getRoles();
        if (!in_array('ROLE_USER', $roles, true)) {
            $roles[] = 'ROLE_USER';
            $entityInstance->setRoles($roles);
        }
        $entityInstance->setFirstname(ucfirst($entityInstance->getFirstname()))
            ->setLastname(strtoupper($entityInstance->getLastname()));
        $entityInstance->setMustChangePassword(true);

        // On n'envoie le mail que si on a un mot de passe à transmettre
        if ($this->plainPasswordForMail) {
            $this->sendMailService->sendMail(
                'Application EcoRide',
                $entityInstance->getEmail(), // <<<<<<
                'Message à nos employés : votre compte a été créé !',
                'new_account',
                [
                    'user' => $entityInstance,
                    'password' => $this->plainPasswordForMail,
                ]
            );
            $this->plainPasswordForMail = null; // Toujours sécuriser
        }

        parent::persistEntity($entityManager, $entityInstance);
    }


    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $roles = $entityInstance->getRoles();
        if (!in_array('ROLE_USER', $roles, true)) {
            $roles[] = 'ROLE_USER';
            $entityInstance->setRoles($roles);
        }
        $entityInstance->setFirstname(ucfirst($entityInstance->getFirstname()))
            ->setLastname(strtoupper($entityInstance->getLastname()));
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function createNewFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $formBuilder = parent::createNewFormBuilder($entityDto, $formOptions, $context);
        return $this->addPasswordEventListener($formBuilder);
    }

    private function addPasswordEventListener(FormBuilderInterface $formBuilder): FormBuilderInterface
    {
        $formBuilder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $entity = $event->getData();

            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $this->plainPasswordForMail = $plainPassword; // <-- on le garde pour l'email !
                $hash = $this->passwordHasher->hashPassword($entity, $plainPassword);
                $entity->setPassword($hash);
            }
        });

        return $formBuilder;
    }
}