<?php

//deals with admin back-end input i.e. create plans, transfers
class MM_WPFSF_Admin
{
    private $stripe = null;
    private $db = null;

    public function __construct()
    {
        $this->stripe = new MM_WPFSF_Stripe();
        $this->db = new MM_WPFSF_Database();
        $this->hooks();
    }

    private function hooks()
    {
        add_action('wp_ajax_wp_full_stripe_create_payment_form', array($this, 'fullstripe_create_payment_form_post'));
        add_action('wp_ajax_wp_full_stripe_edit_payment_form', array($this, 'fullstripe_edit_payment_form_post'));
        add_action('wp_ajax_wp_full_stripe_update_settings', array($this, 'fullstripe_update_settings_post'));
        add_action('wp_ajax_wp_full_stripe_delete_payment_form', array($this, 'fullstripe_delete_payment_form'));
    }

    function fullstripe_create_payment_form_post()
    {
        $name = $_POST['form_name'];
        $title = $_POST['form_title'];
        $amount = isset($_POST['form_amount']) ? $_POST['form_amount'] : '0';
        $custom = $_POST['form_custom'];
        $buttonTitle = $_POST['form_button_text'];
        $showButtonAmount = $_POST['form_button_amount'];
        $showEmailInput = $_POST['form_show_email_input'];
        $showCustomInput = $_POST['form_include_custom_input'];
        $customInputTitle = isset($_POST['form_custom_input_label']) ? $_POST['form_custom_input_label'] : '';
        $doRedirect = $_POST['form_do_redirect'];
        $redirectPostID = isset($_POST['form_redirect_post_id']) ? $_POST['form_redirect_post_id'] : 0;
        $showAddressInput = $_POST['form_show_address_input'];
        $sendEmailReceipt = isset($_POST['form_send_email_receipt']) ? $_POST['form_send_email_receipt'] : 0;
        $formStyle = $_POST['form_style'];

        $data = array(
            'name' => $name,
            'formTitle' => $title,
            'amount' => $amount,
            'customAmount' => $custom,
            'buttonTitle' => $buttonTitle,
            'showButtonAmount' => $showButtonAmount,
            'showEmailInput' => $showEmailInput,
            'showCustomInput' => $showCustomInput,
            'customInputTitle' => $customInputTitle,
            'redirectOnSuccess' => $doRedirect,
            'redirectPostID' => $redirectPostID,
            'showAddress' => $showAddressInput,
            'sendEmailReceipt' => $sendEmailReceipt,
            'formStyle' => $formStyle
        );

        $this->db->insert_payment_form($data);

        header("Content-Type: application/json");
        echo json_encode(array('success' => true));
        exit;
    }

    function fullstripe_edit_payment_form_post()
    {
        $id = $_POST['formID'];
        $name = $_POST['form_name'];
        $title = $_POST['form_title'];
        $amount = isset($_POST['form_amount']) ? $_POST['form_amount'] : '0';
        $custom = $_POST['form_custom'];
        $buttonTitle = $_POST['form_button_text'];
        $showButtonAmount = $_POST['form_button_amount'];
        $showEmailInput = $_POST['form_show_email_input'];
        $showCustomInput = $_POST['form_include_custom_input'];
        $customInputTitle = isset($_POST['form_custom_input_label']) ? $_POST['form_custom_input_label'] : '';
        $doRedirect = $_POST['form_do_redirect'];
        $redirectPostID = isset($_POST['form_redirect_post_id']) ? $_POST['form_redirect_post_id'] : 0;
        $showAddressInput = $_POST['form_show_address_input'];
        $sendEmailReceipt = isset($_POST['form_send_email_receipt']) ? $_POST['form_send_email_receipt'] : 0;
        $formStyle = $_POST['form_style'];

        $data = array(
            'name' => $name,
            'formTitle' => $title,
            'amount' => $amount,
            'customAmount' => $custom,
            'buttonTitle' => $buttonTitle,
            'showButtonAmount' => $showButtonAmount,
            'showEmailInput' => $showEmailInput,
            'showCustomInput' => $showCustomInput,
            'customInputTitle' => $customInputTitle,
            'redirectOnSuccess' => $doRedirect,
            'redirectPostID' => $redirectPostID,
            'showAddress' => $showAddressInput,
            'sendEmailReceipt' => $sendEmailReceipt,
            'formStyle' => $formStyle
        );

        $this->db->update_payment_form($id, $data);

        header("Content-Type: application/json");
        echo json_encode(array('success' => true, 'redirectURL' => admin_url('admin.php?page=fullstripe-payments-f')));
        exit;
    }

    function fullstripe_update_settings_post()
    {
        // Save the posted value in the database
        $options = get_option('fullstripe_options_f');
        $options['publishKey_test'] = trim($_POST['publishKey_test']);
        $options['secretKey_test'] = trim($_POST['secretKey_test']);
        $options['publishKey_live'] = trim($_POST['publishKey_live']);
        $options['secretKey_live'] = trim($_POST['secretKey_live']);
        $options['apiMode'] = $_POST['apiMode'];
        $options['currency'] = $_POST['currency'];
        $options['includeStyles'] = $_POST['includeStyles'];

        update_option('fullstripe_options_f', $options);

        header("Content-Type: application/json");
        echo json_encode(array('success' => true));
        exit;
    }

    function fullstripe_delete_payment_form()
    {
        $id = $_POST['id'];
        do_action('fullstripe_admin_delete_payment_form_action', $id);

        $this->db->delete_payment_form($id);

        header("Content-Type: application/json");
        echo json_encode(array('success' => true));
        exit;
    }
}
