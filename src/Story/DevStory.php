<?php

namespace App\Story;

use App\Factory\BranchFactory;
use App\Factory\DomainFactory;
use App\Factory\ObjectiveFactory;
use App\Factory\SkillFactory;
use Zenstruck\Foundry\Attribute\AsFixture;
use Zenstruck\Foundry\Story;

#[AsFixture(name: 'dev')]
final class DevStory extends Story
{
    public function build(): void
    {
        $path = __DIR__ . '/DevStoryData.json';
        $data = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);

        foreach ($data['domains'] as $domainData) {
            $domain = DomainFactory::createOne([
                'label' => $domainData['label'],
            ]);

            foreach ($domainData['branches'] as $branchData) {
                $branch = BranchFactory::createOne([
                    'label'   => $branchData['label'],
                    'domains' => [$domain],
                ]);

                foreach ($branchData['skills'] as $skillData) {
                    $skill = SkillFactory::createOne([
                        'label'    => $skillData['label'],
                        'branches' => [$branch],
                    ]);

                    foreach ($skillData['objectives'] as $objectiveData) {
                        ObjectiveFactory::createOne([
                            'label' => $objectiveData['label'],
                            'difficulty' => $objectiveData['difficulty'],
                            'duration' => $objectiveData['duration'],
                            'skill' => $skill,
                        ]);
                    }
                }
            }
        }
    }
}
