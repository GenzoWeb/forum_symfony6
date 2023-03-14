<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Message;
use App\Entity\Subject;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $numberUsers = 10;
        $categories = [
            'Les bases' => ['HTML/CSS'],
            'Javascript' => ['Angular', 'React', 'VueJS', 'NodeJS'],
            'PHP' => ['Laravel', 'Symfony', 'Wordpress'],
            'Base de données' => ['MySql'],
            'Opportunités' => ["Offres d'emploi" , 'Formations'],
            'Autres' => ['Jeux vidéos']
        ];

        //////////////////////////////////////////// FIXTURES POUR USER ////////////////////////////////////////////
        for ($i = 0; $i < $numberUsers; $i++) {
            $user = new User();
            $user->setPseudo($faker->userName())
                ->setEmail($faker->email())
                ->setPassword(password_hash($faker->word(), PASSWORD_DEFAULT))
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-10 month')));

            $manager->persist($user);
        }

        //////////////////////////////////////////// FIXTURES POUR CATEGORY ////////////////////////////////////////////
        foreach ($categories as $key => $subCategory) {
            $category = new Category();
            $category->setName($key);
            $category->setSubCategory($subCategory);

            $manager->persist($category);
        }

        $manager->flush();

        //////////////////////////////////////////// FIXTURES POUR SUBJECT ////////////////////////////////////////////
        $categRepo = $manager->getRepository(Category::class)->findAll();
        $userRepo = $manager->getRepository(User::class)->findAll();

        for ($i = 0; $i < 15; $i++) {
            $subject = new Subject();

            $subject->setTitle($faker->sentence(mt_rand(5, 15)))
                    ->setPublished(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-10 month')))
                    ->setCategory($faker->randomElement($categRepo))
                    ->setUser($faker->randomElement($userRepo))
                    ->setContent($faker->text());

            $manager->persist($subject);
        }

        $manager->flush();

        //////////////////////////////////////////// FIXTURES POUR MESSAGE ////////////////////////////////////////////
        $subjectRepo = $manager->getRepository(Subject::class)->findAll();

        for ($i = 0; $i < 100; $i++) {
            $message = new Message();

            $message->setContent($faker->realText(mt_rand(20, 40)))
                    ->setPublished(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-10 month')))
                    ->setSubject($faker->randomElement($subjectRepo))
                    ->setUser($faker->randomElement($userRepo));

            $manager->persist($message);
        }
        
        $manager->flush();
    }
}
