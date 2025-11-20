<?php

namespace App\Controller\Api;

use App\Libs\Domain\ProgressionEngine\Scenario\PortalOpen\PortalOpenPayload;
use App\Libs\Domain\ProgressionEngine\Scenario\QuestOpen\QuestOpenPayload;
use App\Entity\Objective;
use App\Entity\Step;
use App\Libs\Queue\QueueManagerInterface;
use App\Libs\Queue\QueueJobEnum;
use App\ValueResolver\{ObjectiveValueResolver as ObjectiveVR, StepValueResolver as StepVal};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Response};
use Symfony\Component\HttpKernel\Attribute\ValueResolver as VR;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/progression', name: 'api.progression.')]
class ProgressionController extends AbstractController
{
    #[Route('/portal/open/{objective_id}', name: "portal.open", methods: ['GET'])]
    public function portalOpen(#[VR(ObjectiveVR::class)] Objective $objective, QueueManagerInterface $queueManager): JsonResponse
    {
        $dto = new PortalOpenPayload($objective->getId());
        $queueManager->enqueue(QueueJobEnum::SCENARIO_PORTAL_OPEN, $dto);

        return $this->json([], Response::HTTP_OK);
    }

    #[Route('/quest/open/{step_id}', name: "quest.open", methods: ['GET'])]
    public function questStart(#[VR(StepVal::class)] Step $step, QueueManagerInterface $queueManager): JsonResponse
    {
        $dto = new QuestOpenPayload($step->getId());
        $queueManager->enqueue(QueueJobEnum::SCENARIO_QUEST_OPEN, $dto);
        return $this->json([], Response::HTTP_OK);
    }
}
