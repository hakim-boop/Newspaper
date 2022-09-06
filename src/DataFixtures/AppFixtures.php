<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    public function __construct(SluggerInterface $slugger)
    {
        $this->$slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $categories = [
            'Cinema',
            'Sport',
            'People',
            'Politique',
            'Jeu Video',
            'Ecologie',
            'Ecologie',
            'Mode',
            'Société'
        ];

        foreach ($categories as $name) {

            $category = new Category();

            $category->setName($name);
            $category->setAlias($this->Slugger->slug($name));

            $category->setCreatedAt(new DateTime());
            $category->setUpdatedAt(new DateTime());

            # La méthode persist() exécute
            $manager->persist($category);
        }
        # La méthode flush() n'est pas dans la boucle foreach() pour une raison :
        # => cette méthode "vide" l'objet $manager qui est un 'container'.
        # Avant de se 'vider', le $manager exécute les insertions en BDD, pour de vrai cette fois ci !

        $manager->flush();
    }
}
