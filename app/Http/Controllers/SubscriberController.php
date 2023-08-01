<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Project;
use App\Models\Subscriber;
use App\Repositories\MessageRepository;
use App\Repositories\SubscriberRepository;
use App\Http\Requests\Subscribers\CreateSubscriberRequest;
use App\Http\Requests\Subscribers\UpdateSubscriberRequest;

class SubscriberController extends Controller
{
    protected $messageRepository;
    protected $subscriberRepository;

    public function __construct()
    {
        $project = Project::findOrFail(request()->route('project'));
        $subscriber = request()->route('subscriber') ? Subscriber::findOrFail(request()->route('subscriber')) : null;

        $this->messageRepository = new MessageRepository($project);
        $this->subscriberRepository = new SubscriberRepository($project, $subscriber);
    }

    public function showSubscribers()
    {
        // Count the messages
        $totalMessages = $this->messageRepository->countProjectMessages();

        // Get the subscribers using the repository with the required relationships and pagination
        $subscribers = $this->subscriberRepository->getProjectSubscribers(['latestSubscription', 'latestMessage'], ['messages', 'subscriptions']);

        // Render the subscribers view
        return Inertia::render('Subscribers/List/Main', [
            'subscribersPayload' => $subscribers,
            'totalMessages' => $totalMessages,
        ]);
    }

    public function createSubscriber(CreateSubscriberRequest $request)
    {
        //  Get the MSISDN
        $msisdn = $request->input('msisdn');

        // Create new subscriber using the repository
        $this->subscriberRepository->createProjectSubscriber($msisdn);

        return redirect()->back()->with('message', 'Created Successfully');
    }

    public function updateSubscriber(UpdateSubscriberRequest $request)
    {
        //  Get the MSISDN
        $msisdn = $request->input('msisdn');

        // Update subscriber using the repository
        $this->subscriberRepository->updateProjectSubscriber($msisdn);

        return redirect()->back()->with('message', 'Updated Successfully');
    }

    public function deleteSubscriber()
    {
        // Delete subscriber using the repository
        $this->subscriberRepository->deleteProjectSubscriber();

        return redirect()->back()->with('message', 'Deleted Successfully');
    }
}
