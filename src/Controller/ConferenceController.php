<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Conference;
use App\Form\ConferenceType;
use App\Repository\ConferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Clock\DatePoint;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

class ConferenceController extends AbstractController
{
    #[Route('/conference/new', name: 'app_conference_new', methods: ['GET', 'POST'])]
    public function newConference(): Response
    {
        return $this->render('conference/new.html.twig');
    }

    #[Route('/conference', name: 'app_conference_list', methods: ['GET'])]
    public function list(
        #[MapQueryParameter('from_date')] ?string $fromDate = null,
        #[MapQueryParameter('to_date')] ?string $toDate = null,
    ): Response
    {
        $fromDate = \is_string($fromDate) ? DatePoint::createFromFormat('Y-m-d', $fromDate) : null;
        $toDate = \is_string($toDate) ? DatePoint::createFromFormat('Y-m-d', $toDate) : null;

        return $this->render('conference/list.html.twig', [
            'from_date' => $fromDate,
            'to_date' => $toDate,
        ]);
    }

    #[Route('/conference/{id}', name: 'app_conference_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Request $request, Conference $conference): Response
    {
        return $this->renderFrame(
            $request,
            'conference/show.html.twig',
            'conference/_show-details.html.twig',
            ['conference' => $conference]
        );
    }

    protected function renderFrame(Request $request, string $fullTemplate, string $frameTemplate, array $parameters): Response
    {
        if ($request->headers->get('Turbo-Frame')) {
            return $this->render($frameTemplate, $parameters);
        }

        return $this->render($fullTemplate, $parameters);
    }
}
