<?php

namespace App\Http\Controllers\Api;

use App\Models\Project;
use App\Models\Subscriber;
use App\Http\Controllers\Controller;
use App\Repositories\MessageRepository;
use App\Repositories\SubscriberRepository;
use App\Http\Resources\SubscriberResource;
use App\Http\Requests\Subscribers\CreateSubscriberRequest;
use App\Http\Requests\Subscribers\ShowSubscribersRequest;
use App\Http\Requests\Subscribers\UpdateSubscriberRequest;

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

    public function showSubscribers(ShowSubscribersRequest $request)
    {
        //  Get the MSISDN if provided
        $msisdn = $request->filled('msisdn') ? $request->input('msisdn') : null;

        // Get the subscribers using the repository
        $subscribers = $this->subscriberRepository->getProjectSubscribers($msisdn);

        // Return subscribers as a JSON response using SubscriberResource
        return SubscriberResource::collection($subscribers)->response();
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
        $subscriber = $this->subscriberRepository->createProjectSubscriber($msisdn, $metadata);

        // Return the created subscriber as a JSON response using SubscriberResource
        return (new SubscriberResource($subscriber))->response()->setStatusCode(201);
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
