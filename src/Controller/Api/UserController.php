<?php

namespace App\Controller\Api;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route; // Add this import statement
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

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
    public function editUserPassword(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator)
    {
        // Je viens récupérer les infos de l'utilisateur
        $user = $this->getUser();
        //Je viens récupérer les infos envoyées en Json (le nouvel username demandé)
        $content = $request->getContent();
        $data = json_decode($content, true); 

        //Je viens récupérer le mdp dans la bdd
        $passwordBdd = $user->getPassword();
        //Je viens récupéré le champ rempli par l'user : oldpassword
        $oldPassword = $data['oldPassword'];
        $verifPassword = password_verify($oldPassword, $passwordBdd);

        if($verifPassword == true)
        {
            //Alors j'ajoute le nouveau mdp mais je le hash avant par la méthode hashPassword
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $data['newPassword']
                    )
                );
                $entityManager->persist($user);
        }
        else
        {
            echo 'Mot de passe incorrect';
            return $this->json(
                $user,
                404,
                ['Mot de passe incorrect'],
                ['groups' => 'get_users'] 
            );
        }
        $entityManager->flush();

        return $this->json(
            $user,
            200,
            ['Mot de passe modifié avec succès !'],
            ['groups' => 'get_users'] 
        );
        
    }


}