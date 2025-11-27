<?php

namespace App\Twig\Components\Conference;

use App\Entity\Conference;
use App\Repository\ConferenceRepository;
use DateTimeImmutable;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class ConferenceList
{
    use ComponentToolsTrait;
    use DefaultActionTrait;

    #[LiveProp]
    public string $search = '';
    #[LiveProp]
    public string $organization = '';
    #[LiveProp]
    public string $sortBy = '';
    #[LiveProp]
    public string $sortDirection = '';
    #[LiveProp]
    public ?Conference $selectedConference = null;
    #[LiveProp]
    public ?DateTimeImmutable $fromDate = null;
    #[LiveProp]
    public ?DateTimeImmutable $toDate = null;

    public function __construct(private ConferenceRepository $conferenceRepository)
    {
    }

    #[LiveAction]
    public function selectConference(#[LiveArg] Conference $conference): void
    {
        $this->selectedConference = $conference;

        $this->emit('conference:selected', [
            'conference' => $this->selectedConference->getId(),
        ]);
    }

    #[LiveAction]
    public function backToList(): void
    {
        $this->emit('conference:back_to_list');
        $this->selectedConference = null;
    }

    public function getConferences(): array
    {
        if (null === $this->fromDate && null === $this->toDate) {
            return $this->conferenceRepository->findAll();
        }

        return $this->conferenceRepository->findConferencesBetweenDates($this->fromDate, $this->toDate);
    }
}
