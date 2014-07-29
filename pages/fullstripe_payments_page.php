<?php
global $wpdb;
//get the data we need
$paymentForms = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "fullstripe_payment_forms;");
$options = get_option('fullstripe_options_f');
$stripeLink = "<a href='https://manage.stripe.com/";

$currencySymbol = '$';
if ( $options['currency'] === 'eur' )
{
    $currencySymbol = '€';
}
elseif ( $options['currency'] === 'gbp' )
{
    $currencySymbol = '£';
}

$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'payments';

?>
<div class="wrap">
    <h2> <?php echo __('Full Stripe Payments', 'wp-full-stripe'); ?> </h2>
    <div id="updateDiv"><p><strong id="updateMessage"></strong></p></div>

    <h2 class="nav-tab-wrapper">
        <a href="?page=fullstripe-payments-f&tab=payments" class="nav-tab <?php echo $active_tab == 'payments' ? 'nav-tab-active' : ''; ?>">Payments</a>
        <a href="?page=fullstripe-payments-f&tab=forms" class="nav-tab <?php echo $active_tab == 'forms' ? 'nav-tab-active' : ''; ?>">Payment Forms</a>
        <a href="?page=fullstripe-payments-f&tab=create" class="nav-tab <?php echo $active_tab == 'create' ? 'nav-tab-active' : ''; ?>">Create New Form</a>
    </h2>

    <div class="tab-content">
    <?php if ($active_tab == 'payments'): ?>
        <div class="" id="payments">
            <p class="alert alert-info">Here you will find your received payments. If you'd like to view more information please visit your
                <a href="https://manage.stripe.com">Stripe dashboard</a></p>
            <?php $table->display(); ?>
        </div>
    <?php elseif ($active_tab == 'forms'): ?>
        <div class="" id="forms">
            <div style="min-height: 200px;">
                <h2>Your Payment Forms
                    <img src="<?php echo plugins_url('/img/loader.gif', dirname(__FILE__)); ?>" alt="Loading..." class="showLoading"/>
                </h2>
                <?php if (count($paymentForms) === 0): ?>
                    <p class="alert alert-info">You have created no payment forms yet. Use the Create New Form tab to get started</p>
                <?php else: ?>
                    <table class="table table-condensed table-hover">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Title</th>
                            <th>Amount</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody id="paymentFormsTable">
                        <?php foreach ($paymentForms as $f): ?>
                            <tr>
                                <td><?php echo $f->name; ?></td>
                                <td><?php echo $f->formTitle; ?></td>
                                <?php if ($f->customAmount == 0): ?>
                                    <td><?php echo $currencySymbol . sprintf('%0.2f', $f->amount / 100.0); ?></td>
                                <?php else: ?>
                                    <td>Custom</td>
                                <?php endif; ?>
                                <td>
                                    <a class="btn btn-mini edit" href="<?php echo admin_url("admin.php?page=fullstripe-edit-form-f&form=$f->paymentFormID&type=payment"); ?>">Edit</a>
                                </td>
                                <td>
                                    <button class="btn btn-mini delete" data-id="<?php echo $f->paymentFormID; ?>" data-type="paymentForm">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    <?php elseif ($active_tab == 'create'): ?>
        <div class="" id="create">
            <div id="createPaymentFormSection">
            <form class="form-horizontal" action="" method="POST" id="create-payment-form">
                <p class="tips"></p>
                <input type="hidden" name="action" value="wp_full_stripe_create_payment_form">
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">
                            <label class="control-label">Form Name: </label>
                        </th>
                        <td>
                            <input type="text" class="regular-text" name="form_name" id="form_name">
                            <p class="description">This name will be used to identify this form in the shortcode i.e. [fullstripe_payment form="FormName"]</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label class="control-label">Form Title: </label>
                        </th>
                        <td>
                            <input type="text" class="regular-text" name="form_title" id="form_title">
                            <p class="description">The title of the form</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label class="control-label">Payment Type: </label>
                        </th>
                        <td>
                            <label class="radio inline">
                                <input type="radio" name="form_custom" id="set_specific_amount" value="0" checked="checked"> Set Amount
                            </label> <label class="radio inline">
                                <input type="radio" name="form_custom" id="set_custom_amount" value="1"> Custom Amount
                            </label>
                            <p class="description">Choose to set a specific amount for this form, or allow customers to set custom amounts</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label class="control-label">Payment Amount: </label>
                        </th>
                        <td>
                            <input type="text" class="regular-text" name="form_amount" id="form_amount"/>
                            <p class="description">The amount this form will charge your customer, in cents. i.e. for $10.00 enter 1000</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label class="control-label">Payment Button Text: </label>
                        </th>
                        <td>
                            <input type="text" class="regular-text" name="form_button_text" id="form_button_text" value="Make Payment">
                            <p class="description">The text on the payment button</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label class="control-label">Include Amount on Button? </label>
                        </th>
                        <td>
                            <label class="radio inline">
                                <input type="radio" name="form_button_amount" id="hide_button_amount" value="0"> Hide
                            </label> <label class="radio inline">
                                <input type="radio" name="form_button_amount" id="show_button_amount" value="1" checked="checked"> Show
                            </label>
                            <p class="description">For set amount forms, choose to show/hide the amount on the payment button</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label class="control-label">Include Email Address Field? </label>
                        </th>
                        <td>
                            <label class="radio inline">
                                <input type="radio" name="form_show_email_input" id="hide_email_input" value="0"> Hide
                            </label> <label class="radio inline">
                                <input type="radio" name="form_show_email_input" id="show_email_input" value="1" checked="checked"> Show
                            </label>
                            <p class="description">Should this payment form also ask for the customers email address?</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label class="control-label">Send Email Receipt? </label>
                        </th>
                        <td>
                            <label class="radio inline">
                                <input type="radio" name="form_send_email_receipt"  value="0" checked="checked"> No
                            </label> <label class="radio inline">
                                <input type="radio" name="form_send_email_receipt" value="1" > Yes
                            </label>
                            <p class="description">Send an email receipt on successful payment? Must include email address field on this form.</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label class="control-label">Include Billing Address Field? </label>
                        </th>
                        <td>
                            <label class="radio inline">
                                <input type="radio" name="form_show_address_input" id="hide_address_input" value="0" checked="checked"> Hide
                            </label> <label class="radio inline">
                                <input type="radio" name="form_show_address_input" id="show_address_input" value="1"> Show
                            </label>
                            <p class="description">Should this payment form also ask for the customers billing address?</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label class="control-label">Include Custom Input Field? </label>
                        </th>
                        <td>
                            <label class="radio inline">
                                <input type="radio" name="form_include_custom_input" id="noinclude_custom_input" value="0" checked="checked"> No
                            </label> <label class="radio inline">
                                <input type="radio" name="form_include_custom_input" id="include_custom_input" value="1"> Yes
                            </label>
                            <p class="description">You can ask for extra information from the customer to be included in the payment details</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label class="control-label">Custom Input Label: </label>
                        </th>
                        <td>
                            <input type="text" class="regular-text" name="form_custom_input_label" id="form_custom_input_label" disabled="disabled"/>
                            <p class="description">The text for the label next to the custom input field</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label class="control-label">Redirect On Success?</label>
                        </th>
                        <td>
                            <label class="radio inline">
                                <input type="radio" name="form_do_redirect" id="do_redirect_no" value="0" checked="checked"> No
                            </label> <label class="radio inline">
                                <input type="radio" name="form_do_redirect" id="do_redirect_yes" value="1"> Yes
                            </label>
                            <p class="description">When payment is successful you can choose to redirect to another page or post</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label class="control-label">Redirect Page/Post ID: </label>
                        </th>
                        <td>
                            <input type="text" class="regular-text" name="form_redirect_post_id" id="form_redirect_post_id" disabled="disabled"/>
                            <p class="description">The ID for the page/post to redirect to after payment is successful</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label class="control-label">Form Style: </label>
                        </th>
                        <td>
                            <select class="regular-text" name="form_style" id="form_style">
                                <option value="0">Default</option>
                                <option value="1">Compact</option>
                            </select>
                            <p class="description">Choose how you'd like the form to look. (More coming soon in paid version!)</p>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <button class="button button-primary" type="submit">Create Form</button>
                    <img src="<?php echo plugins_url('/img/loader.gif', dirname(__FILE__)); ?>" alt="Loading..." class="showLoading"/>
                </p>
            </form>
            </div>
        </div>
    <?php endif; ?>
    </div>
</div>

