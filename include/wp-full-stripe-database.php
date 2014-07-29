<?php

class MM_WPFSF_Database
{
    const paymentsTable = 'fullstripe_payments';
    const paymentFormsTable = 'fullstripe_payment_forms';

    public static function fullstripe_setup_db()
    {
        //require for dbDelta()
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        global $wpdb;

        $table = $wpdb->prefix . self::paymentsTable;

        $sql = "CREATE TABLE " . $table . " (
        paymentID INT NOT NULL AUTO_INCREMENT,
        eventID VARCHAR(100) NOT NULL,
        description VARCHAR(255) NOT NULL,
        paid TINYINT(1),
        livemode TINYINT(1),
        amount INT NOT NULL,
        fee INT NOT NULL,
        addressLine1 VARCHAR(500) NOT NULL,
        addressLine2 VARCHAR(500) NOT NULL,
        addressCity VARCHAR(500) NOT NULL,
        addressState VARCHAR(255) NOT NULL,
        addressZip VARCHAR(100) NOT NULL,
        addressCountry VARCHAR(100) NOT NULL,
        created DATETIME NOT NULL,
        UNIQUE KEY paymentID (paymentID)
        );";

        //database write/update
        dbDelta($sql);

        $table = $wpdb->prefix . self::paymentFormsTable;

        $sql = "CREATE TABLE " . $table . " (
        paymentFormID INT NOT NULL AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        formTitle VARCHAR(100) NOT NULL,
        amount INT NOT NULL,
        customAmount TINYINT(1) DEFAULT '0',
        buttonTitle VARCHAR(100) NOT NULL DEFAULT 'Make Payment',
        showButtonAmount TINYINT(1) DEFAULT '1',
        showEmailInput TINYINT(1) DEFAULT '0',
        showCustomInput TINYINT(1) DEFAULT '0',
        customInputTitle VARCHAR(100) NOT NULL DEFAULT 'Extra Information',
        redirectOnSuccess TINYINT(1) DEFAULT '0',
        redirectPostID INT(5) DEFAULT 0,
        showAddress TINYINT(1) DEFAULT '0',
        sendEmailReceipt TINYINT(1) DEFAULT '0',
        formStyle INT(5) DEFAULT 0,
        UNIQUE KEY paymentFormID (paymentFormID)
        );";

        //database write/update
        dbDelta($sql);

        //default form
        $defaultPaymentForm = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "fullstripe_payment_forms" . " WHERE name='default';");
        if ($defaultPaymentForm === null)
        {
            $data = array(
                'name' => 'default',
                'formTitle' => 'Payment',
                'amount' => 1000 //$10.00
            );
            $formats = array('%s', '%s', '%d');
            $wpdb->insert($wpdb->prefix . self::paymentFormsTable, $data, $formats);
        }

        do_action('fullstripe_setup_db');
    }

    function fullstripe_insert_payment($payment, $address)
    {
        global $wpdb;

        $data = array(
            'eventID' => $payment->id,
            'description' => $payment->description,
            'paid' => $payment->paid,
            'livemode' => $payment->livemode,
            'amount' => $payment->amount,
            'fee' => $payment->fee,
            'addressLine1' => $address['line1'],
            'addressLine2' => $address['line2'],
            'addressCity' => $address['city'],
            'addressState' => $address['state'],
            'addressZip' => $address['zip'],
            'created' => date('Y-m-d H:i:s', $payment->created)
        );

        $wpdb->insert($wpdb->prefix . self::paymentsTable, apply_filters('fullstripe_insert_payment_data', $data));
    }

    function insert_payment_form($form)
    {
        global $wpdb;
        $wpdb->insert($wpdb->prefix . self::paymentFormsTable, $form);
    }

    function update_payment_form($id, $form)
    {
        global $wpdb;
        $wpdb->update($wpdb->prefix . self::paymentFormsTable, $form, array('paymentFormID' => $id));
    }

    function delete_payment_form($id)
    {
        global $wpdb;
        $wpdb->query('DELETE FROM ' . $wpdb->prefix . self::paymentFormsTable . " WHERE paymentFormID='" . $id . "';");
    }

    function get_payment_form_by_name($name)
    {
        global $wpdb;
        return $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . self::paymentFormsTable . " WHERE name='" . $name . "';");
    }
}
