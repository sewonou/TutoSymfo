<?php

namespace App\Controller\Admin;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/recettes',  'admin.recipe.')]
#[IsGranted('ROLE_USER')]
class RecipeController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request, RecipeRepository $repository, EntityManagerInterface $em): Response
    {
        //$this->denyAccessUnlessGranted('ROLE_USER');
        $page = $request->query->getInt('page', 1);
        $limit = 1;
        $recipes = $repository->paginateRecipes($page);
        $maxPage = ceil($recipes->getTotalItemCount() / $limit);
        //$recipes = $repository->findWithDurationLowerThan(0);

        //dd($recipes);
        return $this->render('admin/recipe/index.html.twig', [
            'recipes' =>  $recipes,
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(EntityManagerInterface $em, Request $request)
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
           /* UploadedFile $file*/
            /* ceci est une logique au cas ou on fait l'import des fichiers sans bundle
            $file = $form->get('thumbnailFile')->getData();
            $fileName = $recipe->getSlug() . '.' . $file->getClientOriginalExtension();
            $file->move($this->getParameter('kernel.project_dir') .'/public/recettes/images', $fileName);
            $recipe->setThumbnail($fileName);*/

            $em->persist($recipe);
            $em->flush();
            $this->addFlash('success', "La recette à bien été créée");
            return $this->redirectToRoute('admin.recipe.index');
        }

        return $this->render('admin/recipe/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', 'edit', methods: ['GET','POST'])]
    public function edit(EntityManagerInterface $em, Recipe $recipe, Request $request)
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        //dd($recipe);

        if($form->isSubmitted() && $form->isValid()){
            $em->flush();
            $this->addFlash('success',  "La recette a bien été modifiée");
            return $this->redirectToRoute('admin.recipe.index');
        }

        return $this->render('admin/recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'show', requirements: ['id' => Requirement::DIGITS])]
    public function show(Request $request, Recipe $recipe):  Response
    {

        return $this->render('admin/recipe/show.html.twig', [
            'recipe' => $recipe
        ]);
    }




    #[Route('/{id}', name: 'delete', methods:['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function remove(EntityManagerInterface $em, Recipe $recipe)
    {
        $em->remove($recipe);
        $em->flush();
        $this->addFlash('success', 'La recette a bien été supprimée');
        return $this->redirectToRoute('admin.recipe.index');
    }
}
