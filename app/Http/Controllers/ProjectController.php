<?php

namespace App\Http\Controllers;

use \Carbon\Carbon;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
            'website_url' => ['sometimes', 'nullable', 'url:http,https', 'max:255'],
            'can_send_messages' => ['sometimes', 'boolean'],
            'settings.sms_sender_name' => ['sometimes', 'nullable', 'string', 'max:11'],
            'settings.sms_client_credentials' => ['sometimes', 'nullable', 'string', 'max:255'],
            'settings.sms_sender_number' => ['sometimes', 'nullable', 'string', 'starts_with:'.config('app.SMS_NUMBER_EXTENSION', '267'), 'regex:/^[0-9]+$/', 'size:11'],
            'settings.auto_billing_client_id' => ['sometimes', 'nullable', 'string', 'max:255'],
            'settings.auto_billing_client_secret' => ['sometimes', 'nullable', 'string', 'max:255'],
        ])->validate();

        //  Set name
        $name = $request->input('name');

        //  Set website url
        $websiteUrl = $request->input('website_url');

        //  Set description
        $description = $request->input('description');

        //  Set can auto bill
        $canAutoBill = $request->input('can_auto_bill');

        //  Set can send messages
        $canSendMessages = $request->input('can_send_messages');

        //  Set settings
        $settings = $request->input('settings');

        // Handle file upload
        if ($request->hasFile('pdf')) {
            $pdfPath = $request->file('pdf')->store('pdf_files', 'public_uploads');
        } else {
            $pdfPath = null;
        }

        //  Create new project
        $project = Project::create([
            'name' => $name,
            'pdf_path' => $pdfPath,
            'settings' => $settings,
            'website_url' => $websiteUrl,
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

    public function updateProject(Request $request, Project $project)
    {
        //  Validate the request inputs
        Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:3', 'max:500'],
            'website_url' => ['sometimes', 'nullable', 'url:http,https', 'max:255'],
            'can_send_messages' => ['sometimes', 'boolean'],
            'settings.sms_sender_name' => ['sometimes', 'string', 'max:11'],
            'settings.sms_client_credentials' => ['sometimes', 'string', 'max:255'],
            'settings.sms_sender_number' => ['sometimes', 'string', 'starts_with:'.config('app.SMS_NUMBER_EXTENSION', '267'), 'regex:/^[0-9]+$/', 'size:11'],
            'settings.auto_billing_client_id' => ['string', 'max:255'],
            'settings.auto_billing_client_secret' => ['string', 'max:255'],
        ])->validate();

        //  Set name
        $name = $request->input('name');

        //  Set website url
        $websiteUrl = $request->input('website_url');

        //  Set description
        $description = $request->input('description');

        //  Set can auto bill
        $canAutoBill = $request->input('can_auto_bill');

        //  Set can send messages
        $canSendMessages = $request->input('can_send_messages');

        //  Set settings
        $settings = $request->input('settings');

        // Handle file upload
        if ($request->hasFile('pdf')) {

            //  If the project has reference to an existing PDF file
            if(!empty($project->pdf_path)) {

                //  Check if the PDF file exists in storage
                if( Storage::disk('public_uploads')->exists($project->getRawOriginal('pdf_path')) ) {

                    // Delete the existing PDF file
                    Storage::disk('public_uploads')->delete($project->getRawOriginal('pdf_path'));

                }

            }

            //  Get the original PDF name
            $originalFileName = $request->file('pdf')->getClientOriginalName();

            //  If the original PDF name is already taken
            if( Storage::disk('public_uploads')->exists("pdf_files/$originalFileName") ) {

                //  Store the new PDF file using a unique name
                $pdfPath = $request->file('pdf')->store('pdf_files', 'public_uploads');

            }else{

                //  Store the new PDF file using the original PDF name
                $pdfPath = $request->file('pdf')->storeAs('pdf_files', $originalFileName, 'public_uploads');

            }

        } else {

            // If we want to remove the existing PDF File
            if(!empty($project->pdf_path) && empty($request->input('pdf_path'))) {

                //  Check if the PDF file exists in storage
                if(Storage::disk('public_uploads')->exists($project->getRawOriginal('pdf_path'))) {

                    // Delete the existing PDF file
                    Storage::disk('public_uploads')->delete($project->getRawOriginal('pdf_path'));

                }

                //  Reset the $pdfPath
                $pdfPath = null;

            } else {

                //  Set the existing PDF file
                $pdfPath = $project->pdf_path;

            }

        }

        //  Update project
        $project->update([
            'name' => $name,
            'pdf_path' => $pdfPath,
            'settings' => $settings,
            'website_url' => $websiteUrl,
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

        return redirect()->route('show.projects')->with('message', 'Deleted Successfully');
    }

    public function showProjectAbout(Request $request, Project $project)
    {
        //  Render the project wiki view
        return Inertia::render('Projects/Show/About/Main', [
            'project' => $project
        ]);
    }
}
