<?php

namespace App\DataFixtures;

use App\Entity\Candys;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;
use Faker;

class CandysFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private SluggerInterface $slugger){}

    public function load(ObjectManager $manager): void
    {
        // use the factory to create a Faker\Generator instance
        $faker = Faker\Factory::create('fr_FR');

        for($cand = 1; $cand <= 10; $cand++){
            $candy = new Candys();
            $candy->setName($faker->text(15));
            $candy->setDescription($faker->text());
            $candy->setSlug($this->slugger->slug($candy->getName())->lower());
            $candy->setPrice($faker->numberBetween(900, 150000));
            $candy->setStock($faker->numberBetween(0, 10));

            //On va chercher une référence de catégorie
            $category = $this->getReference('cat-'. rand(1, 3));
            $candy->setCategories($category);

            $this->setReference('cand-'.$cand, $candy);
            $manager->persist($candy);
        }

        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            CategoriesFixtures::class
        ];  
    }
}