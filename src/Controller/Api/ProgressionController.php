<?php

namespace App\Controller\Api;

use App\Dto\Request\SkillDiscoverInput;
use App\Libs\Domain\ProgressionEngine\Scenario\PortalOpen\PortalOpenPayload;
use App\Libs\Domain\ProgressionEngine\Scenario\QuestOpen\QuestOpenPayload;
use App\Entity\Objective;
use App\Entity\Step;
use App\Libs\Domain\ProgressionEngine\Scenario\SkillDiscover\SkillDiscoverPayload;
use App\Libs\Domain\ProgressionEngine\Scenario\SkillDiscover\SkillDiscoverScenario;
use App\Libs\Queue\QueueManagerInterface;
use App\Libs\Queue\QueueJobEnum;
use App\ValueResolver\{ObjectiveValueResolver as ObjectiveVR, StepValueResolver as StepVal};
use Symfony\Component\HttpFoundation\{JsonResponse, Response};
use Symfony\Component\HttpKernel\Attribute\{MapRequestPayload as Map, ValueResolver as VR};
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/progression', name: 'api.progression.')]
class ProgressionController extends AbstractApiController
{
    public function __construct(
        private ObjectMapperInterface $objectMapper,
        private QueueManagerInterface $queueManager,
    ) {}

    #[Route('/portal/open/{objective_id}', name: "portal.open", methods: ['GET'])]
    public function portalOpen(#[VR(ObjectiveVR::class)] Objective $objective): JsonResponse
    {
        $dto = new PortalOpenPayload($objective->getId());
        $this->queueManager->enqueue(QueueJobEnum::SCENARIO_PORTAL_OPEN, $dto);

        return $this->json([], Response::HTTP_OK);
    }

    #[Route('/quest/open/{step_id}', name: "quest.open", methods: ['GET'])]
    public function questStart(#[VR(StepVal::class)] Step $step): JsonResponse
    {
        $dto = new QuestOpenPayload($step->getId());
        $this->queueManager->enqueue(QueueJobEnum::SCENARIO_QUEST_OPEN, $dto);
        return $this->json([], Response::HTTP_OK);
    }

    #[Route('/skill/discover', name: "skill.discover", methods: ['POST'])]
    public function skillDiscover(
        #[Map] SkillDiscoverInput $input,
        SkillDiscoverScenario $skillDiscoverScenario
    ): JsonResponse {
        $message = $this->objectMapper->map($input, SkillDiscoverInput::class);
        return $this->jsonCreated($skillDiscoverScenario->run(new SkillDiscoverPayload($message->message)));
    }
}
