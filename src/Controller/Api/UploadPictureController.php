<?php

namespace App\Controller\Api;

use App\Entity\Tip;
use App\Repository\TipRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class UploadPictureController extends AbstractController
{
        /**
     * Route pour uploader une img
     *
     * @return Response
     */
    #[Route('api/recipe/upload', name: 'api_recipe_upload', methods: ['POST'])]
    public function upload(Request $request, ParameterBagInterface $params)
    {
        //AJOUTER DES IMAGES UPLOADEES
        $image = $request->files->get('file');
        //dd($image);
        // enregistrement de l'image dans le dossier public du serveur
        // paramas->get('public') =>  va chercher dans services.yaml la variable public
        $newFilename = uniqid() . '.' . $image->getClientOriginalName();
        $image->move($params->get('pictures_directory'), $newFilename);
        // ne pas oublier d'ajouter l'url de l'image dans l'entitée appropriée
		// $entity est l'entity qui doit recevoir votre image

        return $this->json($newFilename, 200, ['message' => 'Image uploaded successfully.']);

    }
    /**
     * SUPPRIMER UNE IMAGE UPLOADÉE SI ELLE N'EST PAS UTILISÉE
     * *EN COURS
     *
     * @return Response
     */
    /*
    #[Route('api/recipe/upload/delete', name: 'api_recipe_upload_delete', methods: ['DELETE'])]
    public function deleteImgUseless(Request $request, ParameterBagInterface $params, RecipeRepository $recipeRepository)
    {
        //Je viens récupérer le dossier contenant toutes mes images
        $imagesfile = scandir($params->get('pictures_directory'));
        
        $imagesFromBdd = $recipeRepository->pictureFromRecipe();
        dd($imagesFromBdd);
*/

}