<?php

namespace App\Twig\Components\Conference;

use App\Entity\Conference;
use App\Entity\Poll;
use App\Entity\Vote;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class ConferencePoll
{
    use DefaultActionTrait;

    #[LiveProp]
    public Conference $conference;
    #[LiveProp]
    public ?Poll $poll = null;
    #[LiveProp]
    public array $messages = [];

    public function __construct(private readonly UrlGeneratorInterface $urlGenerator)
    {}

    #[LiveAction]
    public function newPoll(EntityManagerInterface $manager): RedirectResponse
    {
        $this->poll = new Poll();
        $this->conference->setPoll($this->poll);
        $manager->persist($this->conference);
        $manager->flush();

        return new RedirectResponse(
            $this->urlGenerator->generate('app_conference_show', [
                'id' => $this->conference->getId()
            ])
        );
    }

    #[LiveAction]
    public function vote(#[LiveArg] string $name, EntityManagerInterface $manager): void
    {
        $vote = $this->poll->getVotes()->findFirst(fn($key, Vote $vote) => $vote->getName() === $name);
        $vote->upCount();

        $manager->persist($vote);
        $manager->flush();

        $this->messages[] = [
            'type' => 'success',
            'message' => 'Thank you for voting!',
        ];
    }
}
