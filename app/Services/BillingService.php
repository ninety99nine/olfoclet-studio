<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Models\Project;
use App\Models\Subscriber;
use App\Models\SubscriptionPlan;
use App\Models\BillingTransaction;
use App\Enums\BillingTransactionFailureType;
use App\Enums\CacheName;
use App\Enums\CreatedUsingAutoBilling;
use App\Enums\MessageType;
use App\Helpers\CacheManager;
use App\Models\Message;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BillingService
{
    /**
     *  Bill subscriber on their airtime
     *
     *  @param Project $project - The project Model
     *  @param SubscriptionPlan $subscriptionPlan - The subscription plan Model
     *  @param Subscriber $subscriber - The subscriber Model
     *  @param CreatedUsingAutoBilling $createdUsingAutoBilling - Whether this is an auto billing transaction
     *
     *  @return BillingTransaction
     */
    public static function billUsingAirtime($project, $subscriptionPlan, $subscriber, CreatedUsingAutoBilling $createdUsingAutoBilling = CreatedUsingAutoBilling::NO): BillingTransaction
    {
        $msisdn = $subscriber->msisdn;
        $amount = $subscriptionPlan->price->amount;
        $description = $subscriptionPlan->description;
        $onBehalfOf = $project->settings['billing_name'];
        $productId = $subscriptionPlan->billing_product_id;
        $purchaseCategoryCode = $subscriptionPlan->billing_purchase_category_code;

        $clientId = $project->settings['auto_billing_client_id'];
        $clientSecret = $project->settings['auto_billing_client_secret'];

        //  Create a billing transaction
        $billingTransaction = BillingTransaction::create([
            'amount' => $amount,
            'is_successful' => false,
            'project_id' => $project->id,
            'description' => $description,
            'subscriber_id' => $subscriber->id,
            'subscription_plan_id' => $subscriptionPlan->id,
            'created_using_auto_billing' => $createdUsingAutoBilling->value,
        ]);

        try {

            /**
             *  ------------------------
             *  Request the access token
             *  ------------------------
             *
             *  On Success, the response payload is as follows:
             *
             *  [
             *      "status" => true
             *      "body" => [
             *          "access_token" => "c0352550-14c4-3a74-b82e-31bd8d09a556",
             *          "scope" => "am_application_scope default",
             *          "token_type" => "Bearer",
             *          "expires_in" => 3600
             *      ]
             *  ]
             *
             *  On Fail, the response payload is as follows =>
             *
             *  [
             *      "status" => false
             *      "body" => [
             *          "error_description" => "Oauth application is not in active state.",
             *          "error" => "invalid_client"
             *      ]
             *  ]
             */
            $response = self::requestNewAirtimeBillingAccessToken($clientId, $clientSecret);

            if($status = $response['status']) {

                $accessToken = $response['body']['access_token'];

                /**
                 *  -----------------------------
                 *  Request the product inventory
                 *  -----------------------------
                 *
                 *  On Success, the response payload is as follows:
                 *
                 *  [
                 *      "status" => true
                 *      "body" => [
                 *          [
                 *              "id" => "8037c89b-f204-428e-9336-d3a4bca1b3fe",
                 *              "ratingType" => "Postpaid",
                 *              "status" => "Active",
                 *              "isBundle" => true,
                 *              "startDate" => "2020-09-17T00 =>00 =>00+0000",
                 *              "productOffering" => [
                 *                  "id" => "Orange_Postpaid",
                 *                  "name" => "MySim"
                 *              ]
                 *          ]
                 *      ]
                 *  ]
                 *
                 *  On Fail, the response payload is as follows =>
                 *
                 *  [
                 *      "status" => false
                 *      "body" => [
                 *          "code" => 4001,
                 *          "message" => "Missing parameter",
                 *          "description" => "Parameter publicKey is missing, null or empty"
                 *      ]
                 *  ]
                 */
                $response = self::requestAirtimeBillingProductInventory($msisdn, $accessToken);

                if($status = $response['status']) {

                    //  Get the first item of the product inventory array
                    $productInventory = $response['body'][0];

                    //  Determine if this is an active account
                    $isAnActiveAccount = $productInventory['status'] == 'Active';

                    //  If this is an active account
                    if( $status = $isAnActiveAccount ) {

                        //  Get the account rating type
                        $ratingType = $productInventory['ratingType'];

                        //  Determine if this is a prepaid account
                        $isPrepaidAccount = ($ratingType == 'Prepaid');

                        //  Determine if this is a postpaid account
                        $isPostpaidAccount = ($ratingType == 'Postpaid');

                        //  If this is a postpaid account, we assume to always have enough funds
                        $hasEnoughFunds = $isPostpaidAccount;

                        /**
                         *  If this is a prepaid account, we need to check the
                         *  account balance to know if we have enough funds.
                         */
                        if( $isPrepaidAccount ) {

                            /**
                             *  -----------------------------
                             *  Request the usage consumption
                             *  -----------------------------
                             *
                             *  On Success, the response payload is as follows:
                             *
                             *  [
                             *      "status" => true
                             *      "body" => [
                             *          "id" => "2b778311-ab1b-4f9b-bdb7-e8f3632a6ca9",
                             *          "effectiveDate" => "2022-01-21T13:24:33+0000",
                             *          "bucket" => [
                             *              ...,
                             *              [
                             *                  "id" => "OCS-0",
                             *                  "name" => "Main Balance",
                             *                  "usageType" => "accountBalance",
                             *                  "bucketBalance" => [
                             *                      [
                             *                          "unit" => "BWP",
                             *                          "remainingValue" => 0,
                             *                          "validFor" => [
                             *                              "startDateTime" => "2019-04-04T00:00:00+0000",
                             *                              "endDateTime" => "2023-01-06T00:00:00+0000"
                             *                          ]
                             *                      ]
                             *                  ]
                             *              ],
                             *              ...
                             *          ]
                             *      ]
                             *  ]
                             *
                             *  On Fail, the response payload is as follows =>
                             *
                             *  [
                             *      "status" => false
                             *      "body" => [
                             *          "code" => 4001,
                             *          "message" => "Missing parameter",
                             *          "description" => "Parameter publicKey is missing, null or empty"
                             *      ]
                             *  ]
                             */
                            $response = self::requestAirtimeBillingUsageConsumption($msisdn, $accessToken);

                            if($status = $response['status']) {

                                //  Get the bucket with the id of "OCS-0" as it holds information about the "Main Balance"
                                $accountMainBalanceBucket = collect($response['body']['bucket'])->firstWhere('id', 'OCS-0');

                                //  If the bucket with the id of "OCS-0" was extracted successfully
                                if( $status = !empty($accountMainBalanceBucket) ) {

                                    //  Get the remaining value (The Airtime left that we can bill from the bucket balance)
                                    $remainingValue = $accountMainBalanceBucket['bucketBalance'][0]['remainingValue'];

                                    //  Determine if we have enough funds
                                    $status = $hasEnoughFunds = ($remainingValue >= $amount);

                                    //  Set the funds before deduction
                                    $fundsBeforeDeduction = $remainingValue;

                                    //  Set the funds after deduction
                                    $fundsAfterDeduction = $hasEnoughFunds ? ($remainingValue - $amount) : $remainingValue;

                                    //  If we do not have enough funds
                                    if( !$hasEnoughFunds ) {

                                        $failureType = BillingTransactionFailureType::InsufficientFunds;
                                        $failureReason = $subscriptionPlan->craftInsufficientFundsMessage();

                                    }

                                }else{

                                    $failureType = BillingTransactionFailureType::UsageConsumptionRetrievalFailed;
                                    $failureReason = 'The Main Balance information was not found on the usage consumption response payload';

                                }

                            }else{

                                $failureType = BillingTransactionFailureType::UsageConsumptionRetrievalFailed;
                                $failureReason = $response['body']['description'];

                                if(isset($response['body']['message']) && isset($response['body']['description'])) {
                                    $failureReason = trim($response['body']['message'] ."\n". $response['body']['description']);
                                }else if(isset($response['body']['message'])) {
                                    $failureReason = trim($response['body']['message']);
                                }else if(isset($response['body']['description'])) {
                                    $failureReason = trim($response['body']['description']);
                                }else{
                                    $failureReason = json_encode($response['body']);
                                }

                            }

                        }

                        if( $status ) {

                            /**
                             *  --------------------------
                             *  Request to bill subscriber
                             *  --------------------------
                             *
                             *  On Success, the response payload is as follows:
                             *
                             *  [
                             *      "status" => true
                             *      "body" => [
                             *          "amountTransaction" => [
                             *              "endUserId" => "tel:+ [MSISDN_WITH_COUNTRYCODE]",
                             *              "paymentAmount" => [
                             *                  "chargingInformation" => [
                             *                      "amount" => 5 ,
                             *                      "currency" => "XOF",
                             *                      "description" => [
                             *                          "Short description of the charge"
                             *                      ]
                             *                  ],
                             *                  "totalAmountCharged" => 5 ,
                             *                  "chargingMetaData" => [
                             *                      "productId" => "Daily_subscription",
                             *                      "serviceId" => "Football_results",
                             *                      "purchaseCategoryCode" => "Daily_autorenew_pack "
                             *                  ]
                             *              ],
                             *              "clientCorrelator" => "unique-technical-id",
                             *              "referenceCode" => "Service_provider_payment_reference",
                             *              "transactionOperationStatus" => "Charged",
                             *              "serverReferenceCode" => "5b9bb0235c2dbe6d16d6b5b2",
                             *              "resourceURL" => "/payment/v1/tel%3A%2B [MSISDN_WITH_COUNTRYCODE] /transactions/amount/5b9bb0235c2dbe6d16d6b5b2",
                             *              "link" => []
                             *          ]
                             *      ]
                             *  ]
                             *
                             *  On Fail, the response payload is as follows =>
                             *
                             *  Policy error example:
                             *
                             *  [
                             *      "status" => false
                             *      "body" => [
                             *          "requestError" => [
                             *              "policyException" => [
                             *                  "messageId" => "POL2206",
                             *                  "text" => "User forbidden."
                             *              ]
                             *          ]
                             *      ]
                             *  ]
                             *
                             *  or
                             *
                             *  Server error example:
                             *
                             *  [
                             *      "status" => false
                             *      "body" => [
                             *          "requestError" => [
                             *              "serviceException" => [
                             *                  "messageId": "SVC0005",
                             *                  "text": "duplicate correlatorId cc1d2d34",
                             *                  "variables": [
                             *                      "cc1d2d34"
                             *                  ]
                             *              ]
                             *          ]
                             *      ]
                             *  ]
                             */
                            $response = self::requestAirtimeBillingDeductFee($billingTransaction, $msisdn, $amount, $onBehalfOf, $productId, $purchaseCategoryCode, $description, $accessToken);

                            if($status = $response['status']) {

                                //  The billing is successful at this point

                            }else{

                                $failureType = BillingTransactionFailureType::ProductInventoryRetrievalFailed;

                                if(isset($response['body']['requestError'])) {
                                    if(isset($response['body']['requestError']['policyException'])) $failureReason = $response['body']['requestError']['policyException']['text'];
                                    if(isset($response['body']['requestError']['serviceException'])) $failureReason = $response['body']['requestError']['serviceException']['text'];
                                }

                                if(!isset($failureReason) && isset($response['body']['message'])) {
                                    $failureReason = $response['body']['message'];
                                }

                                if(!isset($failureReason)){
                                    $failureReason = json_encode($response['body']);
                                }

                            }

                        }

                    }else{

                        $failureType = BillingTransactionFailureType::InactiveAccount;
                        $failureReason = 'The account using the mobile number '.$msisdn.' is inactive';

                    }

                }else{

                    $failureType = BillingTransactionFailureType::ProductInventoryRetrievalFailed;

                    if(isset($response['body']['message']) && isset($response['body']['description'])) {
                        $failureReason = trim($response['body']['message'] ."\n". $response['body']['description']);
                    }else if(isset($response['body']['message'])) {
                        $failureReason = trim($response['body']['message']);
                    }else if(isset($response['body']['description'])) {
                        $failureReason = trim($response['body']['description']);
                    }else{
                        $failureReason = json_encode($response['body']);
                    }

                }

            }else{

                $failureType = BillingTransactionFailureType::TokenGenerationFailed;
                $failureReason = $response['body']['error_description'];

            }

            //  Update billing transaction
            $billingTransaction->update([
                'is_successful' => $status,
                'rating_type' => isset($ratingType) ? $ratingType : null,
                'failure_reason' => isset($failureReason) ? $failureReason : null,
                'failure_type' => isset($failureType) ? $failureType->value : null,
                'funds_after_deduction' => isset($fundsAfterDeduction) ? $fundsAfterDeduction : null,
                'funds_before_deduction' => isset($fundsBeforeDeduction) ? $fundsBeforeDeduction : null,
            ]);

            //  Return a fresh instance of the billing transaction
            return $billingTransaction->fresh();

        } catch (\Throwable $th) {

            $failureType = BillingTransactionFailureType::InternalFailure;

            $billingTransaction->update([
                'is_successful' => false,
                'failure_type' => $failureType->value,
                'failure_reason' => $th->getMessage()
            ]);

            //  Return a fresh instance of the billing transaction
            return $billingTransaction->fresh();

        }

    }

    /**
     *  Requests a new airtime billing access token
     *
     *  @param string $clientId - The billing account client id provided by the Mobile Network Operator
     *  @param string $clientSecret - The billing account client secret provided by the Mobile Network Operator
     *
     *  @return array
     */
    public static function requestNewAirtimeBillingAccessToken($clientId, $clientSecret): array
    {
        $cacheManager = (new CacheManager(CacheName::AIRTIME_BILLING_ACCESS_TOKEN_RESPONSE));

        if( $cacheManager->has() ) {

            return $cacheManager->get();

        }else{

            try {

                //  Set the request endpoint
                $endpoint = config('app.ORANGE_BILLING_ENDPOINT').'/token';

                //  Set the request options
                $options = [
                    'headers' => [
                        'Content-type' => 'application/x-www-form-urlencoded',
                        'Accept' => 'application/json'
                    ],
                    'form_params' => [
                        "client_id" => trim($clientId),
                        "grant_type" => "client_credentials",
                        "client_secret" => trim($clientSecret),
                    ],
                    'verify' => false,  // Disable SSL certificate verification
                ];

                //  Create a new Http Guzzle Client
                $httpClient = new Client();

                //  Perform and return the Http request
                $response = $httpClient->request('POST', $endpoint, $options);

            } catch (\GuzzleHttp\Exception\BadResponseException $e) {

                $response = $e->getResponse();

            } catch (\Throwable $e) {

                return [
                    'status' => false,
                    'body' => [
                        'error_description' => $e->getMessage()
                    ]
                ];

            }

            /**
             *  Get the response body as a String.
             *
             *  On Success, the response payload is as follows:
             *
             *  {
             *      "access_token":"c0352550-14c4-3a74-b82e-31bd8d09a556",
             *      "scope":"am_application_scope default",
             *      "token_type":"Bearer",
             *      "expires_in":3600
             *  }
             *
             *  On Fail, the response payload is as follows:
             *
             *  {
             *      "error_description": "Oauth application is not in active state.",
             *      "error": "invalid_client"
             *  }
             */
            $jsonString = $response->getBody();

            /**
             *  Get the response body as an Associative Array:
             *
             *  [
             *      "access_token" => "c0352550-14c4-3a74-b82e-31bd8d09a556",
             *      "scope" => "am_application_scope default",
             *      "token_type" => "Bearer",
             *      "expires_in" => 3600
             *  ]
             */
            $bodyAsArray = json_decode($jsonString, true);

            //  Get the response status code e.g "200"
            $statusCode = $response->getStatusCode();

            //  Return the status and the body
            $data = [
                'status' => ($statusCode == 200),
                'body' => $bodyAsArray
            ];

            if($data['status']) {

                /**
                 *  Cache the successful response data for 58 minutes. The token itself is valid for 1 hour (3600 seconds),
                 *  however we must take into consideration any latecy in the network that may delay the response.
                 *  Therefore we are accomodating 2 minutes (120 seconds) for latency costs. This then means we
                 *  can only cache this successful response data for 58 minutes.
                 *
                 *  Return the status and the body (cached)
                 */
                $cacheManager->put($data, now()->addMinutes(58));

            }

            //  Return the status and the body (uncached)
            return $data;

        }
    }

    /**
     *  Request the airtime billing product inventory data.
     *  This helps us learn the account details, for instance, whether the account
     *  is Active and whether the account is Prepaid or Postpaid.
     *
     *  @param string $msisdn - The MSISDN
     *  @param string $accessToken - The access token
     *
     *  @return array
     */
    public static function requestAirtimeBillingProductInventory($msisdn, $accessToken): array
    {
        try {

            //  Set the request endpoint
            $endpoint = config('app.ORANGE_BILLING_ENDPOINT').'/customer/productInventory/v1/product?publicKey='.$msisdn;

            //  Set the request options
            $options = [
                'headers' => [
                    'Authorization' => 'Bearer '.$accessToken,
                    'Content-type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'verify' => false,  // Disable SSL certificate verification
            ];

            //  Create a new Http Guzzle Client
            $httpClient = new Client();

            //  Perform and return the Http request
            $response = $httpClient->request('GET', $endpoint, $options);

        } catch (\GuzzleHttp\Exception\BadResponseException $e) {

            $response = $e->getResponse();

        } catch (\Throwable $e) {

            return [
                'status' => false,
                'body' => [
                    'message' => $e->getMessage()
                ]
            ];

        }

        /**
         *  Get the response body as a String.
         *
         *  On Success, the response payload is as follows:
         *
         *  [
         *      {
         *          "id": "8037c89b-f204-428e-9336-d3a4bca1b3fe",
         *          "ratingType": "Postpaid",
         *          "status": "Active",
         *          "isBundle": true,
         *          "startDate": "2020-09-17T00:00:00+0000",
         *          "productOffering": {
         *              "id": "Orange_Postpaid",
         *              "name": "MySim"
         *          }
         *      }
         *  ]
         *
         *  On Fail, the response payload is as follows:
         *
         *  {
         *      "code": 4001,
         *      "message": "Missing parameter",
         *      "description": "Parameter publicKey is missing, null or empty"
         *  }
         */
        $jsonString = $response->getBody();

        /**
         *  Get the response body as an Associative Array:
         *
         *  [
         *      [
         *          "id" => "8037c89b-f204-428e-9336-d3a4bca1b3fe",
         *          "ratingType" => "Postpaid",
         *          "status" => "Active",
         *          "isBundle": true,
         *          "startDate" => "2020-09-17T00:00:00+0000",
         *          "productOffering": [
         *              "id" => "Orange_Postpaid",
         *              "name" => "MySim"
         *          ]
         *      ]
         *  ]
         */
        $bodyAsArray = json_decode($jsonString, true);

        //  Get the response status code e.g "200"
        $statusCode = $response->getStatusCode();

        //  Return the status and the body
        return [
            'status' => ($statusCode == 200),
            'body' => $bodyAsArray
        ];
    }

    /**
     *  Request the airtime billing usage consumption data.
     *  This helps us learn how much service consumption is available e.g
     *  The available airtime balance, sms and mobile data left that can be consumed.
     *
     *  @param string $msisdn - The MSISDN
     *  @param string $accessToken - The access token
     *
     *  @return array
     */
    public static function requestAirtimeBillingUsageConsumption($msisdn, $accessToken): array
    {
        try {

            //  Set the request endpoint
            $endpoint = config('app.ORANGE_BILLING_ENDPOINT').'/customer/usageConsumption/v1/usageConsumptionReport?publicKey='.$msisdn;

            //  Set the request options
            $options = [
                'headers' => [
                    'Authorization' => 'Bearer '.$accessToken,
                    'Content-type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'verify' => false,  // Disable SSL certificate verification
            ];

            //  Create a new Http Guzzle Client
            $httpClient = new Client();

            //  Perform and return the Http request
            $response = $httpClient->request('GET', $endpoint, $options);

        } catch (\GuzzleHttp\Exception\BadResponseException $e) {

            $response = $e->getResponse();

        } catch (\Throwable $e) {

            return [
                'status' => false,
                'body' => [
                    'message' => $e->getMessage()
                ]
            ];

        }

        /**
         *  Get the response body as a String.
         *
         *  On Success, the response payload is as follows:
         *
         *  Return the reponse body, the structure is as follows:
         *
         *  {
         *      "id": "2b778311-ab1b-4f9b-bdb7-e8f3632a6ca9",
         *      "effectiveDate": "2022-01-21T13:24:33+0000",
         *      "bucket": [
         *          {
         *              "id": "OCS-0",
         *              "name": "Main Balance",
         *              "usageType": "accountBalance",
         *              "bucketBalance": [
         *                  {
         *                      "unit": "BWP",
         *                      "remainingValue": 0,
         *                      "validFor": {
         *                           "startDateTime": "2019-04-04T00:00:00+0000",
         *                           "endDateTime": "2023-01-06T00:00:00+0000"
         *                       }
         *                   }
         *                ]
         *              },
         *          {
         *              "id": "OCS-2",
         *              "name": "On-Net",
         *              "usageType": "accountBalance",
         *              "bucketBalance": [
         *                  {
         *                      "unit": "BWP",
         *                      "remainingValue": 0,
         *                      "validFor": {
         *                           "startDateTime": "2022-01-02T12:54:34+0000",
         *                           "endDateTime": "2022-01-20T17:51:06+0000"
         *                          }
         *                      }
         *                  ]
         *              },
         *              {
         *              "id": "OCS-5",
         *              "name": "National SMS",
         *              "usageType": "sms",
         *              "bucketBalance": [
         *                  {
         *                      "unit": "SMS",
         *                      "remainingValue": 11,
         *                      "validFor": {
         *                           "startDateTime": "2019-04-07T00:00:00+0000",
         *                           "endDateTime": "2032-01-04T00:00:00+0000"
         *                          }
         *                      }
         *                  ]
         *              },
         *       ]
         *  }
         *
         *  On Fail, the response payload is as follows:
         *
         *  {
         *      "code": 4001,
         *      "message": "Missing parameter",
         *      "description": "Parameter publicKey is missing, null or empty"
         *  }
         */
        $jsonString = $response->getBody();

        /**
         *  Get the response body as an Associative Array:
         *
         *  [
         *      "id" => "2b778311-ab1b-4f9b-bdb7-e8f3632a6ca9",
         *      "effectiveDate" => "2022-01-21T13:24:33+0000",
         *      "bucket" => [
         *          [
         *              "id" => "OCS-0",
         *              "name" => "Main Balance",
         *              "usageType" => "accountBalance",
         *              "bucketBalance" => [
         *                  [
         *                      "unit" => "BWP",
         *                      "remainingValue" => 0,
         *                      "validFor" => [
         *                           "startDateTime" => "2019-04-04T00:00:00+0000",
         *                           "endDateTime" => "2023-01-06T00:00:00+0000"
         *                       ]
         *                   ]
         *                ]
         *              ],
         *          ],
         *          ...
         *      ]
         *  ]
         */
        $bodyAsArray = json_decode($jsonString, true);

        //  Get the response status code e.g "200"
        $statusCode = $response->getStatusCode();

        //  Return the status and the body
        return [
            'status' => ($statusCode == 200),
            'body' => $bodyAsArray
        ];
    }

    /**
     *  Request to bill the subscriber on the given amount
     *
     *  @param BillingTransaction $billingTransaction - The billing transaction Model
     *  @param string $msisdn - The MSISDN
     *  @param string $amount - The amount to be billed e.g 10.00
     *  @param string $onBehalfOf - Entity name to allow aggregator or acquiring partners to specify the actual payee
     *  @param string $productId - Combines with the onBehalfOf to uniquely identify the product being purchased
     *  @param string $purchaseCategoryCode - A category defining the content type. (This parameter MUST be filled with values validated by AAS integration team)
     *  @param string $description - The description of the transaction
     *  @param string $accessToken - The access token
     *
     *  @return array
     */
    public static function requestAirtimeBillingDeductFee($billingTransaction, $msisdn, $amount, $onBehalfOf, $productId, $purchaseCategoryCode, $description, $accessToken): array
    {
        try {

            //  Set the request endpoint
            $endpoint = config('app.ORANGE_BILLING_ENDPOINT').'/payment/v1/tel%3A%2B'.$msisdn.'/transactions/amount';

            $chargingMetaData = [];
            if(!empty($productId)) $chargingMetaData['productId'] = $productId;
            if(!empty($onBehalfOf)) $chargingMetaData['onBehalfOf'] = $onBehalfOf;
            if(!empty($purchaseCategoryCode)) $chargingMetaData['purchaseCategoryCode'] = $purchaseCategoryCode;

            //  Set the request options
            $options = [
                'headers' => [
                    'Authorization' => 'Bearer '.$accessToken,
                    'Content-type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'amountTransaction' => [
                        'endUserId' => 'tel:+'.$msisdn,
                        'paymentAmount' => [
                            'chargingInformation' => [
                                'amount' => (float) $amount,
                                'currency' => config('app.CURRENCY'),
                                'description' => [
                                    0 => $description,
                                ],
                            ],
                            'chargingMetaData' => $chargingMetaData
                        ],
                        'clientCorrelator' => $billingTransaction->id.'-'.now(),   //	'unique-technical-id',
                        'referenceCode' => 'referenceCode-'.now(),                 //	'Service_provider_payment_reference',
                        'transactionOperationStatus' => 'Charged',
                    ],
                ],
                'verify' => false,  // Disable SSL certificate verification
            ];

            //  Create a new Http Guzzle Client
            $httpClient = new Client();

            //  Perform and return the Http request
            $response = $httpClient->request('POST', $endpoint, $options);

        } catch (\GuzzleHttp\Exception\BadResponseException $e) {

            $response = $e->getResponse();

        } catch (\Throwable $e) {

            return [
                'status' => false,
                'body' => [
                    'message' => $e->getMessage()
                ]
            ];

        }

        /**
         *  Get the response body as a String.
         *
         *  On Success, the response payload is as follows:
         *
         *  Return the reponse body, the structure is as follows:
         *
         *  {
         *      "amountTransaction": {
         *          "endUserId": "tel:+ {MSISDN_WITH_COUNTRYCODE} ",
         *          "paymentAmount": {
         *              "chargingInformation": {
         *                  "amount": 5 ,
         *                  "currency": " XOF ",
         *                  "description": [
         *                      "Short description of the charge"
         *                  ]
         *              },
         *              "totalAmountCharged": 5 ,
         *              "chargingMetaData": {
         *                  "productId": " Daily_subscription ",
         *                  "serviceId": " Football_results ",
         *                  "purchaseCategoryCode": " Daily_autorenew_pack "
         *              }
         *          },
         *          "clientCorrelator": "unique-technical-id",
         *          "referenceCode": "Service_provider_payment_reference",
         *          "transactionOperationStatus": "Charged",
         *          "serverReferenceCode": "5b9bb0235c2dbe6d16d6b5b2",
         *          "resourceURL": "/payment/v1/tel%3A%2B {MSISDN_WITH_COUNTRYCODE} /transactions/amount/5b9bb0235c2dbe6d16d6b5b2",
         *          "link": []
         *      }
         *  }
         *
         *  On Fail, the response payload is as follows:
         *
         *  403 status (Policy error example):
         *
         *  {
         *      "requestError": {
         *          "policyException": {
         *              "messageId": " POL2206",
         *              "text": "User forbidden."
         *          }
         *      }
         *  }
         *
         *  409 status (Service error example):
         *
         *  {
         *      "requestError": {
         *          "serviceException": {
         *              "messageId": "SVC0005",
         *              "text": "duplicate correlatorId cc1d2d34",
         *              "variables": [
         *                  "cc1d2d34"
         *              ]
         *          }
         *      }
         *  }
         */
        $jsonString = $response->getBody();

        /**
         *  Get the response body as an Associative Array:
         *
         *  [
         *      "amountTransaction" => [
         *          "endUserId" => "tel:+ [MSISDN_WITH_COUNTRYCODE]",
         *          "paymentAmount" => [
         *              "chargingInformation" => [
         *                  "amount" => 5 ,
         *                  "currency" => "XOF",
         *                  "description" => [
         *                      "Short description of the charge"
         *                  ]
         *              ],
         *              "totalAmountCharged" => 5 ,
         *              "chargingMetaData" => [
         *                  "productId" => "Daily_subscription",
         *                  "serviceId" => "Football_results",
         *                  "purchaseCategoryCode" => "Daily_autorenew_pack "
         *              ]
         *          ],
         *          "clientCorrelator" => "unique-technical-id",
         *          "referenceCode" => "Service_provider_payment_reference",
         *          "transactionOperationStatus" => "Charged",
         *          "serverReferenceCode" => "5b9bb0235c2dbe6d16d6b5b2",
         *          "resourceURL" => "/payment/v1/tel%3A%2B [MSISDN_WITH_COUNTRYCODE] /transactions/amount/5b9bb0235c2dbe6d16d6b5b2",
         *          "link" => []
         *      ]
         *  ]
         */
        $bodyAsArray = json_decode($jsonString, true);

        //  Get the response status code e.g "201"
        $statusCode = $response->getStatusCode();

        //  Return the status and the body
        return [
            'status' => ($statusCode == 201),
            'body' => $bodyAsArray
        ];
    }
}
