<?php

//deals with customer front-end input i.e. payment forms submission
class MM_WPFSF_Customer
{
    private $stripe = null;

    public function __construct()
    {
        $this->stripe = new MM_WPFSF_Stripe();
        $this->db = new MM_WPFSF_Database();
        $this->hooks();
    }

    private function hooks()
    {
        add_action('wp_ajax_wp_full_stripe_payment_charge', array($this, 'fullstripe_payment_charge'));
        add_action('wp_ajax_nopriv_wp_full_stripe_payment_charge', array($this, 'fullstripe_payment_charge'));
    }

    function fullstripe_payment_charge()
    {
        //get POST data from form
        $valid = true;
        $card = $_POST['stripeToken'];
        $name = sanitize_text_field($_POST['fullstripe_name']);
        $amount = $_POST['amount'];
        $formName = $_POST['formName'];
        $isCustom = $_POST['isCustom'];
        $doRedirect = $_POST['formDoRedirect'];
        $redirectPostID = $_POST['formRedirectPostID'];
        $showAddress = $_POST['showAddress'];
        $sendReceipt = $_POST['sendEmailReceipt'];
        $options = get_option('fullstripe_options_f');

        if ($isCustom == 1)
        {
            $amount = $_POST['fullstripe_custom_amount'];
            if (!is_numeric($amount))
            {
                $valid = false;
                $return = array('success' => false, 'msg' => __('The payment amount is invalid, please only use numbers and a decimal point', 'wp-full-stripe'));
            }
            else
            {
                $amount = $amount * 100; //Stripe expects amounts in cents
            }
        }

        $address1 = isset($_POST['fullstripe_address_line1']) ? sanitize_text_field($_POST['fullstripe_address_line1']) : '';
        $address2 = isset($_POST['fullstripe_address_line2']) ? sanitize_text_field($_POST['fullstripe_address_line2']) : '';
        $city = isset($_POST['fullstripe_address_city']) ? sanitize_text_field($_POST['fullstripe_address_city']) : '';
        $state = isset($_POST['fullstripe_address_state']) ? sanitize_text_field($_POST['fullstripe_address_state']) : '';
        $zip = isset($_POST['fullstripe_address_zip']) ? sanitize_text_field($_POST['fullstripe_address_zip']) : '';

        if ($showAddress == 1)
        {
            if ($address1 == '' || $city == '' || $zip == '')
            {
                $valid = false;
                $return = array('success' => false, 'msg' => __('Please enter a valid billing address', 'wp-full-stripe'));
            }
        }

        $email = 'n/a';
        if (isset($_POST['fullstripe_email']))
        {
           $email = $_POST['fullstripe_email'];
           if (!filter_var($email, FILTER_VALIDATE_EMAIL))
           {
               $valid = false;
               $return = array('success' => false, 'msg' => __('Please enter a valid email address', 'wp-full-stripe'));
           }
        }

        if ($valid)
        {
            $customInput = isset($_POST['fullstripe_custom_input']) ? $_POST['fullstripe_custom_input'] : 'n/a';
            $description = "Payment from $name on form: $formName \nCustom Data: $customInput";
            $metadata = array(
                'customer_name' => $name,
                'customer_email' => $email,
                'billing_address_line1' => $address1,
                'billing_address_line2' => $address2,
                'billing_address_city' => $city,
                'billing_address_state' => $state,
                'billing_address_zip' => $zip
            );

            try
            {
                //check email
                $sendPluginEmail = true;
                if ($sendReceipt == 1 && isset($_POST['fullstripe_email']))
                {
                    $sendPluginEmail = false;
                }

                //try the charge
                do_action('fullstripe_before_payment_charge', $amount);
                $result = $this->stripe->charge($amount, $card, $description, $metadata,($sendPluginEmail==false ? $email : null));
                do_action('fullstripe_after_payment_charge', $result);

                //save the payment
                $address = array('line1' => $address1, 'line2' => $address2, 'city' => $city, 'state' => $state, 'zip' => $zip);
                $this->db->fullstripe_insert_payment($result, $address);

                $return = array('success' => true, 'msg' => 'Payment Successful!');
                if ($doRedirect == 1)
                {
                    $return['redirect'] = true;
                    $return['redirectURL'] = get_page_link($redirectPostID);
                }

            }
            catch (Exception $e)
            {
                //show notification of error
                $return = array('success' => false, 'msg' => __('There was an error processing your payment: ', 'wp-full-stripe') . $e->getMessage());
            }
        }

        //correct way to return JS results in wordpress
        header("Content-Type: application/json");
        echo json_encode(apply_filters('fullstripe_payment_charge_return_message', $return));
        exit;
    }
}