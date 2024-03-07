<?php

namespace App\Controller\BackOffice;

use App\Entity\User;
use App\Form\BackOffice\UserPasswordType;
use App\Form\BackOffice\UserType as BackOfficeUserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/back/user')]
class UserController extends AbstractController
{
    #[Route('/list', name: 'user_list')]
    public function list(UserRepository $userRepository): Response
    {

        $users = $userRepository->findAll();
        return $this->render('BackOffice/user/list.html.twig', [
            'users' => $users,
        ]);
    }



    #[Route('/add', name: 'user_add', methods: ['GET', 'POST'])]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(BackOfficeUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {



            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'L\'utilisateur' . $user->getName() . 'a bien été créée !'
            );

            return $this->redirectToRoute('user_list');
            return $this->redirectToRoute('user_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('BackOffice/user/add.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('BackOffice/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/edit/{id}', name: 'user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher): Response
    {
        $form = $this->createForm(BackOfficeUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {



            $entityManager->flush();

            return $this->redirectToRoute('user_list');
        }

        return $this->render('BackOffice/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'user_delete')]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
       
        // if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {

          


            $entityManager->remove($user);
            $entityManager->flush();
        // }

        return $this->redirectToRoute('user_list');
    }


    #[Route('/edit_password/{id}', name: 'user_edit_password', methods: ['GET', 'POST'])]
    public function editPassword(Request $request, User $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher): Response
    {

    

        
        $form = $this->createForm(UserPasswordType::class); 
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $currentPassword = $form->get('password')->getData();
            
        //    dd($userSaved);
            if ($currentPassword !== null && $hasher->isPasswordValid($user, $currentPassword)) {
              

                $plainPassword = $form->get('plainPassword')->getData();
                $hashPlainPassword = $hasher->hashPassword($user, $plainPassword);
                
                $user->setPassword(

                    $hashPlainPassword
                 
                    
                );
                    
                    
                
                
                
                $this->addFlash(
                    'success',
                    'Le mot de passe de l\'utilisateur' . $user->getName() . 'a bien été modifié !'
                );

            $entityManager->persist($user);
                $entityManager->flush();


                return $this->redirectToRoute('main_back');

            } else {
                $this->addFlash(
                    'warning',
                    'Le mot de passe de l\'utilisateur' . $user->getName() . 'n\'a pas été modifié !'
                );
            }

        }
            return $this->render('BackOffice/user/edit_password.html.twig', [
                'user' => $user,
                'form' => $form,
            ]);
        
    }
}
