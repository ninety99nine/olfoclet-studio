<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Project;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use App\Repositories\MessageRepository;
use App\Repositories\SubscriberRepository;
use App\Http\Requests\Subscribers\CreateSubscriberRequest;
use App\Http\Requests\Subscribers\UpdateSubscriberRequest;

class SubscriberController extends Controller
{
    protected $project;
    protected $messageRepository;
    protected $subscriberRepository;

    public function __construct()
    {
        $this->project = Project::findOrFail(request()->route('project'));
        $subscriber = request()->route('subscriber') ? Subscriber::findOrFail(request()->route('subscriber')) : null;

        $this->messageRepository = new MessageRepository($this->project);
        $this->subscriberRepository = new SubscriberRepository($this->project, $subscriber);
    }

    public function showSubscribers(Request $request)
    {
        // Count the messages
        $totalMessages = $this->messageRepository->countProjectMessages();

        // Build query parameters for filtering
        $filters = [
            'msisdn' => $request->query('msisdn'),
            'status' => $request->query('status'),
            'billingStatus' => $request->query('billingStatus'),
        ];

        // Get the subscribers using the repository with the required relationships and pagination
        $subscribers = $this->subscriberRepository->getProjectSubscribers(
            $filters,
            ['latestSubscription', 'latestUserBillingTransaction', 'latestAutoBillingTransaction'],
            [
                'messages', 'subscriptions',
                'userBillingTransactions', 'successfulUserBillingTransactions', 'unsuccessfulUserBillingTransactions',
                'autoBillingTransactions', 'successfulAutoBillingTransactions', 'unsuccessfulAutoBillingTransactions',
                'messagesAsContent', 'messagesAsPaymentConfirmation', 'messagesAsAutoBillingReminder'
            ]
        );

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

        //  Get the Metadata
        $metadata = $request->input('metadata');

        if(is_string($metadata)) {

            $metadata = json_decode($metadata, true);

        }

        // Create new subscriber using the repository
        $this->subscriberRepository->createProjectSubscriber($msisdn, $metadata);

        return redirect()->back()->with('message', 'Created Successfully');
    }

    public function updateSubscriber(UpdateSubscriberRequest $request)
    {
        //  Get the MSISDN
        $msisdn = $request->input('msisdn');

        //  Get the Metadata
        $metadata = $request->input('metadata');

        if(is_string($metadata)) {

            $metadata = json_decode($metadata, true);

        }

        // Update subscriber using the repository
        $this->subscriberRepository->updateProjectSubscriber($msisdn, $metadata);

        return redirect()->back()->with('message', 'Updated Successfully');
    }

    public function deleteSubscriber()
    {
        // Delete subscriber using the repository
        $this->subscriberRepository->deleteProjectSubscriber();

        return redirect()->back()->with('message', 'Deleted Successfully');
    }
}
