<?php

namespace App\Http\Controllers;

use \Carbon\Carbon;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    public function index()
    {
        /**
         *  @var User $user
         */
        $user = auth()->user();

        //  Get the user projects
        $projectsPayload = $user->projectsAsTeamMember()->paginate(10);

        //  Render the projects view
        return Inertia::render('Projects/List/Main', [
            'projectsPayload' => $projectsPayload
        ]);
    }

    public function create(Request $request)
    {
        //  Validate the request inputs
        Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:3', 'max:500']
        ])->validate();

        //  Set name
        $name = $request->input('name');

        //  Set description
        $description = $request->input('description');

        //  Project settings
        $settings = [
            //  The sender name must be strictly 20 characters or less
            'sms_sender_name' => strlen($name) <= 20 ? $name : '',
            'sms_sender_number' => '',
            'sms_client_credentials' => ''
        ];

        //  Create new project
        $project = Project::create([
            'name' => $name,
            'settings' => $settings,
            'description' => $description,
        ]);

        //  Add user to project
        DB::table('user_projects')->insert([
            'permissions' => json_encode(['*']),
            'project_id' => $project->id,
            'user_id' => Auth::user()->id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return redirect()->back()->with('message', 'Created Successfully');
    }

    public function show(Request $request, Project $project)
    {
        //  Render the project view
        return Inertia::render('Projects/Show/Main', [
            'project' => $project
        ]);
    }

    public function update(Request $request, Project $project)
    {
        //  Validate the request inputs
        Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:3', 'max:500'],
            'can_send_messages' => ['required', 'boolean'],
            'settings.sms_sender_name' => ['string', 'max:20'],
            'settings.sms_client_credentials' => ['string', 'max:255'],
            'settings.sms_sender_number' => ['bail', 'required', 'string', 'starts_with:'.config('app.SMS_NUMBER_EXTENSION', '267'), 'regex:/^[0-9]+$/', 'size:11'],
        ])->validate();

        //  Set name
        $name = $request->input('name');

        //  Set description
        $description = $request->input('description');

        //  Set can send messages
        $canSendMessages = $request->input('can_send_messages');

        //  Set settings
        $settings = $request->filled('settings') ? $request->input('settings') : $project->settings;

        //  Update project
        $project->update([
            'name' => $name,
            'settings' => $settings,
            'description' => $description,
            'can_send_messages' => $canSendMessages
        ]);

        return redirect()->back()->with('message', 'Updated Successfully');
    }

    public function delete(Request $request, Project $project)
    {
        //  Delete project
        $project->delete();

        return redirect()->back()->with('message', 'Deleted Successfully');
    }
}
