<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecipeController extends AbstractController
{
    #[Route('/recettes', name: 'recipe.index')]
    public function index(Request $request, RecipeRepository $repository, EntityManagerInterface $em): Response
    {
        $recipes = $repository->findWithDurationLowerThan(30);

        //dd($recipes);
        return $this->render('recipe/index.html.twig', [
            'recipes' =>  $recipes
        ]);
    }

    #[Route('/recettes/{slug}-{id}', name: 'recipe.show', requirements: ['id'=> '\d+', 'slug' => '[a-z0-9-]+'])]
    public function show(Request $request, string $slug, int $id, RecipeRepository $repository):  Response
    {
        $recipe = $repository->find($id);

        if($recipe->getSlug() !== $slug ){
            return $this->redirectToRoute('recipe.show', ['slug' => $recipe->getSlug(), 'id' => $recipe->getId()]);
        }

        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe
        ]);
    }

    #[Route('/recettes/{id}/edit', 'recipe.edit', methods: ['GET','POST'])]
    public function edit(EntityManagerInterface $em, Recipe $recipe, Request $request)
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        //dd($recipe);

        if($form->isSubmitted() && $form->isValid()){
            $recipe->setUpdatedAt(new  \DateTimeImmutable());
            $em->flush();
            $this->addFlash('success',  "La recette a bien été modifiée");
            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/recette/create', name: 'recipe.create')]
    public function create(EntityManagerInterface $em, Request $request)
    {
        $recipe = new Recipe();
        $recipe->setCreatedAt(new  \DateTimeImmutable());
        $recipe->setUpdatedAt(new  \DateTimeImmutable());
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $em->persist($recipe);
            $em->flush();
            $this->addFlash('success', "La recette à bien été créée");
            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('recipe/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/recettes/{id}/edit', name: 'recipe.delete', methods:['DELETE'])]
    public function delete(EntityManagerInterface $em, Recipe $recipe)
    {
        $em->remove($recipe);
        $em->flush();
        $this->addFlash('success', 'La recette a bien été supprimée');
        return $this->redirectToRoute('recipe.index');
    }
}
