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

        if(request()->routeIs('api.show.subscriber')) {

            $this->subscriber = $this->project->subscribers()->where('msisdn', request()->subscriber_msisdn)->with(['latestSubscription.pricingPlan'])->first();

        }else{

            if(!empty(request()->subscriber_msisdn)) {

                $this->subscriber = $this->project->subscribers()->where('msisdn', request()->subscriber_msisdn)->firstOrFail();

            }

        }

        $this->subscriberRepository = new SubscriberRepository($this->project, $this->subscriber);
    }

    public function showSubscriber()
    {
        // Return JSON response
        return response()->json([
            'exists' =>  !is_null($this->subscriber),
            'subscriber' => !is_null($this->subscriber) ? new SubscriberResource($this->subscriber) : null
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
