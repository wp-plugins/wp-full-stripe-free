
Stripe.setPublishableKey(stripekey);

jQuery(document).ready(function ($)
{
    $("#showLoading").hide();

    var $err = $(".payment-errors");

    $('#payment-form').submit(function (e)
    {
        $("#showLoading").show();

        $err.removeClass('alert alert-error');
        $err.html("");

        var $form = $(this);

        // Disable the submit button
        $form.find('button').prop('disabled', true);

        Stripe.createToken($form, stripeResponseHandler);
        return false;
    });

    var stripeResponseHandler = function (status, response)
    {
        var $form = $('#payment-form');

        if (response.error)
        {
            // Show the errors
            $err.addClass('alert alert-error');
            $err.html(response.error.message);
            $err.fadeIn(500).fadeOut(500).fadeIn(500);
            $form.find('button').prop('disabled', false);
            $("#showLoading").hide();
        }
        else
        {
            // token contains id, last4, and card type
            var token = response.id;
            $form.append("<input type='hidden' name='stripeToken' value='" + token + "' />");

            //post payment via ajax
            $.ajax({
                type: "POST",
                url: ajaxurl,
                data: $form.serialize(),
                cache: false,
                dataType: "json",
                success: function (data)
                {
                    $("#showLoading").hide();

                    if (data.success)
                    {
                        //clear form fields
                        $form.find('input:text, input:password').val('');
                        //inform user of success
                        $err.addClass('alert alert-success');
                        $err.html(data.msg);
                        $form.find('button').prop('disabled', false);
                        if (data.redirect)
                        {
                            setTimeout(function ()
                            {
                                window.location = data.redirectURL;
                            }, 1500);
                        }
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
    };

    $('#payment-form-style').submit(function (e)
    {
        $("#showLoading").show();
        var $err = $(".payment-errors");
        $err.removeClass('alert alert-error');
        $err.html("");

        var $form = $(this);

        // Disable the submit button
        $form.find('button').prop('disabled', true);

        Stripe.createToken($form, stripeResponseHandler2);
        return false;
    });

    var stripeResponseHandler2 = function (status, response)
    {
        var $form = $('#payment-form-style');

        if (response.error)
        {
            // Show the errors
            $err.addClass('alert alert-error');
            $err.html(response.error.message);
            $err.fadeIn(500).fadeOut(500).fadeIn(500);
            $form.find('button').prop('disabled', false);
            $("#showLoading").hide();
        }
        else
        {
            // token contains id, last4, and card type
            var token = response.id;
            $form.append("<input type='hidden' name='stripeToken' value='" + token + "' />");

            //post payment via ajax
            $.ajax({
                type: "POST",
                url: ajaxurl,
                data: $form.serialize(),
                cache: false,
                dataType: "json",
                success: function (data)
                {
                    $("#showLoading").hide();

                    if (data.success)
                    {
                        //clear form fields
                        $form.find('input:text, input:password').val('');
                        //inform user of success
                        $err.addClass('alert alert-success');
                        $err.html(data.msg);
                        $form.find('button').prop('disabled', false);
                        if (data.redirect)
                        {
                            setTimeout(function ()
                            {
                                window.location = data.redirectURL;
                            }, 1500);
                        }
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
    };

});