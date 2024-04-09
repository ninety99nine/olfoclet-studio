<?php

namespace App\Services;

use App\Enums\MessageFailureType;
use App\Enums\MessageType;
use GuzzleHttp\Client;
use App\Models\Message;
use App\Models\Subscriber;
use App\Models\Pivots\SubscriberMessage;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     *  Send the Orange SMS
     *
     *  @param Project $project - The project Model
     *  @param Subscriber $subscriber - The subscriber Model
     *  @param Message|string $message - The message Model or message content to send
     *  @param MessageType $messageType - The Message Type
     *
     *  @return SubscriberMessage
     */
    public static function sendSms($project, $subscriber, $message, $messageType = MessageType::Content): SubscriberMessage
    {
        try {

            $messageType = $messageType;

            $messageId = $message instanceof Message ? $message->id : null;

            $content = $message instanceof Message ? $message->content : $message;

            //  Create the subscriber message record
            $subscriberMessage = SubscriberMessage::create([
                'subscriber_id' => $subscriber->id,
                'project_id' => $project->id,
                'message_id' => $messageId,
                'type' => $messageType,
                'content' => $content,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            try {

                //  Set the client credentials
                $clientCredentials = $project->settings['sms_client_credentials'];

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
                 *          "access_token" => "eyJ4NXQiOiJOalUzWWpJeE5qRTVObU0wWVRkbE1XRmhNVFEyWWpkaU1tUXdNemMwTmpreFkyTmlaRE0xTlRrMk9EaGxaVFkwT0RFNU9EZzBNREkwWlRreU9HRmxOZyIsImtpZCI6Ik5qVTNZakl4TmpFNU5tTTBZVGRsTVdGaE1UUTJZamRpTW1Rd016YzBOamt4WTJOaVpETTFOVGsyT0RobFpUWTBPREU1T0RnME1ESTBaVGt5T0dGbE5nX1JTMjU2IiwiYWxnIjoiUlMyNTYifQ.eyJzdWIiOiJPQldfSU5URUdSQVRJT05AY2FyYm9uLnN1cGVyIiwiYXV0IjoiQVBQTElDQVRJT04iLCJhdWQiOiJST2VHNFUxMXBhOUI4ZWludGVPUk5Mcjh1RWdhIiwibmJmIjoxNzEwNzA0OTk0LCJhenAiOiJST2VHNFUxMXBhOUI4ZWludGVPUk5Mcjh1RWdhIiwic2NvcGUiOiJhbV9hcHBsaWNhdGlvbl9zY29wZSBkZWZhdWx0IiwiaXNzIjoiaHR0cHM6XC9cL2Fhcy1idy1ndy5jb20uaW50cmFvcmFuZ2U6NDQzXC9vYXV0aDJcL3Rva2VuIiwiZXhwIjoxNzEwNzA4NTk0LCJpYXQiOjE3MTA3MDQ5OTQsImp0aSI6IjkzYzQ0OGRjLWQ5MzQtNDBhYi1iMDFjLWJhNWUxNDFjN2FjNyJ9.FRgZ1g5hLvj1hFra3DO4W_dnkdLHBy08Whc_Rh0vmouG27MmNoPWSwQrhkSr9n3ekyy7kyLXasi04-egx7xoQq_Dbxuml-PsOevPk0Jrt6INeZiNQoXkKcaZisHWKLFeuue_2m-8urXxEVYCs2GbKH0bEXx9FmrOgOjCbFv0z1hmIuWRaqdSdXFah8Ud4_u-McXI7y9RTL5pd-SUKJ9V9Ml0-7-P7XTGaJ-NJKEbbcX0X-AoMlxWkM-CAJ1aDlxLJGfdhteDr0WsRDSRqoqbaBcRKrnou4vXC7l13iRYpHtfn0cFTff2ZFx1DUiFA25bEpo9HrR21dt6Vxq4GH18wQ",
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
                 *          "error_description" => "Client Authentication failed.",
                 *          "error" => "invalid_client"
                 *      ]
                 *  ]
                 */
                $response = self::requestSmsAccessToken($clientCredentials);

                if($status = $response['status']) {

                    $recipientNumber = $subscriber->msisdn;
                    $clientCorrelator = $subscriberMessage->id;
                    $accessToken = $response['body']['access_token'];
                    $senderName = $project->settings['sms_sender_name'];
                    $senderNumber = $project->settings['sms_sender_number'];

                    /**
                     *  -----------------------
                     *  Request to send the SMS
                     *  -----------------------
                     *
                     *  On Success, the response payload is as follows:
                     *
                     *  [
                     *      "outboundSMSMessageRequest" => [
                     *          "address" => [
                     *              "tel: "+26772882239"
                     *          ],
                     *          "senderAddress" => "tel:+26777479083",
                     *          "senderName" => "Testing",
                     *          "outboundSMSTextMessage" => [
                     *              "message" => "This is a test SMS"
                     *          ],
                     *          "clientCorrelator" => "cf9d467d-2131-4280-b996-dddc5eb70eb2",
                     *          "resourceURL" => "/smsmessaging/v1/outbound/tel:+26777479083/requests/req65f74d24d046b122fc077f8c",
                     *          "link" => [
                     *              [
                     *                  "rel" => "Date",
                     *                  "href": "2024-03-17T20:05:56.566Z"
                     *              ]
                     *          ],
                     *          "deliveryInfoList" => [
                     *              "resourceURL" => "/smsmessaging/v1/outbound/tel:+26777479083/requests/req65f74d24d046b122fc077f8c/deliveryInfos",
                     *              "link" => [],
                     *              "deliveryInfo" => [
                     *                  [
                     *                      "address" => "tel:+26772882239",
                     *                      "deliveryStatus" => "MessageWaiting",
                     *                      "link" => []
                     *                  ]
                     *              ]
                     *          ]
                     *      ]
                     *  ]
                     *
                     *  On Fail, the response payload is as follows:
                     *
                     *  403 status:
                     *
                     *  {
                     *      "requestError": {
                     *          "serviceException": {
                     *              "messageId": "SVC0280",
                     *              "text": "Message too long. Maximum length is %1 characters",
                     *              "variables": [
                     *                  "1024"
                     *              ]
                     *          }
                     *      }
                     *  }
                     *
                     *  409 status:
                     *
                     *  {
                     *      "requestError": {
                     *          "serviceException": {
                     *              "messageId": "SVC0005",
                     *              "text": "duplicate correlatorId 1"
                     *          }
                     *      }
                     *  }
                     */
                    $response = self::requestSendSms($senderName, $senderNumber, $recipientNumber, $content, $clientCorrelator, $accessToken);

                    if($status = $response['status']) {

                        //  The SMS sending is successful at this point

                    }else{

                        $failureType = MessageFailureType::MessageSendingFailed;

                        if(isset($response['body']['requestError']) && isset($response['body']['requestError']['serviceException'])) {
                            $failureReason = $response['body']['requestError']['serviceException']['text'];
                        }

                        if(isset($response['body']['message'])) {
                            $failureReason = $response['body']['message'];
                        }

                    }

                }else{

                    $failureType = MessageFailureType::TokenGenerationFailed;

                    if(isset($response['body']['error_description'])) {
                        $failureReason = $response['body']['error_description'];
                    }

                }

                if($status == false && !isset($failureReason) && isset($response['body'])) {

                    /**
                     *  Final alternative to retrieve the error information in the event that we could not acquire the
                     *  information directly on the "requestError->serviceException->text" or the "error_description".
                     */
                    $failureReason = json_encode($response['body']);

                }

                //  Update the subscriber message record
                $subscriberMessage->update([
                    'is_successful' => $status,
                    'failure_reason' => isset($failureReason) ? $failureReason : null,
                    'failure_type' => isset($failureType) ? $failureType->value : null,
                    'delivery_status' => $status ? $response['body']['outboundSMSMessageRequest']['resourceURL'] : null,
                    'delivery_status_endpoint' => $status ? $response['body']['outboundSMSMessageRequest']['deliveryInfoList']['resourceURL'] : null,
                    'delivery_status' => $status ? $response['body']['outboundSMSMessageRequest']['deliveryInfoList']['deliveryInfo'][0]['deliveryStatus'] : null,
                ]);

                return $subscriberMessage;

            } catch (\Throwable $th) {

                $failureType = MessageFailureType::InternalFailure;

                $subscriberMessage->update([
                    'is_successful' => false,
                    'failure_type' => $failureType->value,
                    'failure_reason' => $th->getMessage()
                ]);

                //  Return a fresh instance of the subscriber message
                return $subscriberMessage->fresh();

            }

        } catch (\Throwable $th) {

            Log::error($th->getMessage());
        }
    }

    /**
     *  Request the Orange SMS Access Token
     *
     *  @param string $clientCredentials - The client credentials provided by the Mobile Network Operator
     *
     *  @return array
     */
    public static function requestSmsAccessToken($clientCredentials): array
    {
        try {

            //  Set the request endpoint
            $endpoint = config('app.ORANGE_SMS_ENDPOINT').'/token';

            //  Set the request options
            $options = [
                'headers' => [
                    'Authorization' => 'Basic '.$clientCredentials,
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ],
                'form_params' => [
                    'grant_type' => 'client_credentials'
                ],
                'verify' => false,  // Disable SSL certificate verification
            ];

            //  Create a new Http Guzzle Client
            $httpClient = new Client();

            //  Perform and return the Http request
            $response = $httpClient->request('POST', $endpoint, $options);

        } catch (\GuzzleHttp\Exception\BadResponseException $e) {

            $response = $e->getResponse();

        } catch (\GuzzleHttp\Exception\ConnectException $e) {

            return [
                'status' => false,
                'body' => [
                    'error_description' => $e->getMessage()
                ]
            ];

        } catch (\Throwable $th) {

            return [
                'status' => false,
                'body' => [
                    'error_description' => $th->getMessage()
                ]
            ];

        }

        /**
         *  Get the response body as a String.
         *
         *  On Success, the response payload is as follows:
         *
         *  {
         *      "access_token": "eyJ4NXQiOiJOalUzWWpJeE5qRTVObU0wWVRkbE1XRmhNVFEyWWpkaU1tUXdNemMwTmpreFkyTmlaRE0xTlRrMk9EaGxaVFkwT0RFNU9EZzBNREkwWlRreU9HRmxOZyIsImtpZCI6Ik5qVTNZakl4TmpFNU5tTTBZVGRsTVdGaE1UUTJZamRpTW1Rd016YzBOamt4WTJOaVpETTFOVGsyT0RobFpUWTBPREU1T0RnME1ESTBaVGt5T0dGbE5nX1JTMjU2IiwiYWxnIjoiUlMyNTYifQ.eyJzdWIiOiJPQldfSU5URUdSQVRJT05AY2FyYm9uLnN1cGVyIiwiYXV0IjoiQVBQTElDQVRJT04iLCJhdWQiOiJST2VHNFUxMXBhOUI4ZWludGVPUk5Mcjh1RWdhIiwibmJmIjoxNzEwNzA0OTk0LCJhenAiOiJST2VHNFUxMXBhOUI4ZWludGVPUk5Mcjh1RWdhIiwic2NvcGUiOiJhbV9hcHBsaWNhdGlvbl9zY29wZSBkZWZhdWx0IiwiaXNzIjoiaHR0cHM6XC9cL2Fhcy1idy1ndy5jb20uaW50cmFvcmFuZ2U6NDQzXC9vYXV0aDJcL3Rva2VuIiwiZXhwIjoxNzEwNzA4NTk0LCJpYXQiOjE3MTA3MDQ5OTQsImp0aSI6IjkzYzQ0OGRjLWQ5MzQtNDBhYi1iMDFjLWJhNWUxNDFjN2FjNyJ9.FRgZ1g5hLvj1hFra3DO4W_dnkdLHBy08Whc_Rh0vmouG27MmNoPWSwQrhkSr9n3ekyy7kyLXasi04-egx7xoQq_Dbxuml-PsOevPk0Jrt6INeZiNQoXkKcaZisHWKLFeuue_2m-8urXxEVYCs2GbKH0bEXx9FmrOgOjCbFv0z1hmIuWRaqdSdXFah8Ud4_u-McXI7y9RTL5pd-SUKJ9V9Ml0-7-P7XTGaJ-NJKEbbcX0X-AoMlxWkM-CAJ1aDlxLJGfdhteDr0WsRDSRqoqbaBcRKrnou4vXC7l13iRYpHtfn0cFTff2ZFx1DUiFA25bEpo9HrR21dt6Vxq4GH18wQ",
         *      "scope": "am_application_scope default",
         *      "token_type": "Bearer",
         *      "expires_in": 3600
         *  }
         *
         *  On Fail, the response payload is as follows:
         *
         *  {
         *      "error_description": "Client Authentication failed.",
         *      "error": "invalid_client"
         *  }
         */
        $jsonString = $response->getBody();

        /**
         *  Get the response body as an Associative Array:
         *
         *  [
         *      "access_token" => "eyJ4NXQiOiJOalUzWWpJeE5qRTVObU0wWVRkbE1XRmhNVFEyWWpkaU1tUXdNemMwTmpreFkyTmlaRE0xTlRrMk9EaGxaVFkwT0RFNU9EZzBNREkwWlRreU9HRmxOZyIsImtpZCI6Ik5qVTNZakl4TmpFNU5tTTBZVGRsTVdGaE1UUTJZamRpTW1Rd016YzBOamt4WTJOaVpETTFOVGsyT0RobFpUWTBPREU1T0RnME1ESTBaVGt5T0dGbE5nX1JTMjU2IiwiYWxnIjoiUlMyNTYifQ.eyJzdWIiOiJPQldfSU5URUdSQVRJT05AY2FyYm9uLnN1cGVyIiwiYXV0IjoiQVBQTElDQVRJT04iLCJhdWQiOiJST2VHNFUxMXBhOUI4ZWludGVPUk5Mcjh1RWdhIiwibmJmIjoxNzEwNzA0OTk0LCJhenAiOiJST2VHNFUxMXBhOUI4ZWludGVPUk5Mcjh1RWdhIiwic2NvcGUiOiJhbV9hcHBsaWNhdGlvbl9zY29wZSBkZWZhdWx0IiwiaXNzIjoiaHR0cHM6XC9cL2Fhcy1idy1ndy5jb20uaW50cmFvcmFuZ2U6NDQzXC9vYXV0aDJcL3Rva2VuIiwiZXhwIjoxNzEwNzA4NTk0LCJpYXQiOjE3MTA3MDQ5OTQsImp0aSI6IjkzYzQ0OGRjLWQ5MzQtNDBhYi1iMDFjLWJhNWUxNDFjN2FjNyJ9.FRgZ1g5hLvj1hFra3DO4W_dnkdLHBy08Whc_Rh0vmouG27MmNoPWSwQrhkSr9n3ekyy7kyLXasi04-egx7xoQq_Dbxuml-PsOevPk0Jrt6INeZiNQoXkKcaZisHWKLFeuue_2m-8urXxEVYCs2GbKH0bEXx9FmrOgOjCbFv0z1hmIuWRaqdSdXFah8Ud4_u-McXI7y9RTL5pd-SUKJ9V9Ml0-7-P7XTGaJ-NJKEbbcX0X-AoMlxWkM-CAJ1aDlxLJGfdhteDr0WsRDSRqoqbaBcRKrnou4vXC7l13iRYpHtfn0cFTff2ZFx1DUiFA25bEpo9HrR21dt6Vxq4GH18wQ",
         *      "scope" => "am_application_scope default",
         *      "token_type" => "Bearer",
         *      "expires_in" => 3600
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
     *  Request to send the Orange SMS
     *
     *  @param string $senderName - The sender name
     *  @param string $senderNumber - The sender mobile number
     *  @param string $recipientNumber - The recipient mobile number
     *  @param string $message - The message content to be sent
     *  @param string $clientCorrelator - Unique ID to identify this SMS message
     *  @param string $accessToken - The access token
     *
     *  @return array
     */
    public static function requestSendSms($senderName, $senderNumber, $recipientNumber, $message, $clientCorrelator, $accessToken): array
    {
        try {

            //  Set the request endpoint
            $endpoint = config('app.ORANGE_SMS_ENDPOINT').'/smsmessaging/v1/outbound/tel%3A%2B'.$senderNumber.'/requests';

            //  Set the request options
            $options = [
                'headers' => [
                    'Authorization' => 'Bearer '.$accessToken,
                    'Content-Type' => 'application/json',
                    'accept' => 'application/json'
                ],
                'json' => [
                    'outboundSMSMessageRequest' => [
                        'address' => ['tel:+'.$recipientNumber],        //  Recepient number to send the SMS message
                        'senderAddress' => 'tel:+'.$senderNumber,       //  Sender number that will be displayed if senderName is not included
                        'senderName' => $senderName,                    //  Sender name e.g "Company XYZ"
                        'outboundSMSTextMessage' => [
                            'message' => $message                       //  Message to be sent
                        ],
                        'clientCorrelator' => $clientCorrelator         // A unique id to identify this SMS message
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

        } catch (\GuzzleHttp\Exception\ConnectException $e) {

            return [
                'status' => false,
                'body' => [
                    'message' => $e->getMessage()
                ]
            ];

        } catch (\Throwable $th) {

            return [
                'status' => false,
                'body' => [
                    'message' => $th->getMessage()
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
         *      "outboundSMSMessageRequest": {
         *          "address": [
         *              "tel:+26772882239"
         *          ],
         *          "senderAddress": "tel:+26777479083",
         *          "senderName": "Testing",
         *          "outboundSMSTextMessage": {
         *              "message": "This is a test SMS"
         *          },
         *          "clientCorrelator": "cf9d467d-2131-4280-b996-dddc5eb70eb2",
         *          "resourceURL": "/smsmessaging/v1/outbound/tel:+26777479083/requests/req65f74d24d046b122fc077f8c",
         *          "link": [
         *              {
         *                  "rel": "Date",
         *                  "href": "2024-03-17T20:05:56.566Z"
         *              }
         *          ],
         *          "deliveryInfoList": {
         *              "resourceURL": "/smsmessaging/v1/outbound/tel:+26777479083/requests/req65f74d24d046b122fc077f8c/deliveryInfos",
         *              "link": [],
         *              "deliveryInfo": [
         *                  {
         *                      "address": "tel:+26772882239",
         *                      "deliveryStatus": "MessageWaiting",
         *                      "link": []
         *                  }
         *              ]
         *          }
         *      }
         *  }
         *
         *  On Fail, the response payload is as follows:
         *
         *  403 status:
         *
         *  {
         *      "requestError": {
         *          "serviceException": {
         *              "messageId": "SVC0280",
         *              "text": "Message too long. Maximum length is %1 characters",
         *              "variables": [
         *                  "1024"
         *              ]
         *          }
         *      }
         *  }
         *
         *  409 status:
         *
         *  {
         *      "requestError": {
         *          "serviceException": {
         *              "messageId": "SVC0005",
         *              "text": "duplicate correlatorId 1"
         *          }
         *      }
         *  }
         */
        $jsonString = $response->getBody();

        /**
         *  Get the response body as an Associative Array:
         *
         *  [
         *      "outboundSMSMessageRequest" => [
         *          "address" => [
         *              "tel: "+26772882239"
         *          ],
         *          "senderAddress" => "tel:+26777479083",
         *          "senderName" => "Testing",
         *          "outboundSMSTextMessage" => [
         *              "message" => "This is a test SMS"
         *          ],
         *          "clientCorrelator" => "cf9d467d-2131-4280-b996-dddc5eb70eb2",
         *          "resourceURL" => "/smsmessaging/v1/outbound/tel:+26777479083/requests/req65f74d24d046b122fc077f8c",
         *          "link" => [
         *              [
         *                  "rel" => "Date",
         *                  "href": "2024-03-17T20:05:56.566Z"
         *              ]
         *          ],
         *          "deliveryInfoList" => [
         *              "resourceURL" => "/smsmessaging/v1/outbound/tel:+26777479083/requests/req65f74d24d046b122fc077f8c/deliveryInfos",
         *              "link" => [],
         *              "deliveryInfo" => [
         *                  [
         *                      "address" => "tel:+26772882239",
         *                      "deliveryStatus" => "MessageWaiting",
         *                      "link" => []
         *                  ]
         *              ]
         *          ]
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
    /**
     *  Update the Subscriber Message delivery status
     *
     *  @param Project $project - The project Model
     *  @param SubscriberMessage $subscriberMessage - The SubscriberMessage Model
     *
     *  @return SubscriberMessage
     */
    public static function updateSmsDeliveryStatus($project, $subscriberMessage): SubscriberMessage
    {
        try {

            //  Set the client credentials
            $clientCredentials = $project->settings['sms_client_credentials'];

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
             *          "access_token" => "eyJ4NXQiOiJOalUzWWpJeE5qRTVObU0wWVRkbE1XRmhNVFEyWWpkaU1tUXdNemMwTmpreFkyTmlaRE0xTlRrMk9EaGxaVFkwT0RFNU9EZzBNREkwWlRreU9HRmxOZyIsImtpZCI6Ik5qVTNZakl4TmpFNU5tTTBZVGRsTVdGaE1UUTJZamRpTW1Rd016YzBOamt4WTJOaVpETTFOVGsyT0RobFpUWTBPREU1T0RnME1ESTBaVGt5T0dGbE5nX1JTMjU2IiwiYWxnIjoiUlMyNTYifQ.eyJzdWIiOiJPQldfSU5URUdSQVRJT05AY2FyYm9uLnN1cGVyIiwiYXV0IjoiQVBQTElDQVRJT04iLCJhdWQiOiJST2VHNFUxMXBhOUI4ZWludGVPUk5Mcjh1RWdhIiwibmJmIjoxNzEwNzA0OTk0LCJhenAiOiJST2VHNFUxMXBhOUI4ZWludGVPUk5Mcjh1RWdhIiwic2NvcGUiOiJhbV9hcHBsaWNhdGlvbl9zY29wZSBkZWZhdWx0IiwiaXNzIjoiaHR0cHM6XC9cL2Fhcy1idy1ndy5jb20uaW50cmFvcmFuZ2U6NDQzXC9vYXV0aDJcL3Rva2VuIiwiZXhwIjoxNzEwNzA4NTk0LCJpYXQiOjE3MTA3MDQ5OTQsImp0aSI6IjkzYzQ0OGRjLWQ5MzQtNDBhYi1iMDFjLWJhNWUxNDFjN2FjNyJ9.FRgZ1g5hLvj1hFra3DO4W_dnkdLHBy08Whc_Rh0vmouG27MmNoPWSwQrhkSr9n3ekyy7kyLXasi04-egx7xoQq_Dbxuml-PsOevPk0Jrt6INeZiNQoXkKcaZisHWKLFeuue_2m-8urXxEVYCs2GbKH0bEXx9FmrOgOjCbFv0z1hmIuWRaqdSdXFah8Ud4_u-McXI7y9RTL5pd-SUKJ9V9Ml0-7-P7XTGaJ-NJKEbbcX0X-AoMlxWkM-CAJ1aDlxLJGfdhteDr0WsRDSRqoqbaBcRKrnou4vXC7l13iRYpHtfn0cFTff2ZFx1DUiFA25bEpo9HrR21dt6Vxq4GH18wQ",
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
             *          "error_description" => "Client Authentication failed.",
             *          "error" => "invalid_client"
             *      ]
             *  ]
             */
            $response = self::requestSmsAccessToken($clientCredentials);

            if($status = $response['status']) {

                $accessToken = $response['body']['access_token'];

                /**
                 *  -----------------------
                 *  Request to send the SMS
                 *  -----------------------
                 *
                 *  On Success, the response payload is as follows:
                 *
                 *  [
                 *      "resourceURL" => "/smsmessaging/v1/outbound/tel:+26777479083/requests/req65f7eb8dd046b122fc0783da/deliveryInfos",
                 *      "link": [],
                 *      "deliveryInfo": [
                 *          [
                 *              "address" => "tel:+26772882239",
                 *              "deliveryStatus" => "DeliveredToTerminal",
                 *              "link": [
                 *                  [
                 *                      "rel" => "OutboundSMSMessageRequest",
                 *                      "href" => "/smsmessaging/v1/outbound/tel:+26777479083/requests/req65f7eb8dd046b122fc0783da"
                 *                  ],
                 *                  [
                 *                      "rel" => "Date",
                 *                      "href" => "2024-03-18T07:21:49.842282981Z"
                 *                  ]
                 *              ]
                 *          ]
                 *      ]
                 *  ]
                 *
                 *  On Fail, the response payload is as follows:
                 *
                 *  [
                 *      "requestError": [
                 *          "serviceException": [
                 *              "messageId" => "SVC0004",
                 *              "text" => "Resource not found",
                 *              "variables": [
                 *                  "req65f7eb8dd046b122fc0783db"
                 *              ]
                 *          ]
                 *      ]
                 *  ]
                 */
                $response = self::requestSmsDeliveryStatus($subscriberMessage, $accessToken);

                if($status = $response['status']) {

                    //  The SMS delivery status check is successful at this point

                }else{

                    if(isset($response['body']['requestError']) && isset($response['body']['requestError']['serviceException'])) {
                        $failureReason = $response['body']['requestError']['serviceException']['text'];
                    }

                    if(isset($response['body']['message'])) {
                        $failureReason = $response['body']['message'];
                    }

                }

            }else{

                if(isset($response['body']['error_description'])) {
                    $failureReason = $response['body']['error_description'];
                }

            }

            if($status == false && !isset($failureReason) && isset($response['body'])) {

                /**
                 *  Final alternative to retrieve the error information in the event that we could not acquire the
                 *  information directly on the "requestError->serviceException->text" or the "error_description".
                 */
                $failureReason = json_encode($response['body']);

            }

            //  Update the subscriber message record
            $subscriberMessage->update([
                'delivery_status_update_is_successful' => $status,
                'delivery_status_update_failure_reason' => isset($failureReason) ? $failureReason : null,
                'delivery_status' => $status ? $response['body']['deliveryInfo'][0]['deliveryStatus'] : $subscriberMessage->delivery_status,
            ]);

            return $subscriberMessage;

        } catch (\Throwable $th) {

            $subscriberMessage->update([
                'delivery_status_update_is_successful' => false,
                'delivery_status_update_failure_reason' => $th->getMessage()
            ]);

            return $subscriberMessage;

        }
    }

    /**
     *  Check the Orange SMS delivery status
     *
     *  @param SubscriberMessage $subscriberMessage - The SubscriberMessage Model
     *  @param string $accessToken - The access token
     *
     *  @return array
     */
    public static function requestSmsDeliveryStatus($subscriberMessage, $accessToken): array
    {
        try {

            //  Set the request endpoint
            $endpoint = config('app.ORANGE_SMS_ENDPOINT'). str_replace('tel:+','tel%3A%2B', $subscriberMessage->delivery_status_endpoint);

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

        } catch (\GuzzleHttp\Exception\ConnectException $e) {

            return [
                'status' => false,
                'body' => [
                    'message' => $e->getMessage()
                ]
            ];

        } catch (\Throwable $th) {

            return [
                'status' => false,
                'body' => [
                    'message' => $th->getMessage()
                ]
            ];

        }

        /**
         *  Get the response body as a String.
         *
         *  On Success, the response payload is as follows:
         *
         *  {
         *      "resourceURL": "/smsmessaging/v1/outbound/tel:+26777479083/requests/req65f7eb8dd046b122fc0783da/deliveryInfos",
         *      "link": [],
         *      "deliveryInfo": [
         *          {
         *              "address": "tel:+26772882239",
         *              "deliveryStatus": "DeliveredToTerminal",
         *              "link": [
         *                  {
         *                      "rel": "OutboundSMSMessageRequest",
         *                      "href": "/smsmessaging/v1/outbound/tel:+26777479083/requests/req65f7eb8dd046b122fc0783da"
         *                  },
         *                  {
         *                      "rel": "Date",
         *                      "href": "2024-03-18T07:21:49.842282981Z"
         *                  }
         *              ]
         *          }
         *      ]
         *  }
         *
         *  On Fail, the response payload is as follows:
         *
         *  {
         *      "requestError": {
         *          "serviceException": {
         *              "messageId": "SVC0004",
         *              "text": "Resource not found",
         *              "variables": [
         *                  "req65f7eb8dd046b122fc0783db"
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
         *      "resourceURL" => "/smsmessaging/v1/outbound/tel:+26777479083/requests/req65f7eb8dd046b122fc0783da/deliveryInfos",
         *      "link": [],
         *      "deliveryInfo": [
         *          [
         *              "address" => "tel:+26772882239",
         *              "deliveryStatus" => "DeliveredToTerminal",
         *              "link": [
         *                  [
         *                      "rel" => "OutboundSMSMessageRequest",
         *                      "href" => "/smsmessaging/v1/outbound/tel:+26777479083/requests/req65f7eb8dd046b122fc0783da"
         *                  ],
         *                  [
         *                      "rel" => "Date",
         *                      "href" => "2024-03-18T07:21:49.842282981Z"
         *                  ]
         *              ]
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
}
