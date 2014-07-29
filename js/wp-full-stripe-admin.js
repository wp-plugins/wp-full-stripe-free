
Stripe.setPublishableKey(stripekey);

jQuery(document).ready(function ($)
{
    $(".showLoading").hide();
    $("#updateDiv").hide();

    function resetForm($form)
    {
        $form.find('input:text, input:password, input:file, select, textarea').val('');
        $form.find('input:radio, input:checkbox')
            .removeAttr('checked').removeAttr('selected');
    }

    function validField(field, fieldName, errorField)
    {
        var valid = true;
        if (field.val() === "")
        {
            errorField.addClass('alert alert-error');
            errorField.html("<p>" + fieldName + " must contain a value</p>");
            valid = false;
        }
        return valid;
    }

     //payment type toggle
    $('#set_custom_amount').click(function ()
    {
        $('#form_amount').prop('disabled', true);
    });
    $('#set_specific_amount').click(function ()
    {
        $('#form_amount').prop('disabled', false);
    });
    $('#noinclude_custom_input').click(function ()
    {
        $('#form_custom_input_label').prop('disabled', true);
    });
    $('#include_custom_input').click(function ()
    {
        $('#form_custom_input_label').prop('disabled', false);
    });
    $('#do_redirect_no').click(function ()
    {
        $('#form_redirect_post_id').prop('disabled', true);
    });
    $('#do_redirect_yes').click(function ()
    {
        $('#form_redirect_post_id').prop('disabled', false);
    });
    $('#do_redirect_no_ck').click(function ()
    {
        $('#form_redirect_post_id_ck').prop('disabled', true);
    });
    $('#do_redirect_yes_ck').click(function ()
    {
        $('#form_redirect_post_id_ck').prop('disabled', false);
    });

    $('#create-payment-form').submit(function (e)
    {
        $(".tips").removeClass('alert alert-error');
        $(".tips").html("");

        var customAmount = $('input[name=form_custom]:checked', '#create-payment-form').val();
        var includeCustom = $('input[name=form_include_custom_input]:checked', '#create-payment-form').val();

        var valid = validField($('#form_name'), 'Name', $('.tips'));
        valid = valid && validField($('#form_title'), 'Form Title', $('.tips'));
        if (customAmount == 0)
            valid = valid && validField($('#form_amount'), 'Amount', $('.tips'));
        if (includeCustom == 1)
            valid = valid && validField($('#form_custom_input_label'), 'Custom Input Label', $('.tips'));

        if (valid)
        {
            $(".showLoading").show();
            var $form = $(this);
            // Disable the submit button
            $form.find('button').prop('disabled', true);

            $.ajax({
                type: "POST",
                url: admin_ajaxurl,
                data: $form.serialize(),
                cache: false,
                dataType: "json",
                success: function (data)
                {
                    $(".showLoading").hide();
                    document.body.scrollTop = document.documentElement.scrollTop = 0;

                    if (data.success)
                    {
                        var row = '<tr>';
                        row += '<td>' + $('#form_name').val() + '</td>';
                        row += '<td>' + $('#form_title').val() + '</td>';
                        row += '<td>' + $('#form_amount').val() + '</td>';
                        row += '<td><button class="btn btn-mini edit" disabled="disabled">Edit</button></td>';
                        row += '<td><button class="btn btn-mini" disabled="disabled">Delete</button></td>';
                        row += '</tr>';
                        $('#paymentFormsTable').append(row);

                        $("#updateMessage").text("Payment form created");
                        $("#updateDiv").addClass('updated').show();
                        $form.find('button').prop('disabled', false);
                        resetForm($form);
                    }
                    else
                    {
                        // re-enable the submit button
                        $form.find('button').prop('disabled', false);
                        // show the errors on the form
                        $(".tips").addClass('alert alert-error');
                        $(".tips").html(data.msg);
                        $(".tips").fadeIn(500).fadeOut(500).fadeIn(500);
                    }
                }
            });
        }

        return false;
    });

    $('#edit-payment-form').submit(function (e)
    {
        var $err = $(".tips");
        $err.removeClass('alert alert-error');
        $err.html("");

        var customAmount = $('input[name=form_custom]:checked', '#edit-payment-form').val();
        var includeCustom = $('input[name=form_include_custom_input]:checked', '#edit-payment-form').val();

        var valid = validField($('#form_name'), 'Name', $err);
        valid = valid && validField($('#form_title'), 'Form Title', $err);
        if (customAmount == 0)
            valid = valid && validField($('#form_amount'), 'Amount', $err);
        if (includeCustom == 1)
            valid = valid && validField($('#form_custom_input_label'), 'Custom Input Label', $err);

        if (valid)
        {
            $(".showLoading").show();
            var $form = $(this);
            // Disable the submit button
            $form.find('button').prop('disabled', true);

            $.ajax({
                type: "POST",
                url: admin_ajaxurl,
                data: $form.serialize(),
                cache: false,
                dataType: "json",
                success: function (data)
                {
                    $(".showLoading").hide();
                    document.body.scrollTop = document.documentElement.scrollTop = 0;

                    if (data.success)
                    {
                        $("#updateMessage").text("Payment form updated");
                        $("#updateDiv").addClass('updated').show();
                        resetForm($form);
                        setTimeout(function ()
                        {
                            window.location = data.redirectURL;
                        }, 1500);
                    }
                    else
                    {
                        // re-enable the submit button
                        $form.find('button').prop('disabled', false);
                        // show the errors on the form
                        $err.addClass('alert alert-error');
                        $err.html(data.msg);
                        $err.fadeIn(500).fadeOut(500).fadeIn(500);
                    }
                }
            });
        }

        return false;
    });

    $('#settings-form').submit(function (e)
    {
        $(".showLoading").show();
        $(".tips").removeClass('alert alert-error');
        $(".tips").html("");

        var $form = $(this);

        // Disable the submit button
        $form.find('button').prop('disabled', true);

        var valid = true;

        if (valid)
        {
            $.ajax({
                type: "POST",
                url: admin_ajaxurl,
                data: $form.serialize(),
                cache: false,
                dataType: "json",
                success: function (data)
                {
                    $(".showLoading").hide();
                    document.body.scrollTop = document.documentElement.scrollTop = 0;

                    if (data.success)
                    {
                        $("#updateMessage").text("Settings updated");
                        $("#updateDiv").addClass('updated').show();
                        $form.find('button').prop('disabled', false);
                    }
                    else
                    {
                        // re-enable the submit button
                        $form.find('button').prop('disabled', false);
                        // show the errors on the form
                        $(".tips").addClass('alert alert-error');
                        $(".tips").html(data.msg);
                        $(".tips").fadeIn(500).fadeOut(500).fadeIn(500);
                    }
                }
            });

            return false;
        }

    });

    //The forms delete button
    $('button.delete').click(function ()
    {
        var id = $(this).attr('data-id');
        var type = $(this).attr('data-type');
        var action = '';
        if (type === 'paymentForm')
            action = 'wp_full_stripe_delete_payment_form';
        else if (type === 'subscriptionForm')
            action = 'wp_full_stripe_delete_subscription_form';
        else if (type === 'checkoutForm')
            action = 'wp_full_stripe_delete_checkout_form';

        var row = $(this).parents('tr:first');

        $(".showLoading").show();

        $.ajax({
            type: "POST",
            url: admin_ajaxurl,
            data: {id: id, action: action},
            cache: false,
            dataType: "json",
            success: function (data)
            {
                $(".showLoading").hide();

                if (data.success)
                {
                    $(row).remove();
                    $("#updateMessage").text("Form deleted");
                    $("#updateDiv").addClass('updated').show();
                }
            }
        });

        return false;

    });

});