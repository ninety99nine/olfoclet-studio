<?php

namespace App\Http\Controllers\Api;

use App\Models\Project;
use App\Models\Subscriber;
use App\Http\Controllers\Controller;
use App\Repositories\MessageRepository;
use App\Repositories\SubscriberRepository;
use App\Http\Resources\SubscriptionResource;
use App\Http\Requests\Subscribers\CreateSubscriberRequest;
use App\Http\Requests\Subscribers\UpdateSubscriberRequest;
use App\Http\Resources\SubscriberResource;

class SubscriberApiController extends Controller
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
        // Get the subscribers using the repository with the required relationships and pagination
        $subscribers = $this->subscriberRepository->getProjectSubscribers();

        // Return subscribers as a JSON response using SubscriptionResource
        return SubscriberResource::collection($subscribers)->response();
    }

    public function createSubscriber(CreateSubscriberRequest $request)
    {
        //  Get the MSISDN
        $msisdn = $request->input('msisdn');

        // Create new subscriber using the repository
        $subscriber = $this->subscriberRepository->createProjectSubscriber($msisdn);

        // Return the created subscriber as a JSON response using SubscriptionResource
        return (new SubscriptionResource($subscriber))->response()->setStatusCode(201);
    }

    public function updateSubscriber(UpdateSubscriberRequest $request)
    {
        //  Get the MSISDN
        $msisdn = $request->input('msisdn');

        // Update subscriber using the repository
        $this->subscriberRepository->updateProjectSubscriber($msisdn);

        // Return a success JSON response
        return response()->json(['message' => 'Updated Successfully']);
    }

    public function deleteSubscriber()
    {
        // Delete subscriber using the repository
        $this->subscriberRepository->deleteProjectSubscriber();

        // Return a success JSON response
        return response()->json(['message' => 'Deleted Successfully']);
    }
}
