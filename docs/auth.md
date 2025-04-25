Voici les étapes clés pour mettre en place un système d’authentification dans un projet Symfony (version 5.x/6.x). Je pars du principe que vous avez déjà un projet Symfony opérationnel.

---

## 1. Installer les bundles nécessaires

1. Assurez-vous d’avoir le SecurityBundle (fourni par défaut) et le MakerBundle pour générer du code :

```bash
composer require symfony/security-bundle
composer require symfony/maker-bundle --dev
```

---

## 2. Créer l’entité User

Générez votre entité User avec les interfaces requises :

```bash
php bin/console make:user
```

- **Class name of the user to create or update**: `User`
- **Do you want to store users in the database (via Doctrine)?**: `yes`
- **Enter a property name that will be the unique "display" name for the user**: `email`
- **Will this app need to hash/check user passwords?**: `yes`

Cela crée une entité `src/Entity/User.php` implémentant `UserInterface` et `PasswordAuthenticatedUserInterface`, avec un champ `email` et un champ `password`.

Pensez à générer la migration et l’exécuter :

```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

---

## 3. Mettre en place le formulaire et le contrôleur de login

Générez un guard authenticator (login form) :

```bash
php bin/console make:auth
```

- **Choose the kind of authentication**: `Login form authenticator`
- **Name of the authenticator class**: `AppAuthenticator`
- **Controller path to redirect after success**: `/` (ou votre route d’accueil)

Cela crée :

- `src/Security/AppAuthenticator.php`
- `templates/security/login.html.twig`
- Un flux de redirection (successHandler, failureHandler).

---

## 4. Configurer `config/packages/security.yaml`

Adaptez votre configuration :

```yaml
security:
    password_hashers:
        App\Entity\User:
            algorithm: auto

    providers:
        app_user_provider:
            entity:
                class: App\Entity\User
                property: email

    firewalls:
        # Zone d’accès public (CSS, images, login…)
        dev:
            pattern: ^/_(profiler|wdt)
            security: false

        main:
            pattern: ^/
            provider: app_user_provider

            # activation du formulaire de login
            custom_authenticator: App\Security\AppAuthenticator

            logout:
                path: app_logout
                # où rediriger après déconnexion
                target: app_login

            # pour autoriser la saisie des credentials
            # anonymous: true

    access_control:
        # page de login accessible à tous
        - { path: ^/login$, roles: IS_AUTHENTICATED_ANONYMOUSLY }
        # tout le reste nécessite d’être connecté
        - { path: ^/,       roles: ROLE_USER }
```

- **password_hashers** : Symfony gère le salage et le hachage sécurisé.
- **providers** : comment on récupère les utilisateurs (ici via Doctrine).
- **firewalls** : configuration du pare-feu principal (`main`).
- **access_control** : contrôle d’accès par route.

---

## 5. Créer un formulaire d’inscription (optionnel)

Si vous souhaitez proposer l’inscription :

1. Créez un contrôleur `RegistrationController` :

    ```php
    // src/Controller/RegistrationController.php
    namespace App\Controller;

    use App\Entity\User;
    use App\Form\RegistrationFormType;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
    use Symfony\Component\Routing\Annotation\Route;

    class RegistrationController extends AbstractController
    {
        #[Route('/register', name: 'app_register')]
        public function register(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $em): Response
        {
            $user = new User();
            $form = $this->createForm(RegistrationFormType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // hachage du mot de passe
                $user->setPassword(
                    $hasher->hashPassword($user, $form->get('plainPassword')->getData())
                );
                $em->persist($user);
                $em->flush();

                return $this->redirectToRoute('app_login');
            }

            return $this->render('registration/register.html.twig', [
                'registrationForm' => $form->createView(),
            ]);
        }
    }
    ```

2. Créez le FormType :

    ```php
    // src/Form/RegistrationFormType.php
    namespace App\Form;

    use App\Entity\User;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\EmailType;
    use Symfony\Component\Form\Extension\Core\Type\PasswordType;
    use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;

    class RegistrationFormType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder
                ->add('email', EmailType::class)
                ->add('plainPassword', RepeatedType::class, [
                    'type'            => PasswordType::class,
                    'first_options'   => ['label' => 'Mot de passe'],
                    'second_options'  => ['label' => 'Confirmez le mot de passe'],
                    'mapped'          => false,
                    'invalid_message' => 'Les mots de passe doivent correspondre.',
                ])
            ;
        }

        public function configureOptions(OptionsResolver $resolver)
        {
            $resolver->setDefaults([
                'data_class' => User::class,
            ]);
        }
    }
    ```

3. Créez la vue Twig `templates/registration/register.html.twig` reprenant `form_start`, `form_widget`, etc.

---

## 6. Personnaliser les templates

- **templates/security/login.html.twig** : adaptez le formulaire de login avec `{{ form_start(form) }}`, `{{ form_row(form.email) }}`, `{{ form_row(form.password) }}`.
- Ajoutez des messages d’erreur (`app.flashes('error')`).

---

## 7. Tester le flux

1. Lancez le serveur :

    ```bash
    symfony serve
    ```

2. Accédez à `/login` : la page de connexion.
3. Accédez à `/register` (si créé) : page d’inscription.
4. Une fois authentifié, vous devez pouvoir atteindre le contenu protégé.

---

## 8. Bonnes pratiques et extensions

- **Encodage** : préférez l’algorithme `auto` (Argon2i si disponible, sinon bcrypt).
- **Rôles** : attribuez `ROLE_USER`, `ROLE_ADMIN`, etc., et adaptez `access_control`.
- **Vérification par email** : installez `symfonycasts/verify-email-bundle`.
- **OAuth/social login** : utilisez `knpuniversity/oauth2-client-bundle`.
- **MFA** : pour l’authentification à deux facteurs, explorez `scheb/2fa-bundle`.

---

Avec ces étapes, vous disposerez d’un système d’authentification robuste, reposant sur les meilleurs standards Symfony. N’hésitez pas à adapter les routes, redirections et templates à votre projet !