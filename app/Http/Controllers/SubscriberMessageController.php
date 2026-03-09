<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Project;
use App\Models\Pivots\SubscriberMessage;
use App\Repositories\SubscriberMessageRepository;
use App\Http\Requests\SubscriberMessages\ListSubscriberMessagesRequest;

class SubscriberMessageController extends Controller
{
    protected $project;
    protected $subscriberMessage;
    protected $subscriberMessageRepository;

    public function __construct()
    {
        $this->project = Project::findOrFail(request()->route('project'));
        $this->subscriberMessage = request()->route('subscriber_message')
            ? SubscriberMessage::where('project_id', $this->project->id)->findOrFail(request()->route('subscriber_message'))->load(['subscriber', 'message'])
            : null;

        $this->subscriberMessageRepository = new SubscriberMessageRepository($this->project, $this->subscriberMessage);
    }

    /**
     * Build filters array from validated request (for JSON list).
     *
     * @param array<string, mixed> $validated
     * @return array<string, mixed>
     */
    private function buildFilters(array $validated): array
    {
        return [
            'msisdn' => $validated['msisdn'] ?? null,
            'status' => $validated['status'] ?? null,
            'type' => $validated['type'] ?? null,
            'date_from' => $validated['date_from'] ?? null,
            'date_to' => $validated['date_to'] ?? null,
            'per_page' => $validated['per_page'] ?? null,
            'sort' => $validated['sort'] ?? null,
        ];
    }

    public function showSubscriberMessages(Request $request)
    {
        // JSON request (e.g. from Axios): validate and return paginated list
        if ($request->expectsJson()) {
            $validated = $request->validate((new ListSubscriberMessagesRequest())->rules());
            $filters = $this->buildFilters($validated);
            $subscriberMessages = $this->subscriberMessageRepository->getProjectSubscriberMessages(
                $filters,
                ['subscriber:id,msisdn,project_id', 'message'],
                []
            );

            return response()->json([
                'subscriberMessagesPayload' => $subscriberMessages,
            ]);
        }

        // Inertia: render shell only; frontend fetches list via Axios
        return Inertia::render('SubscriberMessages/List/Main', []);
    }

    public function showSubscriberMessage()
    {
        if ($this->subscriberMessage === null) {
            abort(404, 'Subscriber message not found.');
        }

        return Inertia::render('SubscriberMessages/Show/Main', [
            'subscriberMessage' => $this->subscriberMessage,
        ]);
    }
}
