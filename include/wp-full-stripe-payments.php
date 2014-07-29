<?php

//deals with calls to Stripe API
class MM_WPFSF_Stripe
{
    function charge($amount, $card, $description, $metadata = null, $stripeEmail = null)
    {
        $options = get_option('fullstripe_options_f');

        $charge = array(
            'card' => $card,
            'amount' => $amount,
            'currency' => $options['currency'],
            'description' => $description,
            'receipt_email' => $stripeEmail
        );

        if ($metadata)
            $charge['metadata'] = $metadata;

        $result = Stripe_Charge::create($charge);

        return $result;
    }
}