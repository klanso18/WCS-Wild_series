<?php

namespace App\DataFixtures;

use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $program = new Program();
        $program->setTitle('Walking dead');
        $program->setSynopsis('Des zombies envahissent la terre');
        $program->setCategory($this->getReference('category_Horreur'));
        $manager->persist($program);
        $manager->flush();

        $program = new Program();
        $program->setTitle('Stranger things');
        $program->setSynopsis("Comme c'est étrange");
        $program->setCategory($this->getReference('category_Fantastique'));
        $manager->persist($program);
        $manager->flush();

        $program = new Program();
        $program->setTitle('Breaking bad');
        $program->setSynopsis("Un peu de chimie");
        $program->setCategory($this->getReference('category_Action'));
        $manager->persist($program);
        $manager->flush();

        $program = new Program();
        $program->setTitle('The Witcher');
        $program->setSynopsis("Des bonbons ou un sort");
        $program->setCategory($this->getReference('category_Aventure'));
        $manager->persist($program);
        $manager->flush();

        $program = new Program();
        $program->setTitle('Arcane');
        $program->setSynopsis("Entre technologies magiques et convictions incompatibles");
        $program->setCategory($this->getReference('category_Animation'));
        $manager->persist($program);
        $manager->flush();
    }

    public function getDependencies()
    {
        // Tu retournes ici toutes les classes de fixtures dont ProgramFixtures dépend
        return [
        CategoryFixtures::class,
        ];
    }

    
}
