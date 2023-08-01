<?php

namespace App\Repositories;

use App\Models\Project;
use App\Models\Subscriber;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SubscriberRepository
{
    /**
     *  @var Project The project instance associated with the repository.
     */
    protected Project $project;

    /**
     *  @var Subscriber|null The subscriber instance associated with the repository.
     */
    protected ?Subscriber $subscriber;

    /**
     *  Create a new SubscriberRepository instance.
     *
     *  @param Project $project The project instance to associate with the repository.
     *  @param Subscriber|null $subscriber The subscriber instance to associate with the repository (optional).
     */
    public function __construct(Project $project, ?Subscriber $subscriber)
    {
        $this->project = $project;

        $this->subscriber = $subscriber;
    }

    /**
     *  Find or create a subscriber with the given MSISDN for the associated project.
     *
     *  If the subscriber with the given MSISDN does not exist for the project,
     *  a new subscriber will be created and returned.
     *
     *  @param string $msisdn The MSISDN (Mobile Subscriber Integrated Services Digital Network Number).
     *  @return Subscriber The found or newly created subscriber instance.
     */
    public function findOrCreateSubscriber(string $msisdn): Subscriber
    {
        //  Get the subscriber if they exist
        $subscriber = $this->project->subscribers()->where('msisdn', $msisdn)->first();

        //  If the subscriber does not exist
        if (!$subscriber) {

            // Create a new subscriber
            $subscriber = $this->createProjectSubscriber($msisdn);

        }

        //  Return the existing or new subscriber
        return $subscriber;
    }

    /**
     *  Count the total number of subscribers for the associated project.
     *
     *  @return int The total number of subscribers.
     */
    public function countProjectSubscribers(): int
    {
        return $this->project->subscribers()->count();
    }

    /**
     *  Show the project subscribers with optional relationships and pagination.
     *
     *  @param array $relationships The relationships to eager load on the subscribers.
     *  @param array $countableRelationships The relationships to count on the subscribers.
     *  @return LengthAwarePaginator The paginated list of project subscribers.
     */
    public function getProjectSubscribers($relationships = [], $countableRelationships = []): LengthAwarePaginator
    {
        return $this->project->subscribers()->with($relationships)->withCount($countableRelationships)->latest()->paginate();
    }

    /**
     *  Create a new project subscriber with the given MSISDN.
     *
     *  @param string $msisdn The MSISDN (Mobile Subscriber Integrated Services Digital Network Number).
     *  @return Subscriber The newly created subscriber instance.
     */
    public function createProjectSubscriber(string $msisdn): Subscriber
    {
        //  Create subscriber
        return Subscriber::create([
            'msisdn' => $msisdn,
            'project_id' => $this->project->id
        ]);
    }

    /**
     *  Update the MSISDN of the associated project subscriber.
     *
     *  @param string $msisdn The new MSISDN for the subscriber.
     *  @return bool True if the update is successful, false otherwise.
     *  @throws ModelNotFoundException If the associated subscriber is not found or does not belong to the project.
     *  @throws \Exception If an error occurs during the update process.
     */
    public function updateProjectSubscriber(string $msisdn): bool
    {
        // Make sure the subscriber exists and belongs to the project
        if ($this->subscriber === null || $this->subscriber->project_id !== $this->project->id) {
            throw new ModelNotFoundException();
        }

        //  Update subscriber
        $this->subscriber->msisdn = $msisdn;
        return $this->subscriber->save();
    }

    /**
     *  Delete the associated project subscriber.
     *
     *  @return bool|null True if the deletion is successful, false if the subscriber is not found.
     *  @throws ModelNotFoundException If the associated subscriber is not found or does not belong to the project.
     *  @throws \Exception If an error occurs during the deletion process.
     */
    public function deleteProjectSubscriber(): bool|null
    {
        // Make sure the subscriber exists and belongs to the project
        if ($this->subscriber === null || $this->subscriber->project_id !== $this->project->id) {
            throw new ModelNotFoundException();
        }

        //  Delete subscriber
        return $this->subscriber->delete();
    }
}
