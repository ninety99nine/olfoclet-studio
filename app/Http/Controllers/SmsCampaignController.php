<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Project;
use App\Models\SmsCampaign;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class SmsCampaignController extends Controller
{
    public function showSmsCampaigns(Project $project)
    {
        $scheduleTypeOptions = SmsCampaign::SCHEDULE_TYPE;
        $contentToSendOptions = SmsCampaign::MESSAGE_TO_SEND;

        //  Get the subscription plans
        $subscriptionPlans = $project->subscriptionPlans()->get();

        //  Get the SMS campaigns
        $smsCampaignsPayload = $project->smsCampaigns()->with(['subscriptionPlans:id', 'latestSmsCampaignBatchJob' => function($query) {

            //  Seleted columns
            $selectedColumns = collect(Schema::getColumnListing('job_batches'))
                                ->reject(fn ($name) => in_array($name, ['options', 'failed_job_ids']))
                                ->map(fn ($name) => 'job_batches.'.$name)
                                ->all();

            //  Limit the loaded message to the message id and sent sms count to consume less memory
            return $query->select(...$selectedColumns);

        }])->withCount('smsCampaignBatchJobs')->latest()->paginate(10);

        //  Render the campaigns view
        return Inertia::render('SmsCampaigns/List/Main', [
            'contentToSendOptions' => $contentToSendOptions,
            'scheduleTypeOptions' => $scheduleTypeOptions,
            'smsCampaignsPayload' => $smsCampaignsPayload,
            'subscriptionPlans' => $subscriptionPlans,
            'projectPayload' => $project
        ]);
    }

    public function createSmsCampaign(Request $request, Project $project)
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
            $startDateTime = (new SmsCampaign)->combineDateAndTime($startDate, $startTime);

            /**
             *  If we are sending later, then the start date
             *  and time must always be in the future
             */
            if($isSendingLater && $startDateTime->isFuture()) {

                return;

            }elseif($isSendingRecurring) {

                $endDate = request()->input('end_date');
                $endTime = request()->input('end_time');
                $endDateTime = (new SmsCampaign)->combineDateAndTime($endDate, $endTime);

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

            $startDateTime = (new SmsCampaign)->combineDateAndTime($startDate, $startTime);
            $endDateTime = (new SmsCampaign)->combineDateAndTime($endDate, $endTime);

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
            'name' => ['required', 'string', 'min:3', 'max:50', Rule::unique('sms_campaigns')->where('project_id', $project->id)],
            'description' => ['nullable', 'string', 'min:10', 'max:500'],
            'can_send_messages' => ['required', 'boolean'],
            'schedule_type' => ['required', 'string', Rule::in(SmsCampaign::SCHEDULE_TYPE)],
            'recurring_duration' => $isSendingNow || $isSendingLater ? [] : [
                'required', 'integer', 'numeric', 'min:1'
            ],
            'recurring_frequency' => $isSendingNow || $isSendingLater ? [] : [
                Rule::in(['Minutes', 'Hours', 'Days', 'Weeks', 'Months', 'Years'])
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

        //  Create new sms campaign
        $smsCampaign = SmsCampaign::create($data);

        if( count( $request->input('subcription_plan_ids') ?? [] ) ) {

            //  Set subcription plan ids
            $subcription_plan_ids = $request->input('subcription_plan_ids');

            //  Sync the subscription plans
            $smsCampaign->subscriptionPlans()->syncWithPivotValues($subcription_plan_ids, [
                'project_id' => $request->project->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        }

        return redirect()->back()->with('message', 'Created Successfully');
    }

    public function updateSmsCampaign(Request $request, Project $project, SmsCampaign $smsCampaign)
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
            $startDateTime = (new SmsCampaign)->combineDateAndTime($startDate, $startTime);

            /**
             *  If we are sending later, then the start date
             *  and time must always be in the future
             */
            if($isSendingLater && $startDateTime->isFuture()) {

                return;

            }elseif($isSendingRecurring) {

                $endDate = request()->input('end_date');
                $endTime = request()->input('end_time');
                $endDateTime = (new SmsCampaign)->combineDateAndTime($endDate, $endTime);

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

            $startDateTime = (new SmsCampaign)->combineDateAndTime($startDate, $startTime);
            $endDateTime = (new SmsCampaign)->combineDateAndTime($endDate, $endTime);

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
            'name' => ['required', 'string', 'min:3', 'max:50', Rule::unique('sms_campaigns')->where('project_id', $project->id)->ignore($smsCampaign->id)],
            'description' => ['nullable', 'string', 'min:10', 'max:500'],
            'can_send_messages' => ['required', 'boolean'],
            'schedule_type' => ['required', 'string', Rule::in(SmsCampaign::SCHEDULE_TYPE)],
            'recurring_duration' => $isSendingNow || $isSendingLater ? [] : [
                'required', 'integer', 'numeric', 'min:1'
            ],
            'recurring_frequency' => $isSendingNow || $isSendingLater ? [] : [
                Rule::in(['Minutes', 'Hours', 'Days', 'Weeks', 'Months', 'Years'])
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

        //  Update sms campaign
        $smsCampaign->update($data);

        //  If the sms campaign is sending recurring sms messages
        if( $smsCampaign->schedule_type == 'Send Recurring' ) {

            /**
             *  Recalculate the next message date and times of the subscribers.
             *  This is so that the suggested date is in sync with the current
             *  recurring schedule settings.
             */
            DB::table('sms_campaign_subscriber')
                ->where('sms_campaign_id', $smsCampaign->id)
                ->update([
                    'next_message_date' => $smsCampaign->nextSmsCampaignMessageDate(),
                    'updated_at' => Carbon::now(),
                ]);

        }

        if( count( $request->input('subcription_plan_ids') ?? [] ) ) {

            //  Set subcription plan ids
            $subcription_plan_ids = $request->input('subcription_plan_ids');

            //  Sync the subscription plans
            $smsCampaign->subscriptionPlans()->syncWithPivotValues($subcription_plan_ids, [
                'project_id' => $project->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        }

        return redirect()->back()->with('message', 'Updated Successfully');
    }

    public function deleteSmsCampaign(Project $project, SmsCampaign $smsCampaign)
    {
        //  Delete sms campaign
        $smsCampaign->delete();

        return redirect()->back()->with('message', 'Deleted Successfully');
    }

    public function showSmsCampaignJobBatches(Project $project, SmsCampaign $smsCampaign)
    {
        //  Get the sms campaign job batches
        $smsCampaignBatchJobsPayload = $smsCampaign->smsCampaignBatchJobs()->latest()->paginate(10);

        //  Render the campaigns view
        return Inertia::render('SmsCampaigns/List/JobBatches/List/Main', [
            'smsCampaign' => $smsCampaign,
            'smsCampaignBatchJobsPayload' => $smsCampaignBatchJobsPayload
        ]);
    }
}
