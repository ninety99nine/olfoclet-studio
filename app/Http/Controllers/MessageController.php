<?php

namespace App\Http\Controllers;

use App\Enums\MessageType;
use Inertia\Inertia;
use App\Models\Message;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    public function showMessages(Project $project)
    {
        $messagesPayload = $project->messages()->whereIsRoot()->latest()->paginate();

        return Inertia::render('Messages/List/Main', [
            'parentMessage' => null,
            'breadcrumbs' => [],
            'messagesPayload' => $messagesPayload
        ]);
    }

    public function showMessage(Project $project, Message $message)
    {
        $messagesPayload = $message->children()->latest()->paginate();

        $breadcrumbs = collect($message->ancestorsAndSelf($message->id))->map(fn($message) => collect($message)->only(['id', 'content']))->toArray();

        return Inertia::render('Messages/List/Main', [
            'parentMessage' => $message,
            'breadcrumbs' => $breadcrumbs,
            'messagesPayload' => $messagesPayload
        ]);

    }

    public function createMessage(Request $request, Project $project)
    {
        //  Validate the request inputs
        Validator::make($request->all(), [
            'content' => ['nullable', 'string', 'min:5', 'max:500'],
            'parent_id' => ['nullable', 'integer', 'min:1', 'exists:messages,id']
        ])->validate();

        //  Set content
        $content = $request->input('content');

        //  Set parent message id
        $parent_id = $request->input('parent_id');

        //  Create new message
        $message = Message::create([
            'content' => $content,
            'project_id' => $project->id
        ]);

        if( $parentTopic = Message::find($parent_id) ) {

            $parentTopic->prependNode($message);

        }

        return redirect()->back()->with('message', 'Created Successfully');
    }

    public function updateMessage(Request $request, Project $project, Message $message)
    {
        Validator::make($request->all(), [
            'content' => ['nullable', 'string', 'min:5', 'max:500'],
            'parent_id' => ['nullable', 'integer', 'min:1', 'exists:messages,id']
        ])->validate();

        //  Set content
        $content = $request->input('content');

        //  Set parent message id
        $parent_id = $request->input('parent_id');

        //  Update message
        $message->update([
            'content' => $content,
            'project_id' => $project->id
        ]);

        if( $parentTopic = Message::find($parent_id) ) {

            $parentTopic->prependNode($message);

        }

        return redirect()->back()->with('message', 'Updated Successfully');
    }

    public function deleteMessage(Project $project, $message_id)
    {
        //  Delete message
        Message::findOrFail($message_id)->delete();

        return redirect()->back()->with('message', 'Deleted Successfully');
    }
}
