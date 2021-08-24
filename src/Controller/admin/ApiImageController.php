<?php

namespace App\Controller\admin;

use App\Entity\Category;
use App\Entity\ImageProduct;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Service\ProductNormalize;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/admin/image", name="api_image_")
 */

class ApiImageController extends AbstractController
{
  
}
