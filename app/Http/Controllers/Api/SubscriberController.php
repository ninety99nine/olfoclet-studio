<?php

namespace App\Http\Controllers\Api;

use App\Models\Project;
use App\Models\Subscriber;
use App\Http\Controllers\Controller;
use App\Repositories\SubscriberRepository;
use App\Http\Resources\SubscriberResource;
use App\Http\Requests\Subscribers\ShowSubscriberRequest;
use App\Http\Requests\Subscribers\CreateSubscriberRequest;
use App\Http\Requests\Subscribers\ShowSubscribersRequest;
use App\Http\Requests\Subscribers\UpdateSubscriberRequest;

class SubscriberController extends Controller
{
    protected $project;
    protected $subscriber;
    protected $subscriberRepository;

    public function __construct()
    {
        $this->project = Project::findOrFail(request()->route('project'));
        $this->subscriber = request()->msisdn ? Subscriber::where('msisdn', request()->msisdn)->firstOrFail() : null;
        $this->subscriberRepository = new SubscriberRepository($this->project, $this->subscriber);
    }

    public function showSubscriber()
    {
        //  Get the MSISDN
        $msisdn = request()->msisdn;

        //  Get the subscriber (if any)
        $subscriber = $this->project->subscribers()->where('msisdn', $msisdn)->with(['activeSubscriptions.subscriptionPlan'])->first();

        // Return JSON response
        return response()->json([
            'exists' =>  !is_null($subscriber),
            'subscriber' => !is_null($subscriber) ? new SubscriberResource($subscriber) : null
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
        $subscriber = $this->subscriberRepository->createProjectSubscriber($msisdn, $metadata);

        // Return JSON response
        return response()->json([
            'message' => 'Created successfully',
            'subscriber' => new SubscriberResource($subscriber)
        ], 201);
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

        // Return JSON response
        return response()->json([
            'message' => 'Updated successfully',
            'subscriber' => new SubscriberResource($this->subscriber->fresh())
        ]);
    }

    public function deleteSubscriber()
    {
        // Delete subscriber using the repository
        $this->subscriberRepository->deleteProjectSubscriber();

        // Return JSON response
        return response()->json([
            'message' => 'Deleted successfully'
        ]);
    }
}
