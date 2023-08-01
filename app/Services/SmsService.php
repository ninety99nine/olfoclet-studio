<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Models\Message;
use App\Models\Subscriber;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     *  Send the Orange SMS
     *
     *  @param Message $message - The message Model
     *  @param Subscriber $subscriber - The message Model
     *  @param string $senderName - The name of the sender sending the sms e.g Company XYZ
     *  @param string $senderNumber - The number of the sender sending the sms e.g 26772000001
     *  @param string $recipientNumber - The number of the recipient to receive the sms e.g 26772000001
     *  @param $clientCredentials - The client credentials used for authentication (Provided by Orange BW)
     */
    public static function sendSms($message, $subscriber, $senderName, $senderNumber, $clientCredentials): bool
    {
        try {

            //  Acquire the access token
            $accessToken = self::requestSmsAccessToken($clientCredentials);

            //  If we have an acccess token
            if($accessToken) {

                $httpClient = new Client();

                //  Get recipient number
                $recipientNumber = $subscriber->msisdn;

                /**
                 *  Note the following:
                 *
                 *  To test sending sms using POSTMAN then replace "https://aas-bw.com.intraorange:443" with "https://aas.orange.co.bw:443".
                 *  The "https://aas-bw.com.intraorange:443" domain is used to send SMS while the application is hosted on the Orange Server
                 *  The "https://aas.orange.co.bw:443" domain is used to send SMS while the application is hosted outside the Orange Server
                 *  such as on a local machine (Macbook, e.t.c) or POSTMAN. Since this application will be hosted on the Orange Server, we
                 *  will use the "https://aas-bw.com.intraorange:443" domain.
                 *
                 *  Note that "tel:+" converts to "tel%3A%2B" after being encoded
                 */
                $smsEndpoint = 'https://aas-bw.com.intraorange:443/smsmessaging/v1/outbound/tel%3A%2B'.$senderNumber.'/requests';

                /**
                 *  Sample Response:
                 *
                 * {
                 *      "outboundSMSMessageRequest": {
                 *      "address": [
                 *           "tel:+26772012345"
                 *      ],
                 *      "senderAddress": "tel:+26772012345",
                 *      "senderName": "Company XYZ",
                 *      "outboundSMSTextMessage": {
                 *          "message": "Welcome to Company XYZ"
                 *      },
                 *      "clientCorrelator": "cf9d467d-2131-4280-b996-dddc5eb70eb2",
                 *      "resourceURL": "/smsmessaging/v1/outbound/tel:+26772012345/requests/req64c2c5261bc1c442747dd2ff",
                 *      "link": [
                 *          {
                 *               "rel": "Date",
                 *               "href": "2023-07-27T19:27:34.612Z"
                 *          }
                 *      ],
                 *      "deliveryInfoList": {
                 *          "resourceURL": "/smsmessaging/v1/outbound/tel:+26772012345/requests/req64c2c5261bc1c442747dd2ff/deliveryInfos",
                 *          "link": [],
                 *          "deliveryInfo": [
                 *              {
                 *                  "address": "tel:+26772012345",
                 *                  "deliveryStatus": "MessageWaiting",
                 *                  "link": []
                 *              }
                 *          ]
                 *      }
                 *  }
                 * }
                 */
                $form = [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $accessToken,
                        'Content-Type' => 'application/json',
                        'accept' => 'application/json'
                    ],
                    'json' => [
                        'outboundSMSMessageRequest' => [
                            'address' => ['tel:+'.$recipientNumber],        //  Recepient number to send the SMS message
                            'senderAddress' => 'tel:+'.$senderNumber,       //  Sender number that will be displayed if senderName is not included
                            'senderName' => $senderName,                    //  Sender name e.g "Company XYZ"
                            'outboundSMSTextMessage' => [
                                'message' => $message->content
                            ],
                            'clientCorrelator' => $subscriber->id.'-'.$message->id.'-'.now()

                            // A unique id to identify this SMS
                        ]
                    ],
                    'verify' => false,  // Disable SSL certificate verification
                ];

                Log::channel('slack')->info(json_encode($form));

                $response = $httpClient->request('POST', $smsEndpoint, $form);

                $jsonString = $response->getBody();
                $statusCode = $response->getStatusCode();

                Log::channel('slack')->info($statusCode);
                Log::channel('slack')->info($jsonString);

                // Handle the response as needed
                if ($statusCode === 201) {

                    /// Return true that we succeeded to send the SMS
                    return true;

                }else{

                    // Send error report here
                    Log::channel('slack')->error('Sms sending failed (status '.$statusCode.') - '. $jsonString);

                }
            }

            // The sms failed to send
            return false;

        } catch (\Throwable $th) {

            // Send error report here
            Log::channel('slack')->error('Sms sending failed: '. $th->getMessage());

            // The sms failed to send
            return false;

        }
    }

    /**
     *  Request the Orange SMS Access Token
     */
    public static function requestSmsAccessToken($clientCredentials): string|bool
    {
        try {

            $httpClient = new Client();

            /**
             *  Note the following:
             *
             *  To test sending sms using POSTMAN then replace "https://aas-bw.com.intraorange:443" with "https://aas.orange.co.bw:443".
             *  The "https://aas-bw.com.intraorange:443" domain is used to send SMS while the application is hosted on the Orange Server
             *  The "https://aas.orange.co.bw:443" domain is used to send SMS while the application is hosted outside the Orange Server
             *  such as on a local machine (Macbook, e.t.c) or POSTMAN. Since this application will be hosted on the Orange Server, we
             *  will use the "https://aas-bw.com.intraorange:443" domain
             */
            $tokenEndpoint = 'https://aas-bw.com.intraorange:443/token';

            /**
             *  Sample Response:
             *
             *  {
             *      "access_token": "eyJ4NXQiOiJOalUzWWpJeE5qRTVObU0wWVRkbE1XRmhNVFEyWWpkaU1tUXdNemMwTmpreFkyTmlaRE0xTlRrMk9EaGxaVFkwT0RFNU9EZzBNREkwWlRreU9HRmxOZyIsImtpZCI6Ik5qVTNZakl4TmpFNU5tTTBZVGRsTVdGaE1UUTJZamRpTW1Rd016YzBOamt4WTJOaVpETTFOVGsyT0RobFpUWTBPREU1T0RnME1ESTBaVGt5T0dGbE5nX1JTMjU2IiwiYWxnIjoiUlMyNTYifQ.eyJzdWIiOiJPQldfSU5URUdSQVRJT05AY2FyYm9uLnN1cGVyIiwiYXV0IjoiQVBQTElDQVRJT04iLCJhdWQiOiJST2VHNFUxMXBhOUI4ZWludGVPUk5Mcjh1RWdhIiwibmJmIjoxNjkwNDY1MzY5LCJhenAiOiJST2VHNFUxMXBhOUI4ZWludGVPUk5Mcjh1RWdhIiwic2NvcGUiOiJhbV9hcHBsaWNhdGlvbl9zY29wZSBkZWZhdWx0IiwiaXNzIjoiaHR0cHM6XC9cL2Fhcy1idy1ndy5jb20uaW50cmFvcmFuZ2U6NDQzXC9vYXV0aDJcL3Rva2VuIiwiZXhwIjoxNjkwNDY4OTY5LCJpYXQiOjE2OTA0NjUzNjksImp0aSI6Ijg1ZDk2ZGJmLTNjYTAtNGEyMS05NzAwLWFlNGNlMTYzMDRjNiJ9.fFSjVkPWfxdLpYAmF86tGZInSI65Wtwz1sDYuQ_9QxHilqU2hUi5bJHB6Iw3cQepayJeY4899RLQ10H27YV9-P1zcVO_DJsiKA1itMZqcdwI5zMjmtOyJ7hbbACWLNXui4wYkuhWP2PhV3YgenB3wcNHIHtt-6dz4p4OIEkL22dmr_g5d6T-eBR3JLqGtP2ijyAfxxuS0brF6clEF04m2XzzE_RH4YoFzLvQPA56cuD45uMsNodhsK7D4f4xLOKyDiLjzXxwrnPuEgzsLp8LrZYmFgNRasLvdbazJFeOmZY9DrPk0vtYD93Bjb3nEmH5Mdgv4PsxoN_medTJdJ6Efw",
             *      "scope": "am_application_scope default",
             *      "token_type": "Bearer",
             *      "expires_in": 3600
             *  }
             *
             */
            $form = [
                'headers' => [
                    'Authorization' => 'Basic '.$clientCredentials,
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ],
                'form_params' => [
                    'grant_type' => 'client_credentials'
                ],
                'verify' => false,  // Disable SSL certificate verification
            ];

            Log::channel('slack')->info(json_encode($form));

            $response = $httpClient->request('POST', $tokenEndpoint, $form);

            $jsonString = $response->getBody();
            $statusCode = $response->getStatusCode();
            $responseData = json_decode($jsonString, true);

            Log::channel('slack')->info($statusCode);
            Log::channel('slack')->info($jsonString);

            // Handle the response as needed
            if ($statusCode === 200) {

                /// Return the access token
                return $responseData['access_token'];

            }else{

                // Send error report here
                Log::channel('slack')->error('Sms token creation failed (status '.$statusCode.') - '. $jsonString);

            }

            // The sms failed to send
            return false;

        } catch (\Throwable $th) {

            // Send error report here
            Log::channel('slack')->error('Sms token creation failed: '. $th->getMessage());

            // The sms failed to send
            return false;

        }
    }
}
