<?php

namespace App\Controller\Api;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route; // Add this import statement
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    /**
     * Liste toutes les infos de l'utilisateur (données personnelles, recettes ajoutées, recettes en favoris)
     *
     * @return Response
     */
    #[Route('api/user/show', name: 'api_user_show', methods: ['GET'])]
    public function show()
    {
        // Récupérer l'utilisateur actuellement authentifié (vous devrez implémenter la logique d'authentification)
        $user = $this->getUser();
        

        return $this->json(
            $user,
            200,
            ['Voici les données'],
            ['groups' => 'get_users'] 
        );
        
    }
    #[Route('api/user/edit/name', name: 'api_user_edit_name', methods: ['PUT'])]
    public function editUserName(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator)
    {
        // Je viens récupérer les infos de l'utilisateur
        $user = $this->getUser();
        //Je viens récupérer les infos envoyées en Json (le nouvel username demandé)
        $content = $request->getContent();
        $data = json_decode($content, true); 
        
        //MODIFIER LE NAME DE L'USER
        $user->setName($data['username']);
 
        $entityManager->flush();

        return $this->json(
            $user,
            200,
            ['Username modifié'],
            ['groups' => 'get_users'] 
        );
        
    }
    #[Route('api/user/edit', name: 'api_user_edit', methods: ['PUT'])]
    public function editUserPassword(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, ValidatorInterface $validator)
    {
        // Je viens récupérer les infos de l'utilisateur
        $user = $this->getUser();
        //Je viens récupérer les infos envoyées en Json
        $content = $request->getContent();
        $data = json_decode($content, true); 
        //dd($data);
        //Je viens récupérer le mdp dans la bdd
        $passwordBdd = $user->getPassword();
        //Je viens récupérer le champ rempli par l'user : oldpassword
        $oldPassword = $data['oldPassword'];
        //Je fais appel à la fonction password_verify de PHP, je lui passe comme premier argument le password recu dans la requete, puis je le compare avec le PW déjà présent en bdd
        $verifPassword = password_verify($oldPassword, $passwordBdd);
        //Je stocke le nouveau password souhaité depuis ma requête.
        $newmdp = $data['newPassword'];

        //Si le PW correspond
        if($verifPassword == true)
        {
            //Alors je vais vérifier que les données du PW souhaité rentre bien dans les critères exigés pour un PW
            //Je fais appel à la fonction validate du validator interface et lui passe en premier argument les données à vérifier, et enfin les contraintes à lui appliquer. 
            $violations = $validator->validate($newmdp, 
            [
                new Assert\NotBlank(),
                new Assert\Regex([
                    'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*[@$!%?&])(?=.*\d).{12,}$/',
                    'match' => true,
                    'message' => 'Votre nouveau mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre, un caractère spécial et contenir au minimum 12 caractères'
                    ]),
                ]);
                //dd($violations);
            //Si le tableau d'erreurs n'est pas vide (s'il a au moins une erreur)
            if(count($violations) > 0)
            {
                //Je lui renvoie une erreur
                return $this->json($violations, 500, ['message' => 'error']);
            }
            else          
            {  
                //Sinon j'ajoute le nouveau mdp à la BDD mais je le hash avant par la méthode hashPassword
                $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $newmdp
                    )
                );
                //Je persiste pour modifier les informations/ Mais ici ce n'est pas nécéssaire puisque l'objet user est déjà créer en BDD. 
                //$entityManager->persist($user);
            }


        }
        //Si la vérification entre l'ancien mdp et celui en BDD a échoué, je renvoie un message d'erreur. 
        else
        {
            echo 'Mot de passe incorrect';
            return $this->json(
                $user,
                401,
                ['Mot de passe incorrect'],
                ['groups' => 'get_users'] 
            );
        }
        //Je flush pour ajouter les modifications en bdd
        $entityManager->flush();

        //Et je viens renvoyer un message de succès.
        return $this->json(
            $user,
            201,
            ['Mot de passe modifié avec succès !'],
            ['groups' => 'get_users'] 
        );
        
    }


}