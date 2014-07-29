<?php
global $wpdb;
//get the data we need
$formID = -1;
$formType = "";
if (isset($_GET['form']))
    $formID = $_GET['form'];
if (isset($_GET['type']))
    $formType = $_GET['type'];

$valid = true;
if ($formID == -1 || $formType == "")
    $valid = false;

$editForm = null;
$plans = array();

if ($valid)
{

    if ($formType == "payment")
    {
        $editForm = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "fullstripe_payment_forms WHERE paymentFormID=%d", $formID));
    }
    else
        $valid = false;

    if ($editForm == null) $valid = false;
}
?>
<div class="wrap">
    <h2> <?php echo __('Full Stripe Edit Form', 'wp-full-stripe'); ?> </h2>
    <div id="updateDiv"><p><strong id="updateMessage"></strong></p></div>
    <?php if (!$valid): ?>
        <p>Form not found!</p>
    <?php else: ?>
        <?php if ($formType == "payment"): ?>
            <form class="form-horizontal" action="" method="POST" id="edit-payment-form">
                <p class="tips"></p>
                <input type="hidden" name="action" value="wp_full_stripe_edit_payment_form">
                <input type="hidden" name="formID" value="<?php echo $editForm->paymentFormID; ?>">
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">
                            <label class="control-label">Form Name: </label>
                        </th>
                        <td>
                            <input type="text" class="regular-text" name="form_name" id="form_name" value="<?php echo $editForm->name; ?>">
                            <p class="description">This name will be used to identify this form in the shortcode i.e. [fullstripe_payment form="FormName"]</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label class="control-label">Form Title: </label>
                        </th>
                        <td>
                            <input type="text" class="regular-text" name="form_title" id="form_title" value="<?php echo $editForm->formTitle; ?>">
                            <p class="description">The title of the form</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label class="control-label">Payment Type: </label>
                        </th>
                        <td>
                            <label class="radio inline">
                                <input type="radio" name="form_custom" id="set_specific_amount" value="0" <?php echo ($editForm->customAmount == '0') ? 'checked' : '' ?> > Set Amount
                            </label> <label class="radio inline">
                                <input type="radio" name="form_custom" id="set_custom_amount" value="1" <?php echo ($editForm->customAmount == '1') ? 'checked' : '' ?> > Custom Amount
                            </label>
                            <p class="description">Choose to set a specific amount for this form, or allow customers to set custom amounts</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label class="control-label">Payment Amount: </label>
                        </th>
                        <td>
                            <input type="text" class="regular-text" name="form_amount" id="form_amount" value="<?php echo $editForm->amount; ?>"/>
                            <p class="description">The amount this form will charge your customer, in cents. i.e. for $10.00 enter 1000</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label class="control-label">Payment Button Text: </label>
                        </th>
                        <td>
                            <input type="text" class="regular-text" name="form_button_text" id="form_button_text" value="<?php echo $editForm->buttonTitle; ?>">
                            <p class="description">The text on the payment button</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label class="control-label">Include Amount on Button? </label>
                        </th>
                        <td>
                            <label class="radio inline">
                                <input type="radio" name="form_button_amount" id="hide_button_amount" value="0" <?php echo ($editForm->showButtonAmount == '0') ? 'checked' : '' ?> > Hide
                            </label> <label class="radio inline">
                                <input type="radio" name="form_button_amount" id="show_button_amount" value="1" <?php echo ($editForm->showButtonAmount == '1') ? 'checked' : '' ?> > Show
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
                                <input type="radio" name="form_show_email_input" id="hide_email_input" value="0" <?php echo ($editForm->showEmailInput == '0') ? 'checked' : '' ?> > Hide
                            </label> <label class="radio inline">
                                <input type="radio" name="form_show_email_input" id="show_email_input" value="1" <?php echo ($editForm->showEmailInput == '1') ? 'checked' : '' ?> > Show
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
                                <input type="radio" name="form_send_email_receipt" value="0" <?php echo ($editForm->sendEmailReceipt == '0') ? 'checked' : '' ?>> No
                            </label> <label class="radio inline">
                                <input type="radio" name="form_send_email_receipt" value="1" <?php echo ($editForm->sendEmailReceipt == '1') ? 'checked' : '' ?>> Yes
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
                                <input type="radio" name="form_show_address_input" id="hide_address_input" value="0" <?php echo ($editForm->showAddress == '0') ? 'checked' : '' ?> > Hide
                            </label> <label class="radio inline">
                                <input type="radio" name="form_show_address_input" id="show_address_input" value="1" <?php echo ($editForm->showAddress == '1') ? 'checked' : '' ?> > Show
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
                                <input type="radio" name="form_include_custom_input" id="noinclude_custom_input" value="0" <?php echo ($editForm->showCustomInput == '0') ? 'checked' : '' ?> > No
                            </label> <label class="radio inline">
                                <input type="radio" name="form_include_custom_input" id="include_custom_input" value="1" <?php echo ($editForm->showCustomInput == '1') ? 'checked' : '' ?> > Yes
                            </label>
                            <p class="description">You can ask for extra information from the customer to be included in the payment details</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label class="control-label">Custom Input Label: </label>
                        </th>
                        <td>
                            <input type="text" class="regular-text" name="form_custom_input_label" id="form_custom_input_label" <?php echo ($editForm->showCustomInput == '0') ? 'disabled="disabled"' : '' ?> value="<?php echo $editForm->customInputTitle; ?>"/>
                            <p class="description">The text for the label next to the custom input field</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label class="control-label">Redirect On Success?</label>
                        </th>
                        <td>
                            <label class="radio inline">
                                <input type="radio" name="form_do_redirect" id="do_redirect_no" value="0" <?php echo ($editForm->redirectOnSuccess == '0') ? 'checked' : '' ?> > No
                            </label> <label class="radio inline">
                                <input type="radio" name="form_do_redirect" id="do_redirect_yes" value="1" <?php echo ($editForm->redirectOnSuccess == '1') ? 'checked' : '' ?> > Yes
                            </label>
                            <p class="description">When payment is successful you can choose to redirect to another page or post</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label class="control-label">Redirect Page/Post ID: </label>
                        </th>
                        <td>
                            <input type="text" class="regular-text" name="form_redirect_post_id" id="form_redirect_post_id" <?php echo ($editForm->redirectOnSuccess == '0') ? 'disabled="disabled"' : '' ?> value="<?php echo $editForm->redirectPostID; ?>"/>
                            <p class="description">The ID for the page/post to redirect to after payment is successful</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label class="control-label">Form Style: </label>
                        </th>
                        <td>
                            <select class="regular-text" name="form_style" id="form_style">
                                <option value="0" <?php if ($editForm->formStyle == 0) echo 'selected="selected"'; ?> >Default</option>
                                <option value="1" <?php if ($editForm->formStyle == 1) echo 'selected="selected"'; ?> >Compact</option>
                            </select>
                            <p class="description">Choose how you'd like the form to look. (More coming soon in paid version!)</p>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <button class="button button-primary" type="submit">Save Changes</button>
                    <img src="<?php echo plugins_url('/img/loader.gif', dirname(__FILE__)); ?>" alt="Loading..." class="showLoading"/>
                </p>
            </form>
        <?php endif; ?>
    <?php endif; ?>
</div>