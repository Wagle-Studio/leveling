<?php

namespace App\Fixtures;

use App\Fixtures\Story\DevStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        DevStory::load();
    }
}
