<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Produits avec noms en français et anglais
        $products = [
            ['nameFr' => 'Pomme', 'nameEn' => 'Apple', 'category' => 'category_fruits'],
            ['nameFr' => 'Banane', 'nameEn' => 'Banana', 'category' => 'category_fruits'],
            ['nameFr' => 'Tomate', 'nameEn' => 'Tomato', 'category' => 'category_légumes'],
            ['nameFr' => 'Carotte', 'nameEn' => 'Carrot', 'category' => 'category_légumes'],
            ['nameFr' => 'Jasmin', 'nameEn' => 'Jasmine', 'category' => 'category_fleurs'],
            ['nameFr' => 'Rose', 'nameEn' => 'Rose', 'category' => 'category_fleurs'],
        ];

        foreach ($products as $productData) {
            // Création d'un seul produit par entrée
            $product = new Product();
            $product->setNameFr($productData['nameFr']);
            $product->setNameEn($productData['nameEn']);

            // Récupérer la catégorie correspondante avec le type explicite
            $category = $this->getReference($productData['category'], Category::class);
            $product->setCategory($category);

            $manager->persist($product);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
        ];
    }
}
