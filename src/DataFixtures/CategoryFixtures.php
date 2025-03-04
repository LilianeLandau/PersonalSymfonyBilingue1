<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = [
            ['titleFr' => 'Fruits', 'titleEn' => 'Fruits'],
            ['titleFr' => 'Légumes', 'titleEn' => 'Vegetables'],
            ['titleFr' => 'Fleurs', 'titleEn' => 'Flowers'],
        ];

        foreach ($categories as $categoryData) {
            $category = new Category();
            $category->setTitleFr($categoryData['titleFr']);
            $category->setTitleEn($categoryData['titleEn']);
            $manager->persist($category);

            // On référence chaque catégorie par un identifiant unique
            $this->addReference('category_' . strtolower($categoryData['titleFr']), $category);
        }

        $manager->flush();
    }
}
