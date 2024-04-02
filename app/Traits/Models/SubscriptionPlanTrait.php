<?php

namespace App\Traits\Models;

use App\Models\Subscription;

trait SubscriptionPlanTrait
{
    public function craftInsufficientFundsMessage()
    {
        return $this->handleEmbeddedDynamicContentConversion($this->insufficient_funds_message, [
            'subscriptionPlanName' => $this->name
        ]);
    }

    public function craftSuccessfulPaymentSmsMessage(Subscription $subscription, Subscription $subscriptionWithFurthestEndAt)
    {
        return $this->handleEmbeddedDynamicContentConversion($this->successful_payment_sms_message, [
            'subscriptionId' => $subscription->id,
            'subscriptionPlanName' => $this->name,
            'subscriptionPlanPrice' => $this->price->amount_with_currency,
            'subscriptionEndDate' => $subscription->end_at->format('d M Y H:i'),
            'subscriptionStartDate' => $subscription->start_at->format('d M Y H:i'),
            'nextBillableDate' => $subscriptionWithFurthestEndAt->end_at->format('d M Y H:i'),
        ]);
    }

    public function craftNextAutoBillingReminderSmsMessage(Subscription $subscriptionWithFurthestEndAt)
    {
        return $this->handleEmbeddedDynamicContentConversion($this->next_auto_billing_reminder_sms_message, [
            'subscriptionPlanName' => $this->name,
            'nextBillableDate' => $subscriptionWithFurthestEndAt->end_at->format('d M Y H:i'),
        ]);
    }

    /**
     *  Convert mustache tags embedded within the given string into their corresponding
     *  matching dynamic values. The final value returned is always of type String.
     */
    public function handleEmbeddedDynamicContentConversion($text = '', $variables = [])
    {
        //  Remove the (\u00a0) special character which represents a no-break space in HTML
        $text = $this->remove_HTML_No_Break_Space($text);

        //  Get all instances of mustache tags within the given text
        $result = $this->getInstancesOfMustacheTags($text);

        //  Get the total number of mustache tags found within the given text
        $numberOfMustacheTags = $result['total'];

        //  Get the mustache tags found within the given text
        $mustacheTags = $result['mustacheTags'];

        //  If we managed to detect one or more mustache tags
        if ($numberOfMustacheTags) {

            //  Foreach mustache tag we must convert it into a php variable
            foreach ($mustacheTags as $mustacheTag) {

                //  Convert "{{ company.name }}" into "$company->name"
                $dynamicVariable = $this->convertMustacheTagIntoPHPVariable($mustacheTag);

                //  Convert the dynamic property into its dynamic value e.g "$company->name" into "Company XYZ"
                $output = $this->processPHPCode("return $dynamicVariable;", $variables);

                //  Convert the output into a String format
                $output = $this->convertToString($output);

                //  Replace the mustache tag with its dynamic data e.g replace "{{ company.name }}" with "Company XYZ"
                $text = preg_replace("/$mustacheTag/", $output, $text);

            }
        }

        //  Return the converted text
        return $text;
    }

    /**
     *  Remove the (\u00a0) special character which represents a no-break space in HTML.
     *  This can cause issues since it can make valid mustache tags look invalid
     *  e.g convert "{{ \u00a0users }}" into "{{ users }}".
     */
    public function remove_HTML_No_Break_Space($text = '')
    {
        return preg_replace('/\xc2\xa0/', '', $text);
    }

    public function getInstancesOfMustacheTags($text = '')
    {
        /**
         *  Detect Dynamic Variables
         *
         *  Pattern Meaning:.
         *
         *  [{]{2} = The string must have exactly 2 opening curly braces e.g {{ not that "{{{" or "({{" or "[{{" will also pass
         *
         *  [\s]* = The string may have zero or more occurences of spaces e.g "{{company" or "{{ company" or "{{   company"
         *
         *  [a-zA-Z_]{1} = The first character at this point must be a lowercase or uppercase alphabet or an underscrore (_)
         *                 e.g "{{ c" or "{{ company" or "{{ _company" but deny "{{ 123" or "{{ 123_company" e.t.c
         *
         *  [a-zA-Z0-9_\.]{0,} = After the first character the string may have zero or more occurances of lowercase or uppercase
         *             alphabets, numbers, underscores (_) and periods (.) e.g "{{ company_123" or "{{ company.name" e.t.c
         *
         *  [\s]* = The string may have zero or more occurences of spaces afterwards "{{ company" or "{{ company   " e.t.c
         *
         *  [}]{2} = The string must end with exactly 2 closing curly braces e.g }} not that "}}}" or "}})" or "}}]" will also pass
         */
        $pattern = "/[{]{2}[\s]*[a-zA-Z_]{1}[a-zA-Z0-9_\.]{0,}[\s]*[}]{2}/";

        $totalResults = preg_match_all($pattern, $text, $results);

        /**
         * The "$totalResults" represents the number of matched mustache tags e.g
         *
         * $totalResults = 3;
         *
         * The "$results[0]" represents an array of the matched mustache tags
         *
         * $results[0] = [
         *      "{{ company.name }}",
         *      "{{ company.branches.total }}",
         *      "{{ company.details.contacts.phone }}",
         *      ... e.t.c
         *  ];
         */
        return ['total' => $totalResults, 'mustacheTags' => $results[0]];
    }

    /**
     *  Convert the given mustache tag into a valid PHP variable e.g
     *
     *  1) {{ users }} into $users
     *  2) {{ product.id }} into $product->id.
     *
     *  Note that adding the "$" sign to the variable name is optional
     */
    public function convertMustacheTagIntoPHPVariable($text = null)
    {
        //  If the text has been provided and is type of (String)
        if (!empty($text) && is_string($text)) {

            //  Remove any HTML or PHP tags
            $text = strip_tags($text);

            //  Replace all curly braces and spaces with nothing e.g convert "{{ company.name }}" into "company.name"
            $text = preg_replace("/[{}\s]*/", '', $text);

            //  Replace one or more occurences of the period with "->" e.g convert "company..name" or "company...name" into "company->name"
            $text = preg_replace("/[\.]+/", '->', $text);

            //  Remove left and right spaces (If Any)
            $text = trim($text);

            //  Append the $ sign to the begining of the result e.g convert "company->name" into "$company->name"
            $text = '$'.$text;

            //  Return the converted text
            return $text;

        }

        return null;
    }

    public function processPHPCode($phpCode = 'return null', $variables = [])
    {
        //  Create dynamic variables
        foreach ($variables as $key => $value) {

            /**
             *  Foreach dataset use the iterator key to create the dynamic variable name and
             *  assign the iterator value as the new variable value.
             *
             *  Example:
             *
             *  $variables = ['subscriptionPlan' => {...}, 'subscription' => {...}, ...e.tc];
             *
             *  Foreach dataset, we produce dynamic variables e.g
             *
             *  $subscriptionPlan = {...};
             *  $subscription = {...};
             *  ... e.t.c
             */
            ${$key} = $value;

        }

        try {

            return eval($phpCode);

        } catch (\Throwable $th) {

            return '???';

        }
    }

    public function convertToString($data = '')
    {
        //  If the given data is not a string
        if (!is_string($data)) {

            //  If the data is an array, object or bool
            if (is_array($data) || is_object($data) || is_bool($data)) {

                $data = json_encode($data);

                //  If failed
                if($data == false) {

                    return '???';

                }

            }else{

                //  Cast data into a string format
                $data = (string) $data;

            }

        }

        return $data;
    }
}
