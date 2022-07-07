<?php

namespace App\Controller;

use App\Entity\Categories;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categories', name: 'categories_')]
class CategoriesController extends AbstractController
{
    #[Route('/{slug}', name: 'list')]
    public function index(Categories $category): Response
    {
        //on cherche les produits de la category
        $products = $category->getProducts();
        return $this->render('categories/list.html.twig', 
            //['category' => $category],
            compact('category', 'products'),
        );
    }
}
