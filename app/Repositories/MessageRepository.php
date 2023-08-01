<?php

namespace App\Repositories;

use App\Models\Project;
use App\Models\Message;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MessageRepository
{
    /**
     * @var Project
     */
    protected $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    /**
     *  Count the total number of messages for the project.
     *
     *  @return int
     */
    public function countProjectMessages(): int
    {
        return $this->project->messages()->count();
    }

    /**
     *  Show the project messages
     *
     *  @param array $relationships
     *  @param array $countableRelationships
     *  @return LengthAwarePaginator
     */
    public function getProjectMessages($relationships = [], $countableRelationships = []): LengthAwarePaginator
    {
        return $this->project->messages()->with($relationships)->withCount($countableRelationships)->latest()->paginate();
    }

    /**
     *  Create a project message
     *
     *  @param string $msisdn
     *  @return Message
     */
    public function createProjectMessage(string $msisdn): Message
    {
        //  Create message
        return Message::create([
            'msisdn' => $msisdn,
            'project_id' => $this->project->id
        ]);
    }

    /**
     * Update a project message
     *
     * @param string $msisdn
     * @param Message $message
     * @return bool
     * @throws ModelNotFoundException
     * @throws \Exception
     */
    public function updateProjectMessage(string $msisdn, Message $message): bool
    {
        //  Make sure the message belongs to the project
        if ($message->project_id !== $this->project->id) {
            throw new ModelNotFoundException();
        }

        //  Update message
        $message->msisdn = $msisdn;
        return $message->save();
    }

    /**
     * Delete a project message
     *
     * @param Message $message
     * @return bool|null
     * @throws ModelNotFoundException
     * @throws \Exception
     */
    public function deleteProjectMessage(Message $message): bool|null
    {
        //  Make sure the message belongs to the project
        if ($message->project_id !== $this->project->id) {
            throw new ModelNotFoundException();
        }

        //  Delete message
        return $message->delete();
    }
}
