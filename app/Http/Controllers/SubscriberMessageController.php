<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Project;
use App\Models\Pivots\SubscriberMessage;
use App\Repositories\SubscriberMessageRepository;

class SubscriberMessageController extends Controller
{
    protected $project;
    protected $subscriberMessage;
    protected $subscriberMessageRepository;

    public function __construct()
    {
        $this->project = Project::findOrFail(request()->route('project'));
        $this->subscriberMessage = request()->route('subscriber_message') ? SubscriberMessage::findOrFail(request()->route('subscriber_message'))->load(['subscriber', 'message']) : null;

        $this->subscriberMessageRepository = new SubscriberMessageRepository($this->project, $this->subscriberMessage);
    }

    public function showSubscriberMessages()
    {
        // Get the subscriber messages using the repository with the required relationships and pagination
        $subscriberMessages = $this->subscriberMessageRepository->getProjectSubscriberMessages(null,
            ['subscriber', 'message'], []
        );

        // Render the subscriber messages view
        return Inertia::render('SubscriberMessages/List/Main', [
            'subscriberMessagesPayload' => $subscriberMessages
        ]);
    }

    public function showSubscriberMessage()
    {
        return Inertia::render('SubscriberMessage/List/Main', [
            'subscriberMessage' => $this->subscriberMessage
        ]);
    }
}
