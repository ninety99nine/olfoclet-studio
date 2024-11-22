<template>

    <div>

        <!-- Add Subscription Plan Button -->
        <div v-if="showHeader" class="grid grid-cols-12 mb-6 gap-4">

            <div class="col-span-8 bg-gray-50 pt-4 pl-6 border-b rounded-t">

                <div class="text-2xl font-semibold leading-6 text-gray-500 border-b pb-4 mb-4">{{ parentSubscriptionPlan ? parentSubscriptionPlan.name : 'Subscription Plans' }}</div>

                <div v-if="parentSubscriptionPlan" class="border-b">

                    <jet-secondary-button @click="goBackToPreviousPage()" class="py-1 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16l-4-4m0 0l4-4m-4 4h18" />
                        </svg>
                        <span class="ml-2">Go Back</span>
                    </jet-secondary-button>

                    <el-breadcrumb separator=">" class="mb-4">
                        <el-breadcrumb-item @click="nagivateToSubscriptionPlan()">
                            <span class="hover:underline hover:text-green-600 text-green-500 font-semibold cursor-pointer">Subscription Plans</span>
                        </el-breadcrumb-item>

                        <el-breadcrumb-item v-for="breadcrumb in breadcrumbs" :key="breadcrumb.id" @click="nagivateToSubscriptionPlan(breadcrumb)">
                            <span class="hover:underline hover:text-green-600 text-green-500 font-semibold cursor-pointer">{{ breadcrumb.name }}</span>
                        </el-breadcrumb-item>
                    </el-breadcrumb>

                </div>

                <div class="text-sm text-gray-500 my-2">
                    <span class="font-bold mr-2">GET / POST:</span>
                    <span v-if="parentSubscriptionPlan" class="text-green-500 font-semibold">{{ route('api.show.subscription.plan', { project: route().params.project, subscription_plan: parentSubscriptionPlan.id, type: 'children' }) }}</span>
                    <span v-else class="text-green-500 font-semibold">{{ route('api.show.subscription.plans', { project: route().params.project }) }}</span>
                </div>

            </div>

            <div v-if="$inertia.page.props.projectPermissions.includes('Manage subscription plans')" class="col-span-4 flex justify-end items-start">
                <jet-button @click="openModal()">Add Subscription Plan</jet-button>
            </div>

        </div>

        <div>

            <!-- Success Message -->
            <div v-if="showSuccessMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6 mt-3" role="alert">
                <strong v-if="wantsToUpdate" class="font-bold">Subscription plan updated successfully</strong>
                <strong v-else-if="wantsToDelete" class="font-bold">Subscription plan deleted successfully</strong>
                <strong v-else class="font-bold">Subscription plan created successfully</strong>

                <span @click="showSuccessMessage = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>

            <!-- Error Message -->
            <div v-if="showErrorMessage" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6 mt-3" role="alert">
                <strong v-if="wantsToUpdate" class="font-bold">Subscription plan update failed</strong>
                <strong v-else-if="wantsToDelete" class="font-bold">Subscription plan delete failed</strong>
                <strong v-else class="font-bold">Subscription plan creation failed</strong>

                <span @click="showSuccessMessage = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>

            <!-- Dialog Modal -->
            <jet-dialog-modal :show="showModal" :closeable="false">

                <!-- Modal Title -->
                <template #title>

                    <template v-if="wantsToUpdate">Update Subscription Plan</template>

                    <template v-else-if="wantsToDelete">Delete Subscription Plan</template>

                    <template v-else>Add Subscription Plan</template>

                </template>

                <!-- Modal Content -->
                <template #content>

                    <template v-if="wantsToDelete">

                        <span class="block mt-6 mb-6">Are you sure you want to delete this subscription plan?</span>

                        <p class="text-sm text-gray-500">{{ subscriptionPlan.name }}</p>

                    </template>

                    <template v-else>

                        <span class="block mt-6 mb-6">

                            <span v-if="parentSubscriptionPlan">
                                You are {{ wantsToUpdate ? 'updating' : 'adding' }} a subscription plan for
                                <span class="rounded-lg py-1 px-2 border border-green-400 text-green-500 text-sm">
                                    {{ parentSubscriptionPlan.name }}
                                </span>
                            </span>

                            <div v-if="parentSubscriptionPlan" class="bg-gray-50 py-3 px-3 mt-6 mb-2">

                                <el-breadcrumb separator=">">
                                    <el-breadcrumb-item>
                                        <span class="hover:text-green-600 text-green-500 font-semibold">Subscription Plans</span>
                                    </el-breadcrumb-item>

                                    <el-breadcrumb-item v-for="breadcrumb in breadcrumbs" :key="breadcrumb.id">
                                        <span class="text-green-500 font-semibold">{{ breadcrumb.name }}</span>
                                    </el-breadcrumb-item>
                                </el-breadcrumb>

                            </div>

                        </span>

                        <!-- Name -->
                        <div class="mb-4">
                            <jet-label for="name" value="Name" />
                            <jet-input id="name" type="text" class="w-full mt-1 block" v-model="form.name" placeholder="Daily @ P95.00"/>
                            <jet-input-error :message="form.errors.name" class="mt-2" />
                        </div>

                        <!-- Active -->
                        <div class="mb-4">
                            <span class="text-sm text-gray-500">Active</span>
                            <el-switch v-model="form.active" class="mx-2"></el-switch>
                            <span class="text-sm text-gray-400">— {{ form.active ? 'Turn off to disable this subscription plan' : 'Turn on to enable this subscription plan' }}</span>
                        </div>

                        <!-- Folder -->
                        <div class="mb-4">
                            <span class="text-sm text-gray-500">Folder</span>
                            <el-switch v-model="form.is_folder" class="mx-2"></el-switch>
                            <span class="text-sm text-gray-400">— {{ form.is_folder ? 'Turn off to make this a subscription plan' : 'Turn on to make this a folder' }}</span>
                        </div>

                        <template v-if="form.is_folder == false">

                            <div class="grid gap-4 grid-cols-2">

                                <!-- Duration -->
                                <div class="mb-4">
                                    <jet-label for="duration" value="Duration" />
                                    <jet-number-input id="duration" class="w-full mt-1 block" v-model.string="form.duration" placeholder="1"/>
                                    <jet-input-error :message="form.errors.duration" class="mt-2" />
                                </div>

                                <!-- Frequency -->
                                <div class="mb-4">
                                    <jet-select-input placeholder="Select frequency" :options="frequencyOptions" v-model="form.frequency" class="mt-6" />
                                    <jet-input-error :message="form.errors.frequency" class="mt-2" />
                                </div>

                            </div>

                            <!-- Price -->
                            <div class="mb-4">
                                <jet-label for="price" value="Price" class="mb-1" />
                                <jet-number-input id="price" class="w-full mt-1 block" v-model="form.price" placeholder="95.00"/>
                                <jet-input-error :message="form.errors.price" class="mt-2" />
                            </div>

                            <!-- Description -->
                            <div class="mb-4">
                                <div class="flex mb-1">
                                    <jet-label for="sub-pl-description" value="Description" />

                                    <el-popover :width="300">
                                        <template #reference>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                            </svg>
                                        </template>
                                        <template #default>
                                            <span class="break-normal">
                                                When billing the subscriber and creating airtime transation records, this description will be used as the transaction description.
                                            </span>
                                        </template>
                                    </el-popover>

                                </div>
                                <jet-textarea id="sub-pl-description" class="w-full mt-1 block" v-model="form.description" />
                                <jet-input-error :message="form.errors.description" class="mt-2" />
                            </div>

                            <div class="flex bg-slate-50 rounded-lg p-4 mb-4">

                                <span class="font-bold mr-2">Category</span>

                                <el-tag v-for="tag in form.tags" :key="tag" class="mx-1" closable :disable-transitions="false" @close="handleRemoveTag(tag)">{{ tag }}</el-tag>

                                <span v-if="showAddTagInput" class="w-20">
                                    <el-input ref="addTagInputRef" v-model="addTagInput" size="small"
                                        @keyup.enter="handleAddTag"
                                        @blur="handleAddTag"/>
                                </span>

                                <el-button v-else class="button-new-tag ml-1" size="small" @click="showInput">
                                    + New Tag
                                </el-button>

                            </div>

                            <!-- Insufficient Funds Message -->
                            <div class="mb-4">
                                <div class="flex mb-1">
                                    <jet-label for="sub-pl-c-message" value="Insufficient Funds Message" />

                                    <el-popover :width="400">
                                        <template #reference>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                            </svg>
                                        </template>
                                        <template #default>
                                            <span class="break-normal">
                                                While billing, this message will be used as the billing transaction description to know why the billing transaction failed.
                                                This message can be shown to the user via USSD if required. This is possible since making a subscription via the API endpoint
                                                returns the billing transaction resource which can be used to determine the billing transaction success status as well as this
                                                message to show the subscriber in the case that the transaction has failed due to insufficient funds.
                                            </span>
                                            <div class="mt-4">
                                                <table class="w-full divide-y divide-gray-200">
                                                    <thead>
                                                        <tr class="font-bold">
                                                            <td>Variable</td>
                                                            <td>Meaning</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-gray-200">
                                                        <tr>
                                                            <td class="font-semibold text-green-500" v-html="'{{ subscriptionPlanName }}'"></td>
                                                            <td>The Subscription Plan name</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="font-semibold text-green-500" v-html="'{{ subscriptionPlanPrice }}'"></td>
                                                            <td>The Subscription Plan price</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <p class="mt-4"><strong>Example:</strong> <span v-html="'You do not have enough funds to complete this transaction'"></span></p>

                                        </template>
                                    </el-popover>

                                </div>
                                <jet-textarea id="sub-pl-insufficient-funds-message" class="w-full mt-1 block" v-model="form.insufficient_funds_message" />
                                <jet-input-error :message="form.errors.insufficient_funds_message" class="mt-2" />
                            </div>

                            <!-- Successful Payment SMS Message -->
                            <div class="mb-4">
                                <div class="flex mb-1">
                                    <jet-label for="sub-pl-successful-payment-sms-message" value="Successful Payment SMS Message" />

                                    <el-popover :width="400">
                                        <template #reference>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                            </svg>
                                        </template>
                                        <template #default>
                                            <span class="break-normal">
                                                While billing, this message will be sent as an SMS to the subscriber if they have sufficient funds to complete the transaction
                                            </span>
                                            <div class="mt-4">
                                                <table class="w-full divide-y divide-gray-200">
                                                    <thead>
                                                        <tr class="font-bold">
                                                            <td>Variable</td>
                                                            <td>Meaning</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-gray-200">
                                                        <tr>
                                                            <td class="font-semibold text-green-500" v-html="'{{ subscriptionId }}'"></td>
                                                            <td >The Subscription ID</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="font-semibold text-green-500" v-html="'{{ nextBillableDate }}'"></td>
                                                            <td>The next billable date</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="font-semibold text-green-500" v-html="'{{ subscriptionEndDate }}'"></td>
                                                            <td>The Subscription end date</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="font-semibold text-green-500" v-html="'{{ subscriptionStartDate }}'"></td>
                                                            <td>The Subscription start date</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="font-semibold text-green-500" v-html="'{{ subscriptionPlanName }}'"></td>
                                                            <td>The Subscription Plan name</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="font-semibold text-green-500" v-html="'{{ subscriptionPlanPrice }}'"></td>
                                                            <td>The Subscription Plan price</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <p class="mt-4"><strong>Example:</strong> <span v-html="'Your payment for {{ subscriptionPlanName }} priced {{ subscriptionPlanPrice }} was completed successfully. Valid till {{ subscriptionEndDate }}. You will be automatically billed on {{ nextBillableDate }}. Ref: #{{ subscriptionId }} . Dial *xxx# to unsubscribe.'"></span></p>

                                        </template>
                                    </el-popover>

                                </div>
                                <jet-textarea id="sub-pl-successful-payment-sms-message" class="w-full mt-1 block" v-model="form.successful_payment_sms_message" />
                                <jet-input-error :message="form.errors.successful_payment_sms_message" class="mt-2" />
                            </div>

                            <!-- Billing Product ID -->
                            <div class="mb-4">
                                <div class="flex mb-1">
                                    <jet-label for="billing_product_id" value="Billing Product ID (AAS)" />
                                    <el-popover :width="400">
                                        <template #reference>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                            </svg>
                                        </template>
                                        <template #default>
                                            <span class="break-normal">
                                                This is the subscription plan identifier used by the Mobile Network to distiguish between transactions based on the product being purchased. Give a unique name that will distinguish this subscription plan as a product
                                            </span>
                                        </template>
                                    </el-popover>
                                </div>
                                <jet-input id="billing_product_id" type="text" class="w-full mt-1 block" v-model="form.billing_product_id" placeholder="Product 123"/>
                                <jet-input-error :message="form.errors.billing_product_id" class="mt-2" />
                            </div>

                            <!-- Billing Purchase Category Code -->
                            <div class="mb-4">

                                <div class="flex mb-1">
                                    <jet-label for="billing_purchase_category_code" value="Billing Purchase Category Code (AAS)" />
                                    <el-popover :width="400">
                                        <template #reference>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                            </svg>
                                        </template>
                                        <template #default>
                                            <span class="break-normal">
                                                This is the subscription plan category used by the Mobile Network to distiguish between transactions. This parameter MUST be filled with values validated by AAS integration team
                                            </span>
                                        </template>
                                    </el-popover>
                                </div>
                                <jet-input id="billing_purchase_category_code" type="text" class="w-full mt-1 block" v-model="form.billing_purchase_category_code" placeholder="Product 123"/>
                                <jet-input-error :message="form.errors.billing_purchase_category_code" class="mt-2" />
                            </div>

                            <!-- Auto Bill -->
                            <div class="mb-4">
                                <span class="text-sm text-gray-500">Can Auto Bill</span>
                                <el-switch v-model="form.can_auto_bill" class="mx-2"></el-switch>
                                <span class="text-sm text-gray-400">— {{ form.can_auto_bill ? 'Turn off to disable auto billing on this subscription plan' : 'Turn on to enable auto billing on this subscription plan' }}</span>
                            </div>

                            <template v-if="form.can_auto_bill">

                                <!-- Successful Auto Billing Payment SMS Message -->
                                <div class="mb-4">
                                    <div class="flex mb-1">
                                        <jet-label for="sub-pl-successful-auto-payment-sms-message" value="Successful Auto Billing Payment SMS Message" />

                                        <el-popover :width="400">
                                            <template #reference>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                                    <path strokeLinecap="round" strokeLinejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                                </svg>
                                            </template>
                                            <template #default>
                                                <span class="break-normal">
                                                    While auto billing, this message will be sent as an SMS to the subscriber if they have sufficient funds to complete the transaction
                                                </span>
                                                <div class="mt-4">
                                                    <table class="w-full divide-y divide-gray-200">
                                                        <thead>
                                                            <tr class="font-bold">
                                                                <td>Variable</td>
                                                                <td>Meaning</td>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-200">
                                                            <tr>
                                                                <td class="font-semibold text-green-500" v-html="'{{ subscriptionId }}'"></td>
                                                                <td >The Subscription ID</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="font-semibold text-green-500" v-html="'{{ nextBillableDate }}'"></td>
                                                                <td>The next billable date</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="font-semibold text-green-500" v-html="'{{ subscriptionEndDate }}'"></td>
                                                                <td>The Subscription end date</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="font-semibold text-green-500" v-html="'{{ subscriptionStartDate }}'"></td>
                                                                <td>The Subscription start date</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="font-semibold text-green-500" v-html="'{{ subscriptionPlanName }}'"></td>
                                                                <td>The Subscription Plan name</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="font-semibold text-green-500" v-html="'{{ subscriptionPlanPrice }}'"></td>
                                                                <td>The Subscription Plan price</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <p class="mt-4"><strong>Example:</strong> <span v-html="'Your auto payment for {{ subscriptionPlanName }} priced {{ subscriptionPlanPrice }} was completed successfully. Valid till {{ subscriptionEndDate }}. You will be automatically billed on {{ nextBillableDate }}. Ref: #{{ subscriptionId }} . Dial *xxx# to unsubscribe.'"></span></p>

                                            </template>
                                        </el-popover>

                                    </div>
                                    <jet-textarea id="sub-pl-successful-auto-payment-sms-message" class="w-full mt-1 block" v-model="form.successful_auto_billing_payment_sms_message" />
                                    <jet-input-error :message="form.errors.successful_auto_billing_payment_sms_message" class="mt-2" />
                                </div>

                                <!-- Maximum Auto Billing Attempts -->
                                <div class="mb-4">
                                    <jet-label for="max_auto_billing_attempts" value="Maximum Auto Billing Attempts" />
                                    <jet-number-input id="max_auto_billing_attempts" class="w-full mt-1" v-model.string="form.max_auto_billing_attempts" min="1" max="10" placeholder="3"/>
                                    <jet-input-error :message="form.errors.max_auto_billing_attempts" class="mt-2" />
                                </div>

                                <!-- Next Auto Billing Reminder Options -->
                                <div class="flex items-center mb-4">
                                    <span class="font-medium text-sm text-gray-700 whitespace-nowrap mr-2">Next Auto Billing Reminder:</span>
                                    <div class="w-full">
                                        <el-select v-model="form.auto_billing_reminder_ids" multiple placeholder="Set Reminders" class="w-full">
                                            <el-option v-for="option in autoBillingReminderOptions" :key="option.value" :value="option.value" :label="option.name"></el-option>
                                        </el-select>
                                        <jet-input-error :message="form.errors.auto_billing_reminder_ids" class="mt-2" />
                                    </div>
                                </div>

                                <!-- Next Auto Billing Reminder SMS Message -->
                                <div class="mb-4">
                                    <div class="flex mb-1">
                                        <jet-label for="sub-pl-next-auto-billing-reminder-sms-message" value="Next Auto Billing Reminder SMS Message" />

                                        <el-popover :width="400">
                                            <template #reference>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                                    <path strokeLinecap="round" strokeLinejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                                </svg>
                                            </template>
                                            <template #default>
                                                <span class="break-normal">
                                                    Before billing, this message will be sent as an SMS to the subscriber to notify them several hours before auto billing is attempted. This grants the subscriber the opportunity to unsubscribe if necessary
                                                </span>
                                                <div class="mt-4">
                                                    <table class="w-full divide-y divide-gray-200">
                                                        <thead>
                                                            <tr class="font-bold">
                                                                <td>Variable</td>
                                                                <td>Meaning</td>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-200">
                                                            <tr>
                                                                <td class="font-semibold text-green-500" v-html="'{{ nextBillableDate }}'"></td>
                                                                <td>The next billable date</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="font-semibold text-green-500" v-html="'{{ subscriptionPlanName }}'"></td>
                                                                <td>The Subscription Plan name</td>
                                                            </tr>
                                                        <tr>
                                                            <td class="font-semibold text-green-500" v-html="'{{ subscriptionPlanPrice }}'"></td>
                                                            <td>The Subscription Plan price</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>

                                                    <p class="mt-4"><strong>Example:</strong> <span v-html="'You will be automatically billed for {{ subscriptionPlanName }} priced {{ subscriptionPlanPrice }} on {{ nextBillableDate }}. Dial *xxx# to unsubscribe.'"></span></p>
                                                </div>

                                            </template>
                                        </el-popover>
                                    </div>
                                    <jet-textarea id="sub-pl-next-auto-billing-reminder-sms-message" class="w-full mt-1 block" v-model="form.next_auto_billing_reminder_sms_message" />
                                    <jet-input-error :message="form.errors.next_auto_billing_reminder_sms_message" class="mt-2" />
                                </div>

                                <!-- Auto Billing Disabled SMS Message -->
                                <div class="mb-4">
                                    <div class="flex mb-1">
                                        <jet-label for="sub-pl-next-auto-billing-reminder-sms-message" value="Auto Billing Disabled SMS Message" />

                                        <el-popover :width="400">
                                            <template #reference>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                                    <path strokeLinecap="round" strokeLinejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                                </svg>
                                            </template>
                                            <template #default>
                                                <span class="break-normal">
                                                    After attempting auto billing for the last time with no avail, this message will be sent to the subscriber to notify them that auto billing has been disabled for this subscription plan. This means that auto billing on this subscription plan will no longer be attempted moving forward, therefore the subscriber must internally subscribe for this subscription plan
                                                </span>
                                                <div class="mt-4">
                                                    <table class="w-full divide-y divide-gray-200">
                                                        <thead>
                                                            <tr class="font-bold">
                                                                <td>Variable</td>
                                                                <td>Meaning</td>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-200">
                                                            <tr>
                                                                <td class="font-semibold text-green-500" v-html="'{{ subscriptionPlanName }}'"></td>
                                                                <td>The Subscription Plan name</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="font-semibold text-green-500" v-html="'{{ subscriptionPlanPrice }}'"></td>
                                                                <td>The Subscription Plan price</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>

                                                    <p class="mt-4"><strong>Example:</strong> <span v-html="'You have been successfully unsubscribed from {{ subscriptionPlanName }} priced {{ subscriptionPlanPrice }}. Dial *217# to subscribe.'"></span></p>
                                                </div>

                                            </template>
                                        </el-popover>
                                    </div>
                                    <jet-textarea id="sub-pl-next-auto-billing-reminder-sms-message" class="w-full mt-1 block" v-model="form.auto_billing_disabled_sms_message" />
                                    <jet-input-error :message="form.errors.auto_billing_disabled_sms_message" class="mt-2" />
                                </div>

                            </template>

                        </template>

                    </template>

                </template>

                <!-- Modal Footer -->
                <template #footer>

                    <jet-secondary-button @click="closeModal()" class="mr-2">
                        Cancel
                    </jet-secondary-button>

                    <jet-button v-if="!hasSubscriptionPlan" @click.prevent="create()" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Create
                    </jet-button>

                    <jet-button v-if="wantsToUpdate" @click.prevent="update()" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Update
                    </jet-button>

                    <jet-danger-button v-if="wantsToDelete" @click.prevent="destroy()" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Delete
                    </jet-danger-button>

                </template>

            </jet-dialog-modal>

        </div>

    </div>

</template>

<script>

    import { nextTick, defineComponent } from 'vue';
    import JetInput from '@/Components/TextInput.vue';
    import JetLabel from '@/Components/InputLabel.vue';
    import JetTextarea from '@/Components/Textarea.vue';
    import JetButton from '@/Components/PrimaryButton.vue';
    import JetInputError from '@/Components/InputError.vue';
    import JetNumberInput from '@/Components/NumberInput.vue';
    import JetSelectInput from '@/Components/SelectInput.vue';
    import JetDialogModal from '@/Components/DialogModal.vue';
    import JetDangerButton from '@/Components/DangerButton.vue';
    import JetSecondaryButton from '@/Components/SecondaryButton.vue';

    export default defineComponent({
        components: {
            JetLabel, JetInput, JetNumberInput, JetTextarea, JetButton, JetInputError, JetSelectInput,
            JetDialogModal, JetSecondaryButton, JetDangerButton
        },
        props: {
            action: String,
            breadcrumbs: Array,
            modelValue: Boolean,
            subscriptionPlan: Object,
            autoBillingReminders: Array,
            parentSubscriptionPlan: Object,
            showHeader: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {

                //  Form attributes
                form: null,

                addTagInput: '',
                showAddTagInput: false,

                //  Modal attributes
                showModal: this.modelValue,

                showSuccessMessage: false,
                showErrorMessage: false,

            }
        },

        watch: {

            showModal: {
                handler: function (val, oldVal) {

                    if(val != this.modelValue){
                        this.$emit('update:modelValue', val);
                    }

                }
            },

            modelValue: {
                handler: function (val, oldVal) {

                    if(val != this.showModal){
                        this.showModal = val;
                        this.reset();
                    }

                }
            },

        },

        computed: {
            hasSubscriptionPlan(){
                return this.subscriptionPlan == null ? false : true;
            },
            wantsToUpdate(){
                return (this.hasSubscriptionPlan && this.action == 'update') ? true : false;
            },
            wantsToDelete(){
                return (this.hasSubscriptionPlan && this.action == 'delete') ? true : false;
            },
            frequencyOptions(){
                return [
                    {
                        name: this.form.duration == '1' ? 'Minute': 'Minutes',
                        value: 'Minutes'
                    },
                    {
                        name: this.form.duration == '1' ? 'Hour': 'Hours',
                        value: 'Hours'
                    },
                    {
                        name: this.form.duration == '1' ? 'Day': 'Days',
                        value: 'Days'
                    },
                    {
                        name: this.form.duration == '1' ? 'Week': 'Weeks',
                        value: 'Weeks'
                    },
                    {
                        name: this.form.duration == '1' ? 'Month': 'Months',
                        value: 'Months'
                    },
                    {
                        name: this.form.duration == '1' ? 'Year': 'Years',
                        value: 'Years'
                    }
                ];
            },
            autoBillingReminderOptions() {
                return this.autoBillingReminders.map(function(autoBillingReminder){
                    return {
                        'name': autoBillingReminder.name,
                        'value': autoBillingReminder.id,
                    };
                });
            }
        },
        methods: {
            nagivateToSubscriptionPlan(subscriptionPlan = null){
                if( subscriptionPlan ){

                    this.$inertia.get(route('show.subscription.plan', { project: route().params.project, subscription_plan: subscriptionPlan.id }));

                }else{

                    this.$inertia.get(route('show.subscription.plans', { project: route().params.project }));

                }
            },
            goBackToPreviousPage(){
                window.history.back();
            },

            /**
             *  MODAL METHODS
             */
            openModal() {
                this.showModal = true;
            },
            closeModal() {
                this.showModal = false;
            },

            /**
             *  TAG METHODS
             */
            handleAddTag() {
                if (this.addTagInput) {
                    const newTag = this.addTagInput.trim();
                    if (!this.form.tags.includes(newTag)) {
                        this.form.tags.push(newTag);
                    }
                }
                this.showAddTagInput = false;
                this.addTagInput = '';
            },
            handleRemoveTag(tag) {
                this.form.tags.splice(this.form.tags.indexOf(tag), 1);
            },
            showInput() {
                this.showAddTagInput = true;
                nextTick(() => {
                    this.$refs.addTagInputRef.focus();
                });
            },

            /**
             *  FORM METHODS
             */
            create() {
                var options = {

                    preserveState: true, preserveScroll: true, replace: true,

                    onSuccess: (response) => {

                        this.handleOnSuccess();

                    },

                    onError: errors => {

                        this.handleOnError();

                    },

                };

                this.form.transform((data) => {
                    if(data.is_folder) {
                        return {
                            'name': data.name,
                            'active': data.active,
                            'is_folder': data.is_folder,
                            'parent_id': data.parent_id,
                        };
                    }else if(data.can_auto_bill == false) {
                        delete data.next_auto_billing_reminder_sms_message;
                        delete data.auto_billing_reminder_ids;
                        return data;
                    }else {
                        return data;
                    }
                }).post(route('create.subscription.plan', { project: route().params.project }), options);
            },
            update() {
                var options = {

                    preserveState: true, preserveScroll: true, replace: true,

                    onSuccess: (response) => {

                        this.handleOnSuccess();

                    },

                    onError: errors => {

                        this.handleOnError();

                    },
                };

                this.form.transform((data) => {
                    if(data.is_folder) {
                        return {
                            'name': data.name,
                            'active': data.active,
                            'is_folder': data.is_folder,
                            'parent_id': data.parent_id,
                        };
                    }else if(data.can_auto_bill == false) {
                        delete data.next_auto_billing_reminder_sms_message;
                        delete data.auto_billing_reminder_ids;
                        return data;
                    }else {
                        return data;
                    }
                }).put(route('update.subscription.plan', { project: route().params.project, subscription_plan: this.subscriptionPlan.id }), options);
            },
            destroy() {

                var options = {

                    preserveState: true, preserveScroll: true, replace: true,

                    onSuccess: (response) => {

                        this.handleOnSuccess(true);

                    },

                    onError: errors => {

                        this.handleOnError();

                    },
                };

                this.form.delete(route('delete.subscription.plan', { project: route().params.project, subscription_plan: this.subscriptionPlan.id }), options);
            },
            handleOnSuccess(hasDeleted = false){

                this.reset();
                this.closeModal();
                if(hasDeleted) this.$emit('onDeleted');

                this.showSuccessMessage = true;

                setTimeout(() => {
                    this.showSuccessMessage = false;
                }, 3000);

            },
            handleOnError(){

                this.showErrorMessage = true;

                setTimeout(() => {
                    this.showErrorMessage = false;
                }, 3000);

            },
            reset() {
                this.form = this.$inertia.form({
                    name: this.hasSubscriptionPlan ? this.subscriptionPlan.name : null,
                    active: this.hasSubscriptionPlan ? this.subscriptionPlan.active : false,
                    tags: this.hasSubscriptionPlan ? this.subscriptionPlan.tags ?? [] : [],
                    is_folder: this.hasSubscriptionPlan ? this.subscriptionPlan.is_folder : false,
                    frequency: this.hasSubscriptionPlan ? this.subscriptionPlan.frequency : 'Days',
                    parent_id: this.parentSubscriptionPlan ? this.parentSubscriptionPlan.id : null,
                    description: this.hasSubscriptionPlan ? this.subscriptionPlan.description : null,
                    can_auto_bill: this.hasSubscriptionPlan ? this.subscriptionPlan.can_auto_bill : false,
                    price: this.hasSubscriptionPlan ? ((this.subscriptionPlan.price ?? {}).amount_without_currency) : null,
                    max_auto_billing_attempts: this.hasSubscriptionPlan ? this.subscriptionPlan.max_auto_billing_attempts : 3,
                    billing_product_id: this.hasSubscriptionPlan ? this.subscriptionPlan.billing_product_id : 'Product 123',
                    billing_purchase_category_code: this.hasSubscriptionPlan ? this.subscriptionPlan.billing_purchase_category_code : '',
                    duration: this.hasSubscriptionPlan ? (this.subscriptionPlan.is_folder ? null : this.subscriptionPlan.duration.toString()) : '3',
                    auto_billing_reminder_ids: this.hasSubscriptionPlan ? this.subscriptionPlan.auto_billing_reminders.map((autoBillingReminder) => autoBillingReminder.id) : [],
                    insufficient_funds_message: this.hasSubscriptionPlan ? this.subscriptionPlan.insufficient_funds_message : 'You do not have enough funds to complete this transaction',
                    auto_billing_disabled_sms_message: this.hasSubscriptionPlan ? this.subscriptionPlan.auto_billing_disabled_sms_message : 'You have been successfully unsubscribed from {{ subscriptionPlanName }}. Dial *xxx# to subscribe.',
                    next_auto_billing_reminder_sms_message: this.hasSubscriptionPlan ? this.subscriptionPlan.next_auto_billing_reminder_sms_message : 'You will be automatically billed {{ subscriptionPlanPrice }} on {{ nextBillableDate }} for {{ subscriptionPlanName }}. Dial *xxx# to unsubscribe.',
                    successful_payment_sms_message: this.hasSubscriptionPlan ? this.subscriptionPlan.successful_payment_sms_message : 'Your payment for {{ subscriptionPlanName }} was completed successfully. Valid till {{ subscriptionEndDate }}. You will be automatically billed on {{ nextBillableDate }}. Ref: #{{ subscriptionId }}. Dial *xxx# to unsubscribe.',
                    successful_auto_billing_payment_sms_message: this.hasSubscriptionPlan ? this.subscriptionPlan.successful_auto_billing_payment_sms_message : 'Your payment for {{ subscriptionPlanName }} was completed successfully. Valid till {{ subscriptionEndDate }}. You will be automatically billed on {{ nextBillableDate }}. Ref: #{{ subscriptionId }}. Dial *xxx# to unsubscribe.',
                });
            },
        },
        created(){

            this.reset();

        }
    })
</script>
