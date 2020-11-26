<?php


namespace App\DataFixtures;


use App\Entity\AdminUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $userAdmin = new AdminUser();
        $userAdmin->setEmail('tristan.spinnewyn@gmail.com');
        $userAdmin->setUsername('admin');
        $userAdmin->setPlainPassword('azerty');

        $manager->persist($userAdmin);

        $manager->flush();
    }
}