<?php


namespace App\Controller\Api;

use Assert\Regex;
use App\Entity\User;
use Assert\NotBlank;
use App\Security\LoginAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

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
        // je stocke le mot de passe proposé en requête. 
        $mdp = $data['password'];
        
        /*Je veux venir rajouter des contraintes de champs pour le mdp suivant les réglementations conseillées par la CNIL
        Je viens donc appelé mon validator passer en argument de ma fonction*/
        $violations = $validator->validate($mdp, 
        //Je lui demande de vérifier le mdp reçu et de lui passer une contrainte de remplissage, puis une contrainte sous forme de regex (ou expression régulière)
        [
        new Assert\NotBlank(),
        new Assert\Regex([
            'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*[@$!%?&])(?=.*\d).{12,}$/',
            'match' => true,
            'message' => 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre, un caractère spécial et contenir au minimum 12 caractères'
            ]),
        ]);
        //dd($violations);
        if(count($violations) > 0)
        {
            return $this->json($violations, 500, ['message' => 'error']);
        }
        else
        {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $data['password']
                )
            ); 
        }
    
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


