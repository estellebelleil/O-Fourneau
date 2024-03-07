<?php


namespace App\Controller\Api;

use App\Entity\User;
use App\Security\LoginAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegisterController extends AbstractController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request, ValidatorInterface $validator, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, LoginAuthenticator $authenticator, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true); // je récupère les données envoyées par le client

        $user = new User(); // je crée un nouvel utilisateur
        $user->setname($data['username']);// je créee un nouvel utilisateur avec le nom
        $user->setEmail($data['email']); // je crée un nouvel utilisateur avec l'email
        $user->setRoles(['ROLE_USER']); // je crée un nouvel utilisateur avec le role user
        // je hash le mot de passe
        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $data['password']
            )
        );
        $entityManager->persist($user);

        $errorFields = $validator->validate($user);

        //S'il y a une erreur
        if (count($errorFields) > 0) {

            return $this->json($errorFields, 500, ['message' => 'error']);
        }
        else
        {
            $entityManager->flush();
            
            return $this->json(
            $user,
            200,
            ['Utilisateur ajouté'], // je retourne un message pour informer l'utilisateur que la recette a bien été supprimée
            ['groups' => 'get_users']
            );
        }
    }

    }


