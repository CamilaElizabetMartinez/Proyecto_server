<?php

namespace App\Controller\admin;

use App\Repository\CategoryRepository;
use App\Service\CategoryNormalize;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
* @Route("/api/admin/category", name="api_category_admin")
*/
class ApiCategoryController extends AbstractController
{
}
