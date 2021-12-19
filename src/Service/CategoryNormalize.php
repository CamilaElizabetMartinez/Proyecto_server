<?php

namespace App\Service;

use App\Entity\Category;

class CategoryNormalize
{
    /**
    * Normalize an Category.
    *
    * @param Category
    *
    * @return array|null
    */
    public function CategoryNormalize(Category $category): ?array
    {
        //Retorna los datos en formato JSON
        return [
            'categoryId' =>$category->getId(),
            'categoryName' =>$category->getName(),
        ];
    }
}
