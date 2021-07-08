<?php

namespace App\Service;

use App\Entity\ImageProduct;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\UrlHelper;

class ProductNormalize
{
    private $urlHelper;

    public function __construct(UrlHelper $constructorDeURL)
    {
        $this->urlHelper = $constructorDeURL;
    }

    /**
     * Normalize an product.
     *
     * @param Product
     *
     * @return array|null
     */
    public function productNormalize(Product $product): ?array
    {
        $img_principal = '';
        if ($product->getImgPrincipal()) {
            $img_principal = $this->urlHelper->getAbsoluteUrl('/media/img/product/'.$product->getImgPrincipal());
        }

        $productsImage = [];
        foreach ($product->getImageProducts() as $productImage) {
            $productsImage[] = $this->urlHelper->getAbsoluteUrl('/media/img/product/'.$productImage->getImgFile());
        }

        return [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'category_name' => $product->getCategory()->getName(),
            'category_id' => $product->getCategory()->getId(),
            'slug' => $product->getSlug(),
            'img_principal' => $img_principal,
            'weight' => $product->getWeight(),
            'price' => $product->getPrice(),
            'imageProduct' => $productsImage,
            'user_admin' => [
                'user_name' => $product->getUser()->getName(),
                'user_company' => $product->getUser()->getCompanyName(),
                'user_phone' => $product->getUser()->getPhoneNumber(),
                'user_city' => $product->getUser()->getCity(),
                'user_address' => $product->getUser()->getAddress(),
                'user_avatar' => $product->getUser()->getAvatar(),
            ],
        ];
    }
}
