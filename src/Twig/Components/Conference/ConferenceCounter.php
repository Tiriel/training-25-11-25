<?php

namespace App\Twig\Components\Conference;

use App\Entity\Conference;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class ConferenceCounter
{
    use DefaultActionTrait;

    #[LiveProp]
    public ?Conference $conference = null;
    #[LiveProp]
    public int $selectionCount = 0;

    #[LiveListener('conference:selected')]
    public function onConferenceSelected(#[LiveArg] Conference $conference): void
    {
        $this->conference = $conference;
        $this->selectionCount++;
    }
    #[LiveListener('conference:back_to_list')]
    public function onBackToList(): void
    {
        $this->selectionCount = 0;
        $this->conference = null;
    }
}
