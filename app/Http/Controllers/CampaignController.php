<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Project;
use App\Models\Campaign;
use App\Models\JobBatches;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class CampaignController extends Controller
{
    public function index(Project $project)
    {
        $scheduleTypeOptions = Campaign::SCHEDULE_TYPE;
        $contentToSendOptions = Campaign::MESSAGE_TO_SEND;

        //  Get the subscription plans
        $subscriptionPlans = $project->subscriptionPlans()->get();

        //  Get the campaigns
        $campaignsPayload = $project->campaigns()->with(['subscriptionPlans:id', 'latestCampaignBatchJob' => function($query) {

            //  Seleted columns
            $selectedColumns = collect(Schema::getColumnListing('job_batches'))->reject(function ($name) {

                //  Exclude the following columns
                return in_array($name, ['options', 'failed_job_ids']);

            })->map(function ($name) {

                /**
                 *  Append the table name to each column to avoid clashing of ambiguous fields
                 *  e.g id, created_at, e.t.c
                 */
                return 'job_batches.'.$name;

            })->all();

            //  Limit the loaded message to the message id and sent sms count to consume less memory
            return $query->select(...$selectedColumns);

        }])->withCount('campaignBatchJobs')->latest()->paginate(10);

        //  Render the campaigns view
        return Inertia::render('Campaigns/List/Main', [
            'contentToSendOptions' => $contentToSendOptions,
            'scheduleTypeOptions' => $scheduleTypeOptions,
            'subscriptionPlans' => $subscriptionPlans,
            'campaignsPayload' => $campaignsPayload,
            'projectPayload' => $project
        ]);
    }

    public function create(Request $request, Project $project)
    {
        $scheduleType = $request->input('schedule_type');
        $isSendingRecurring = $scheduleType == 'Send Recurring';
        $isSendingLater = $scheduleType == 'Send Later';
        $isSendingNow = $scheduleType == 'Send Now';

        /**
         *  Check whether or not the combination of the start date and start time
         *  produce a datetime that is in the future otherwise throw a validation
         *  error. The same validation rule is used for the "start_date" and the
         *  "start_time" fields.
         */
        $startDateTimeValidation = function ($attribute, $value, $fail) use ($isSendingLater, $isSendingRecurring) {

            /**
             *  These values will always be provided since we specify the
             *  "required" validation before this validation is evaluated.
             */
            $startDate = request()->input('start_date');
            $startTime = request()->input('start_time');
            $startDateTime = (new Campaign)->combineDateAndTime($startDate, $startTime);

            /**
             *  If we are sending later, then the start date
             *  and time must always be in the future
             */
            if($isSendingLater && $startDateTime->isFuture()) {

                return;

            }elseif($isSendingRecurring) {

                $endDate = request()->input('end_date');
                $endTime = request()->input('end_time');
                $endDateTime = (new Campaign)->combineDateAndTime($endDate, $endTime);

                /**
                 *  If we are sending recurring, then the start date and time
                 *  must always be before the end date and time
                 */
                if($startDateTime->lessThan($endDateTime)) {

                    return;

                }

            }

            if($isSendingLater) {

                if( $attribute == 'start_date' ) {

                    $fail('Must be a date in the future');

                }else{

                    $fail('Must be a time in the future');

                }

            }elseif($isSendingRecurring) {

                if( $attribute == 'start_date' ) {

                    $fail('Must be a date before the end date');

                }else{

                    $fail('Must be a time before the end time');

                }

            }

        };

        /**
         *  Check whether or not the combination of the end date and end time
         *  produce a datetime that is in the future of the given start date
         *  and start time otherwise throw a validation error. The same
         *  validation rule is used for the "end_date" and the
         *  "end_time" fields.
         */
        $endDateTimeValidation = function ($attribute, $value, $fail) use ($isSendingRecurring) {

            /**
             *  These values will always be provided since we specify the
             *  "required" validation before this validation is evaluated.
             */
            $startDate = request()->input('start_date');
            $startTime = request()->input('start_time');
            $endDate = request()->input('end_date');
            $endTime = request()->input('end_time');

            $startDateTime = (new Campaign)->combineDateAndTime($startDate, $startTime);
            $endDateTime = (new Campaign)->combineDateAndTime($endDate, $endTime);

            /**
             *  If we are sending recurring, then the end date and time
             *  must always be after the start date and time
             */
            $case1 = $isSendingRecurring && $endDateTime->greaterThan($startDateTime);

            if($case1) {

                return;

            }

            if( $attribute == 'end_date' ) {

                $fail('Must be a date after the start date');

            }else{

                $fail('Must be a time after the start time');

            }

        };

        //  Validate the request inputs
        $data = Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:3', 'max:50', Rule::unique('campaigns')->where('project_id', $project->id)],
            'description' => ['nullable', 'string', 'min:10', 'max:500'],
            'active' => ['required', 'boolean'],
            'can_send_messages' => ['required', 'boolean'],
            'schedule_type' => ['required', 'string', Rule::in(Campaign::SCHEDULE_TYPE)],
            'recurring_duration' => $isSendingNow || $isSendingLater ? [] : [
                'required', 'integer', 'numeric', 'min:1'
            ],
            'recurring_frequency' => $isSendingNow || $isSendingLater ? [] : [
                Rule::in(['Seconds', 'Minutes', 'Hours', 'Days', 'Weeks', 'Months', 'Years'])
            ],
            'start_date' => $isSendingNow ? ['exclude'] : [
                'bail', 'required', 'date', $startDateTimeValidation
            ],
            'start_time' => $isSendingNow ? ['exclude'] : [
                'bail', 'required', 'string'
            ],
            'end_date' => $isSendingNow || $isSendingLater ? ['exclude'] : [
                'bail', 'required', 'date', $endDateTimeValidation
            ],
            'end_time' => $isSendingNow || $isSendingLater ? ['exclude'] : [
                'bail', 'required', 'string'
            ],
            'days_of_the_week' => $isSendingNow || $isSendingLater ? ['exclude'] : ['required'],
            'subcription_plan_ids' => ['sometimes', 'array'],
            'message_to_send' => ['required', 'string'],
            'message_ids' => ['required', 'array'],
        ],
        [
            //  Custom messages
        ],
        [
            //  Custom attribute names
            'end_date' => 'end date',
            'end_time' => 'end time',
            'message_ids' => 'messages',
            'start_date' => 'start date',
            'start_time' => 'start time',
        ])->validate();

        $data = array_merge($data, [
            'project_id' => $request->project->id
        ]);

        //  Create new campaign
        $campaign = Campaign::create($data);

        if( count( $request->input('subcription_plan_ids') ?? [] ) ) {

            //  Set subcription plan ids
            $subcription_plan_ids = $request->input('subcription_plan_ids');

            //  Sync the subscription plans
            $campaign->subscriptionPlans()->syncWithPivotValues($subcription_plan_ids, ['project_id' => $project->id]);

        }

        return redirect()->back()->with('message', 'Created Successfully');
    }

    public function update(Request $request, Project $project, Campaign $campaign)
    {
        $scheduleType = $request->input('schedule_type');
        $isSendingRecurring = $scheduleType == 'Send Recurring';
        $isSendingLater = $scheduleType == 'Send Later';
        $isSendingNow = $scheduleType == 'Send Now';

        /**
         *  Check whether or not the combination of the start date and start time
         *  produce a datetime that is in the future otherwise throw a validation
         *  error. The same validation rule is used for the "start_date" and the
         *  "start_time" fields.
         */
        $startDateTimeValidation = function ($attribute, $value, $fail) use ($isSendingLater, $isSendingRecurring) {

            /**
             *  These values will always be provided since we specify the
             *  "required" validation before this validation is evaluated.
             */
            $startDate = request()->input('start_date');
            $startTime = request()->input('start_time');
            $startDateTime = (new Campaign)->combineDateAndTime($startDate, $startTime);

            /**
             *  If we are sending later, then the start date
             *  and time must always be in the future
             */
            if($isSendingLater && $startDateTime->isFuture()) {

                return;

            }elseif($isSendingRecurring) {

                $endDate = request()->input('end_date');
                $endTime = request()->input('end_time');
                $endDateTime = (new Campaign)->combineDateAndTime($endDate, $endTime);

                /**
                 *  If we are sending recurring, then the start date and time
                 *  must always be before the end date and time
                 */
                if($startDateTime->lessThan($endDateTime)) {

                    return;

                }

            }

            if($isSendingLater) {

                if( $attribute == 'start_date' ) {

                    $fail('Must be a date in the future');

                }else{

                    $fail('Must be a time in the future');

                }

            }elseif($isSendingRecurring) {

                if( $attribute == 'start_date' ) {

                    $fail('Must be a date before the end date');

                }else{

                    $fail('Must be a time before the end time');

                }

            }

        };

        /**
         *  Check whether or not the combination of the end date and end time
         *  produce a datetime that is in the future of the given start date
         *  and start time otherwise throw a validation error. The same
         *  validation rule is used for the "end_date" and the
         *  "end_time" fields.
         */
        $endDateTimeValidation = function ($attribute, $value, $fail) use ($isSendingRecurring) {

            /**
             *  These values will always be provided since we specify the
             *  "required" validation before this validation is evaluated.
             */
            $startDate = request()->input('start_date');
            $startTime = request()->input('start_time');
            $endDate = request()->input('end_date');
            $endTime = request()->input('end_time');

            $startDateTime = (new Campaign)->combineDateAndTime($startDate, $startTime);
            $endDateTime = (new Campaign)->combineDateAndTime($endDate, $endTime);

            /**
             *  If we are sending recurring, then the end date and time
             *  must always be after the start date and time
             */
            $case1 = $isSendingRecurring && $endDateTime->greaterThan($startDateTime);

            if($case1) {

                return;

            }

            if( $attribute == 'end_date' ) {

                $fail('Must be a date after the start date');

            }else{

                $fail('Must be a time after the start time');

            }

        };

        //  Validate the request inputs
        $data = Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:3', 'max:50', Rule::unique('campaigns')->where('project_id', $project->id)->ignore($campaign->id)],
            'description' => ['nullable', 'string', 'min:10', 'max:500'],
            'active' => ['required', 'boolean'],
            'can_send_messages' => ['required', 'boolean'],
            'schedule_type' => ['required', 'string', Rule::in(Campaign::SCHEDULE_TYPE)],
            'recurring_duration' => $isSendingNow || $isSendingLater ? [] : [
                'required', 'integer', 'numeric', 'min:1'
            ],
            'recurring_frequency' => $isSendingNow || $isSendingLater ? [] : [
                Rule::in(['Seconds', 'Minutes', 'Hours', 'Days', 'Weeks', 'Months', 'Years'])
            ],
            'start_date' => $isSendingNow ? ['exclude'] : [
                'bail', 'required', 'date', $startDateTimeValidation
            ],
            'start_time' => $isSendingNow ? ['exclude'] : [
                'bail', 'required', 'string'
            ],
            'end_date' => $isSendingNow || $isSendingLater ? ['exclude'] : [
                'bail', 'required', 'date', $endDateTimeValidation
            ],
            'end_time' => $isSendingNow || $isSendingLater ? ['exclude'] : [
                'bail', 'required', 'string'
            ],
            'days_of_the_week' => $isSendingNow || $isSendingLater ? ['exclude'] : ['required'],
            'subcription_plan_ids' => ['sometimes', 'array'],
            'message_to_send' => ['required', 'string'],
            'message_ids' => ['required', 'array'],
        ],
        [
            //  Custom messages
        ],
        [
            //  Custom attribute names
            'end_date' => 'end date',
            'end_time' => 'end time',
            'message_ids' => 'messages',
            'start_date' => 'start date',
            'start_time' => 'start time',
        ])->validate();

        $data = array_merge($data, [
            'project_id' => $request->project->id
        ]);

        //  Update campaign
        $campaign->update($data);

        //  If the campaign is sending recurring sms messages
        if( $campaign->schedule_type == 'Send Recurring' ) {

            /**
             *  Recalculate the next message date and times of the subscribers.
             *  This is so that the suggested date is insync with the current
             *  recurring schedule settings.
             */
            DB::table('campaign_subscriber')
                ->where('campaign_id', $campaign->id)
                ->whereNotNull('next_message_date')
                ->update([
                    'next_message_date' => $campaign->nextCampaignSmsMessageDate(),
                    'updated_at' => Carbon::now(),
                ]);

        }

        if( count( $request->input('subcription_plan_ids') ?? [] ) ) {

            //  Set subcription plan ids
            $subcription_plan_ids = $request->input('subcription_plan_ids');

            //  Sync the subscription plans
            $campaign->subscriptionPlans()->syncWithPivotValues($subcription_plan_ids, ['project_id' => $project->id]);

        }

        return redirect()->back()->with('message', 'Updated Successfully');
    }

    public function delete(Project $project, Campaign $campaign)
    {
        //  Delete campaign
        $campaign->delete();

        return redirect()->back()->with('message', 'Deleted Successfully');
    }

    public function jobBatches(Project $project, Campaign $campaign)
    {
        //  Get the campaign job batches
        $campaignBatchJobsPayload = $campaign->campaignBatchJobs()->latest()->paginate(10);

        //  Render the campaigns view
        return Inertia::render('Campaigns/List/JobBatches/List/Main', [
            'campaign' => $campaign,
            'campaignBatchJobsPayload' => $campaignBatchJobsPayload
        ]);
    }
}
