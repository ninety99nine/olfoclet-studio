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
    public function showProjects()
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

    public function createProject(Request $request)
    {
        //  Validate the request inputs
        Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:3', 'max:500'],
            'about_url' => ['sometimes', 'nullable', 'url:http,https', 'max:255'],
            'can_send_messages' => ['sometimes', 'boolean'],
            'settings.sms_sender_name' => ['sometimes', 'nullable', 'string', 'max:11'],
            'settings.sms_client_credentials' => ['sometimes', 'nullable', 'string', 'max:255'],
            'settings.sms_sender_number' => ['sometimes', 'nullable', 'string', 'starts_with:'.config('app.SMS_NUMBER_EXTENSION', '267'), 'regex:/^[0-9]+$/', 'size:11'],
            'settings.auto_billing_client_id' => ['sometimes', 'nullable', 'string', 'max:255'],
            'settings.auto_billing_client_secret' => ['sometimes', 'nullable', 'string', 'max:255'],
        ])->validate();

        //  Set name
        $name = $request->input('name');

        //  Set about url
        $aboutUrl = $request->input('about_url');

        //  Set description
        $description = $request->input('description');

        //  Set can auto bill
        $canAutoBill = $request->input('can_auto_bill');

        //  Set can send messages
        $canSendMessages = $request->input('can_send_messages');

        //  Set settings
        $settings = $request->input('settings');

        //  Create new project
        $project = Project::create([
            'name' => $name,
            'settings' => $settings,
            'about_url' => $aboutUrl,
            'description' => $description,
            'can_auto_bill' => $canAutoBill,
            'can_send_messages' => $canSendMessages
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

    public function showProject(Request $request, Project $project)
    {
        //  Render the project view
        return Inertia::render('Projects/Show/Main', [
            'project' => $project
        ]);
    }

    public function updateProject(Request $request, Project $project)
    {
        //  Validate the request inputs
        Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:3', 'max:500'],
            'about_url' => ['sometimes', 'nullable', 'url:http,https', 'max:255'],
            'can_send_messages' => ['sometimes', 'boolean'],
            'settings.sms_sender_name' => ['sometimes', 'string', 'max:11'],
            'settings.sms_client_credentials' => ['sometimes', 'string', 'max:255'],
            'settings.sms_sender_number' => ['sometimes', 'string', 'starts_with:'.config('app.SMS_NUMBER_EXTENSION', '267'), 'regex:/^[0-9]+$/', 'size:11'],
            'settings.auto_billing_client_id' => ['string', 'max:255'],
            'settings.auto_billing_client_secret' => ['string', 'max:255'],
        ])->validate();

        //  Set name
        $name = $request->input('name');

        //  Set about url
        $aboutUrl = $request->input('about_url');

        //  Set description
        $description = $request->input('description');

        //  Set can auto bill
        $canAutoBill = $request->input('can_auto_bill');

        //  Set can send messages
        $canSendMessages = $request->input('can_send_messages');

        //  Set settings
        $settings = $request->filled('settings') ? $request->input('settings') : $project->settings;

        //  Update project
        $project->update([
            'name' => $name,
            'settings' => $settings,
            'about_url' => $aboutUrl,
            'description' => $description,
            'can_auto_bill' => $canAutoBill,
            'can_send_messages' => $canSendMessages
        ]);

        return redirect()->back()->with('message', 'Updated Successfully');
    }

    public function deleteProject(Request $request, Project $project)
    {
        //  Delete project
        $project->delete();

        return redirect()->back()->with('message', 'Deleted Successfully');
    }

    public function showProjectAbout(Request $request, Project $project)
    {
        //  Render the project wiki view
        return Inertia::render('Projects/Show/About/Main', [
            'project' => $project
        ]);
    }
}
