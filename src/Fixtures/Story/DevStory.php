<?php

namespace App\Fixtures\Story;

use App\Fixtures\Factory\BranchFactory;
use App\Fixtures\Factory\DomainFactory;
use App\Fixtures\Factory\ObjectiveFactory;
use App\Fixtures\Factory\SkillFactory;
use App\Fixtures\Factory\StepFactory;
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
                    'label' => $branchData['label'],
                    'domains' => [$domain],
                ]);

                foreach ($branchData['skills'] as $skillData) {
                    $skill = SkillFactory::createOne([
                        'label' => $skillData['label'],
                        'branches' => [$branch],
                    ]);

                    foreach ($skillData['objectives'] as $objectiveData) {
                        $objective = ObjectiveFactory::createOne([
                            'label' => $objectiveData['label'],
                            'duration' => $objectiveData['duration'],
                            'skill' => $skill,
                        ]);

                        foreach ($objectiveData['steps'] as $stepData) {
                            StepFactory::createOne([
                                'label' => $stepData['label'],
                                'instruction' => $stepData['instruction'],
                                'objective' => $objective
                            ]);
                        }
                    }
                }
            }
        }
    }
}
