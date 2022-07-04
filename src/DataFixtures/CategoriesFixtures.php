<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoriesFixtures extends Fixture
{
    private $counter = 1;
    public function __construct(private SluggerInterface $slugger)
    {
        
    }

    public function load(ObjectManager $manager): void
    {
        $parent = $this-> createCategory('Informatique', null,   $manager);

        $this->createCategory('Ordinateurs Portables', $parent, $manager);
        $this->createCategory('Souris', $parent, $manager);
        $this->createCategory('Ecrans', $parent, $manager);


        $parent = $this-> createCategory('Mode', null,   $manager);

        $this->createCategory('Hommes', $parent, $manager);
        $this->createCategory('Femmes', $parent, $manager);
        $this->createCategory('Enfants', $parent, $manager);


        $manager->flush();
    }

    public function createCategory(string $name, Categories $parent = null, objectManager $manager)
    {
        $category = new Categories();
        $category->setName($name);
        $category->setSlug($this->slugger->slug($category->getName())->lower());
        $category->setParent($parent);
        $manager->persist($category);
        
        $this->addReference('cat-'.$this->counter, $category);
        $this->counter++;

        return $category;
    }


}
