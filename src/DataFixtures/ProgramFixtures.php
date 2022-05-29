<?php

namespace App\DataFixtures;

use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    public const PROGRAMS = [
        1 => ['title' => 'Walking dead', 'synopsis' => 'Des zombies envahissent la terre', 'poster' => 'walkingdead.jpeg', 'category' => 'category_Horreur', 'country' => 'USA', 'year' => '2020'],
        2 => ['title' => 'All of us are dead', 'synopsis' => 'Où sont les vivants?', 'poster' => 'all_of_us_are_dead.jpeg', 'category' => 'category_Horreur', 'country' => 'USA', 'year' => '2020'],
        3 => ['title' => 'American Horror Story', 'synopsis' => "Une histoire de film d'horreur", 'poster' => 'ahs.jpeg', 'category' => 'category_Horreur', 'country' => 'USA', 'year' => '2020'],
        4 => ['title' => 'Slasher', 'synopsis' => "D'ignobles tueurs en série sèment l'effroi", 'poster' => 'slasher.jpeg', 'category' => 'category_Horreur', 'country' => 'USA', 'year' => '2020'],
        5 => ['title' => 'Stranger things', 'synopsis' => "Comme c'est étrange", 'poster' => 'strangerThings.jpeg', 'category' => 'category_Fantastique', 'country' => 'USA', 'year' => '2020'],
        6 => ['title' => 'Breaking bad', 'synopsis' => "Un peu de chimie", 'poster' => 'breakingbad.jpeg', 'category' => 'category_Action', 'country' => 'USA', 'year' => '2020'],
        7 => ['title' => 'The Witcher', 'synopsis' => "Des bonbons ou un sort !", 'poster' => 'the_witcher.jpeg', 'category' => 'category_Aventure', 'country' => 'USA', 'year' => '2020'],
        8 => ['title' => 'Arcane', 'synopsis' => "Entre technologies magiques et convictions incompatibles", 'poster' => 'arcane.jpeg', 'category' => 'category_Animation', 'country' => 'USA', 'year' => '2020']
    ];

    public function load(ObjectManager $manager)
    {
        foreach (self::PROGRAMS as $key => $programName) {
            $program = new Program();
            $program->setTitle($programName['title']);
            $program->setSynopsis($programName['synopsis']);
            $program->setPoster($programName['poster']);
            $program->setCategory($this->getReference($programName['category']));
            $program->setCountry($programName['country']);
            $program->setYear($programName['year']);
            $this->addReference('program_' . $key, $program);
            $manager->persist($program);
            
        }
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


