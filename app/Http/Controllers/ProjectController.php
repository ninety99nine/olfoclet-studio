<?php

namespace App\Http\Controllers;

use \Carbon\Carbon;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
        $requiredIfCanAutoBill = Rule::requiredIf(in_array(request()->input('can_auto_bill'), [true, 1]));
        $requiredIfCanSendMessages = Rule::requiredIf(in_array(request()->input('can_send_messages'), [true, 1]));
        $requiredIfCanCreateBillingReports = Rule::requiredIf(in_array(request()->input('can_create_billing_reports'), [true, 1]));

        $sharePercentageValidation = function ($attribute, $value, $fail) {

            $ourSharePercentage = (float) request()->input('our_share_percentage');
            $theirSharePercentage = (float) request()->input('their_share_percentage');

            $percentage = $ourSharePercentage + $theirSharePercentage;

            if( $percentage > 100 ) {

                $fail('The share percentages combined exceed 100%');

            }else if ( $percentage < 0 ) {

                $fail('The share percentages combined are less than 0%');

            }

        };

        $costsValidation = function ($attribute, $value, $fail) {

            $costs = request()->input('costs');
            $percentage = collect($costs)->sum('percentage');

            if( $percentage > 100 ) {

                $fail('The cost percentages combined exceed 100%');

            }else if ( $percentage < 0 ) {

                $fail('The cost percentages combined are less than 0%');

            }

        };

        //  Validate the request inputs
        Validator::make($request->all(), [
            'can_auto_bill' => ['required', 'boolean'],
            'can_send_messages' => ['required', 'boolean'],
            'costs.*.name' => ['string', 'min:1', 'max:40'],
            'costs.*.percentage' => ['integer', 'min:1', 'max:100'],
            'costs' => [$requiredIfCanCreateBillingReports, 'array', $costsValidation],
            'name' => ['required', 'string', 'min:3', 'max:500'],
            'can_create_billing_reports' => ['required', 'boolean'],
            'billing_report_email_addresses.*' => ['email'],
            'billing_report_email_addresses' => [$requiredIfCanCreateBillingReports, 'array', 'max:2'],
            'our_share_percentage' => [$requiredIfCanCreateBillingReports, 'integer', 'min:1', 'max:100', $sharePercentageValidation],
            'website_url' => ['sometimes', 'nullable', 'url:http,https', 'max:255'],
            'their_share_percentage' => [$requiredIfCanCreateBillingReports, 'integer', 'min:1', 'max:100', $sharePercentageValidation],
            'settings.sms_sender_name' => [$requiredIfCanSendMessages, 'nullable', 'string', 'max:11'],
            'settings.sms_client_credentials' => [$requiredIfCanSendMessages, 'nullable', 'string', 'max:255'],
            'settings.sms_sender_number' => [$requiredIfCanSendMessages, 'nullable', 'string', 'starts_with:'.config('app.SMS_NUMBER_EXTENSION', '267'), 'regex:/^[0-9]+$/', 'size:11'],
            'settings.auto_billing_client_id' => [$requiredIfCanAutoBill, 'nullable', 'string', 'max:255'],
            'settings.auto_billing_client_secret' => [$requiredIfCanAutoBill, 'nullable', 'string', 'max:255'],
            'settings.auto_billing_on_behalf_of' => [$requiredIfCanAutoBill, 'nullable', 'string', 'max:255'],
        ], [

            //  Messages

        ], [
            //  Attributes
            'costs.*.name' => 'cost name',
            'our_share_percentage' => 'percentage',
            'their_share_percentage' => 'percentage',
            'costs.*.percentage' => 'cost percentage',
            'billing_report_email_addresses.*' => 'email address',

        ])->validate();

        //  Set name
        $name = $request->input('name');

        //  Set costs
        $costs = $request->input('costs');

        //  Set website url
        $websiteUrl = $request->input('website_url');

        //  Set description
        $description = $request->input('description');

        //  Set can auto bill
        $canAutoBill = $request->input('can_auto_bill');

        //  Set can send messages
        $canSendMessages = $request->input('can_send_messages');

        //  Set can create billing reports
        $canCreateBillingReports = $request->input('can_create_billing_reports');

        //  Set our share percentage
        $ourSharePercentage = $request->input('our_share_percentage');

        //  Set their share percentage
        $theirSharePercentage = $request->input('their_share_percentage');

        //  Set settings
        $settings = $request->input('settings');

        //  Create new project
        $project = Project::create([
            'name' => $name,
            'costs' => $costs,
            'settings' => $settings,
            'website_url' => $websiteUrl,
            'description' => $description,
            'can_auto_bill' => $canAutoBill,
            'can_send_messages' => $canSendMessages,
            'our_share_percentage' => $ourSharePercentage,
            'their_share_percentage' => $theirSharePercentage,
            'can_create_billing_reports' => $canCreateBillingReports,
        ]);

        // Handle file upload
        if ($request->hasFile('pdf')) {

            $pdfPath = $request->file('pdf')->store("$project->id/pdf_files", 'public_uploads');

            $project->update([
                'pdf_path' => $pdfPath
            ]);

        }

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
        $requiredIfCanAutoBill = Rule::requiredIf(in_array(request()->input('can_auto_bill'), [true, 1]));
        $requiredIfCanSendMessages = Rule::requiredIf(in_array(request()->input('can_send_messages'), [true, 1]));
        $requiredIfCanCreateBillingReports = Rule::requiredIf(in_array(request()->input('can_create_billing_reports'), [true, 1]));

        $sharePercentageValidation = function ($attribute, $value, $fail) {

            $ourSharePercentage = (float) request()->input('our_share_percentage');
            $theirSharePercentage = (float) request()->input('their_share_percentage');

            $percentage = $ourSharePercentage + $theirSharePercentage;

            if( $percentage > 100 ) {

                $fail('The share percentages combined exceed 100%');

            }else if ( $percentage < 0 ) {

                $fail('The share percentages combined are less than 0%');

            }

        };

        $costsValidation = function ($attribute, $value, $fail) {

            $costs = request()->input('costs');
            $percentage = collect($costs)->sum('percentage');

            if( $percentage > 100 ) {

                $fail('The cost percentages combined exceed 100%');

            }else if ( $percentage < 0 ) {

                $fail('The cost percentages combined are less than 0%');

            }

        };

        //  Validate the request inputs
        Validator::make($request->all(), [
            'can_auto_bill' => ['required', 'boolean'],
            'can_send_messages' => ['required', 'boolean'],
            'costs.*.name' => ['string', 'min:1', 'max:40'],
            'costs.*.percentage' => ['min:1', 'max:100'],
            'costs' => [$requiredIfCanCreateBillingReports, 'array', $costsValidation],
            'name' => ['required', 'string', 'min:3', 'max:500'],
            'can_create_billing_reports' => ['required', 'boolean'],
            'billing_report_email_addresses.*' => ['email'],
            'billing_report_email_addresses' => [$requiredIfCanCreateBillingReports, 'array', 'max:2'],
            'our_share_percentage' => [$requiredIfCanCreateBillingReports, 'integer', 'min:1', 'max:100', $sharePercentageValidation],
            'website_url' => ['sometimes', 'nullable', 'url:http,https', 'max:255'],
            'their_share_percentage' => [$requiredIfCanCreateBillingReports, 'integer', 'min:1', 'max:100', $sharePercentageValidation],
            'settings.sms_sender_name' => [$requiredIfCanSendMessages, 'nullable', 'string', 'max:11'],
            'settings.sms_client_credentials' => [$requiredIfCanSendMessages, 'nullable', 'string', 'max:255'],
            'settings.sms_sender_number' => [$requiredIfCanSendMessages, 'nullable', 'string', 'starts_with:'.config('app.SMS_NUMBER_EXTENSION', '267'), 'regex:/^[0-9]+$/', 'size:11'],
            'settings.auto_billing_client_id' => [$requiredIfCanAutoBill, 'nullable', 'string', 'max:255'],
            'settings.auto_billing_client_secret' => [$requiredIfCanAutoBill, 'nullable', 'string', 'max:255'],
        ], [

            //  Messages

        ], [
            //  Attributes
            'costs.*.name' => 'cost name',
            'our_share_percentage' => 'percentage',
            'their_share_percentage' => 'percentage',
            'costs.*.percentage' => 'cost percentage',
            'billing_report_email_addresses.*' => 'email address',

        ])->validate();

        //  Set name
        $name = $request->input('name');

        //  Set costs
        $costs = $request->input('costs');

        //  Set website url
        $websiteUrl = $request->input('website_url');

        //  Set description
        $description = $request->input('description');

        //  Set can auto bill
        $canAutoBill = $request->input('can_auto_bill');

        //  Set can send messages
        $canSendMessages = $request->input('can_send_messages');

        //  Set can create billing reports
        $canCreateBillingReports = $request->input('can_create_billing_reports');

        //  Set our share percentage
        $ourSharePercentage = $request->input('our_share_percentage');

        //  Set their share percentage
        $theirSharePercentage = $request->input('their_share_percentage');

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
            if( Storage::disk('public_uploads')->exists("$project->id/pdf_files/$originalFileName") ) {

                //  Store the new PDF file using a unique name
                $pdfPath = $request->file('pdf')->store("$project->id/pdf_files", 'public_uploads');

            }else{

                //  Store the new PDF file using the original PDF name
                $pdfPath = $request->file('pdf')->storeAs("$project->id/pdf_files", $originalFileName, 'public_uploads');

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
            'costs' => $costs,
            'pdf_path' => $pdfPath,
            'settings' => $settings,
            'website_url' => $websiteUrl,
            'description' => $description,
            'can_auto_bill' => $canAutoBill,
            'can_send_messages' => $canSendMessages,
            'our_share_percentage' => $ourSharePercentage,
            'their_share_percentage' => $theirSharePercentage,
            'can_create_billing_reports' => $canCreateBillingReports,
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
