<div class="wrap">
    <h2><?php echo __('Full Stripe Help', 'wp-full-stripe'); ?></h2>
    <p>This plugin is designed to make it easy for you to accept payments from your Wordpress site. Powered by Stripe, you can embed payment forms into any post or page and take payments directly from your website without making your customers leave for a 3rd party website.</p>
    <h4>Setup</h4>
    <ul>
        <li>You need a free Stripe account from <a href="https://stripe.com">Stripe.com</a></li>
        <li>Get your Stripe API keys from your
            <a href="https://manage.stripe.com">Stripe Dashboard</a> -> Account Settings -> API Keys tab
        </li>
        <li>Update the Full Stripe settings with your API keys and select the mode. (Test most is recommended initially to make sure everything is setup correctly)</li>
    </ul>
    <h4>Payments</h4>
    <p>Now that the Stripe keys are set, you can create a payment form from the Full Stripe Payments page. A payment form is setup to take a specific payment amount from your customers. Create the form by setting it's name, title and payment amount.
        You can also choose to allow your customers to enter custom amounts on the form.  This makes creating things like donation forms easier.
        The form name is used in the shortcode (see below) to display the form.</p>
    <p>To show a payment form, add the following shortcode to any post or page:
        <code>[fullstripe_payment form="formName"]</code> where "formName" equals the name you used to create the form.
    </p>
    <p>Once a payment is taken using the form, the payment information will appear on the Full Stripe Payments page as well as on your Stripe Dashboard</p>

    <h4>SSL</h4>
    <p>Use of SSL is
        <strong>highly recommended</strong> as this will protect your customers card details. No card details are ever stored on your server however without SSL they are still subject to certain types of hacking. SSL certificates are extremely affordable from companies like
        <a href="http://www.namecheap.com/?aff=51961">Namecheap</a> and well worth it for the security of your customers.
    </p>
    <h4>Payment Currency</h4>
    <p>Select the currency you receive payments in from the Full Stripe settings menu.  If you have a US Stripe account you can only accept payments in USD and selecting any other option will fail.  If you have a Canadian Stripe account you can select either USD or CAD.
    The UK and the Ireland beta accounts can accept Euros, GBP and USD (only with a US account).</p>
    <p>Please note that you must make sure your Stripe account is set to accept the same currency otherwise payment will fail.  This means going to your Stripe Account Settings and selecting the currency, or adding a secondary currency in the case of accounts which allow that.</p>
    <h4>Custom Fields</h4>
    <p>You can add an extra field to payment forms to include any extra data you want to request from the customer.  When creating the payment form you can choose to include this extra field and it's title which will be shown to the user on the form.
    The extra data will be appended to the payment information and viewable in your Stripe dashboard once the payment is complete.</p>
    <h4>Redirects</h4>
    <p>When creating payment or subscription forms you have the option to redirect to a specific page or post after a successful payment.  To do this you must turn on redirects when creating the form and also input the post ID of the post/page you wish to redirect to.<br/>
    If you are using the default permalink structure you'll see a the post ID of each post/page in the URL bar when viewing that post/page.  If you are using "pretty" permalinks you can also find the post/page ID by viewing all posts/pages in the Wordpress dashboard menu.
    Simply hover your mouse over the post/page title and you'll see the post ID in the browser status bar.  Finally, you can also see the post ID by browsing your database table 'wp_posts' for your Wordpress website.</p>
    <h4>More Help</h4>
    <p>If you require any more help with this plugin, you can always go to
        <a href="http://mammothology.com/forums">the Mammothology Support Forums</a> to ask your question, or email us directly at
        <a href="mailto:support@mammothology.com">support@mammothology.com</a></p>
    <div style="padding-top: 50px;">
        <h4>Notices</h4>
        <p>Please note that while every care has been taken to write secure and working code, Mammothology and Jamie Osborne take no responsibility for any errors, faults or other problems arising from using this payments plugin. Use at your own risk. Mammothology cannot foresee every possible usage and user error and does not condone the use of this plugin for any illegal means. Mammothology has no affiliation with
            <a href="https://stripe.com">Stripe</a> and any issues with payments should be directed to
            <a href="https://stripe.com">Stripe.com</a>.</p><p>Please also note that this is free software provided 'as is' with no liability placed on Mammothology or Jamie Osborne and by using this software you
        agree to do so at your own risk.</p>

    </div>
</div>