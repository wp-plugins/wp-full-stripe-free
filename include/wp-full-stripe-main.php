<?php

class MM_WPFSF
{
    public static $instance;
    private $customer = null;
    private $admin = null;
    private $adminMenu = null;
    private $database = null;
    private $stripe = null;

    const VERSION = '1.0';

    public static function getInstance()
    {
        if (is_null(self::$instance))
        {
            self::$instance = new MM_WPFSF();
        }
        return self::$instance;
    }

    public static function setup_db()
    {
        MM_WPFSF_Database::fullstripe_setup_db();
    }

    public function __construct()
    {
        $this->includes();
        $this->setup();
        $this->hooks();
    }

    function includes()
    {
        include 'wp-full-stripe-database.php';
        include 'wp-full-stripe-customer.php';
        include 'wp-full-stripe-payments.php';
        include 'wp-full-stripe-admin.php';
        include 'wp-full-stripe-admin-menu.php';

        do_action('fullstripe_includes_action');
    }

    function hooks()
    {
        add_shortcode('fullstripe_payment', array($this, 'fullstripe_payment_form'));
        add_filter( 'plugin_action_links_' . WP_FULL_STRIPE_BASENAME, array($this, 'add_action_links') );
        do_action('fullstripe_main_hooks_action');
    }

    function setup()
    {
        //set option defaults
        $options = get_option('fullstripe_options_f');
        if ($options == '' || $options['fullstripe_version'] != self::VERSION)
        {
            $this->set_option_defaults($options);
        }

        //set API key
        if ($options['apiMode'] === 'test')
        {
            $this->fullstripe_set_api_key($options['secretKey_test']);
        }
        else
        {
            $this->fullstripe_set_api_key($options['secretKey_live']);
        }

        //setup subclasses to handle everything
        $this->database = new MM_WPFSF_Database();
        $this->customer = new MM_WPFSF_Customer();
        $this->admin = new MM_WPFSF_Admin();
        $this->adminMenu = new MM_WPFSF_Admin_Menu();
        $this->stripe = new MM_WPFSF_Stripe();

        do_action('fullstripe_setup_action');

    }

    function add_action_links ($links)
    {
        $links[] = '<a href="' . admin_url( 'admin.php?page=fullstripe-settings-f' ) . '">Settings</a>';
        $links[] = '<a href="http://codecanyon.net/item/wp-full-stripe/5266365?ref=mammothology">Upgrade</a>';

        return $links;
    }

    function set_option_defaults($options)
    {
        if ($options == '')
        {
            $arr = array(
                'secretKey_test' => 'YOUR_TEST_SECRET_KEY',
                'publishKey_test' => 'YOUR_TEST_PUBLISHABLE_KEY',
                'secretKey_live' => 'YOUR_LIVE_SECRET_KEY',
                'publishKey_live' => 'YOUR_LIVE_PUBLISHABLE_KEY',
                'apiMode' => 'test',
                'currency' => 'usd',
                'includeStyles' => '1',
                'fullstripe_version' => self::VERSION
            );

            update_option('fullstripe_options_f', $arr);
        }
        else //different version
        {
            $options['fullstripe_version'] = self::VERSION;
            if (!array_key_exists('secretKey_test', $options)) $options['secretKey_test'] = 'YOUR_TEST_SECRET_KEY';
            if (!array_key_exists('publishKey_test', $options)) $options['publishKey_test'] = 'YOUR_TEST_PUBLISHABLE_KEY';
            if (!array_key_exists('secretKey_live', $options)) $options['secretKey_live'] = 'YOUR_LIVE_SECRET_KEY';
            if (!array_key_exists('publishKey_live', $options)) $options['publishKey_live'] = 'YOUR_LIVE_PUBLISHABLE_KEY';
            if (!array_key_exists('apiMode', $options)) $options['apiMode'] = 'test';
            if (!array_key_exists('currency', $options)) $options['currency'] = 'usd';
            if (!array_key_exists('includeStyles', $options)) $options['includeStyles'] = '1';

            update_option('fullstripe_options_f', $options);

            //also, if version changed then the DB might be out of date
            MM_WPFSF_Database::fullstripe_setup_db();
        }

    }

    function fullstripe_set_api_key($key)
    {
        if ($key != '' && $key != 'YOUR_TEST_SECRET_KEY' && $key != 'YOUR_LIVE_SECRET_KEY')
        {
            try
            {
                Stripe::setApiKey($key);
            }
            catch (Exception $e)
            {
                //invalid key was set, ignore it
            }
        }
    }

    function fullstripe_payment_form($atts)
    {
        extract(shortcode_atts(array(
            'form' => 'default',
        ), $atts));

        //load scripts and styles
        $this->fullstripe_load_css();
        $this->fullstripe_load_js();
        //load form data into scope
        list($formData, $currencySymbol, $localeState, $localeZip, $creditCardImage) = $this->load_payment_form_data($form);

        //get the form style
        $style = 0;
        if (!$formData) $style = -1;
        else $style = $formData->formStyle;

        ob_start();
        include $this->get_payment_form_by_style($style);
        $content = ob_get_clean();
        return apply_filters('fullstripe_payment_form_output', $content);
    }

    function load_payment_form_data($form)
    {
        list ($currencySymbol,  $localeState,  $localeZip, $creditCardImage) = $this->get_locale_strings();

        $formData = array(
            $this->database->get_payment_form_by_name($form),
            $currencySymbol,
            $localeState,
            $localeZip,
            $creditCardImage
        );

        return $formData;
    }

    function get_payment_form_by_style($styleID)
    {
        switch ($styleID)
        {
            case -1:
                return WP_FULL_STRIPE_DIR . '/pages/forms/invalid_shortcode.php';

            case 0:
                return WP_FULL_STRIPE_DIR . '/pages/fullstripe_payment_form.php';

            case 1:
                return WP_FULL_STRIPE_DIR . '/pages/forms/payment_form_compact.php';

            default:
                return WP_FULL_STRIPE_DIR . '/pages/fullstripe_payment_form.php';
        }
    }
    function fullstripe_load_js()
    {
        $options = get_option('fullstripe_options_f');
        wp_enqueue_script('stripe-js', 'https://js.stripe.com/v2/', array('jquery'));
        wp_enqueue_script('wp-full-stripe-js', plugins_url('js/wp-full-stripe.js', dirname(__FILE__)), array('stripe-js'));
        if ($options['apiMode'] === 'test')
        {
            wp_localize_script('wp-full-stripe-js', 'stripekey', $options['publishKey_test']);
        }
        else
        {
            wp_localize_script('wp-full-stripe-js', 'stripekey', $options['publishKey_live']);
        }

        wp_localize_script('wp-full-stripe-js', 'ajaxurl', admin_url('admin-ajax.php'));

        do_action('fullstripe_load_js_action');
    }

    function fullstripe_load_css()
    {
        $options = get_option('fullstripe_options_f');
        if ($options['includeStyles'] === '1')
        {
            wp_enqueue_style('fullstripe-bootstrap-css', plugins_url('/css/newstyle.css', dirname(__FILE__)));
        }

        do_action('fullstripe_load_css_action');
    }

    function get_locale_strings()
    {
        $options = get_option('fullstripe_options_f');
        $currencySymbol = '$';
        $localeState = 'State';
        $localeZip = 'Zip';
        $creditCardImage = 'creditcards-us.png';

        if ( $options['currency'] === 'eur' )
        {
            $currencySymbol = '€';
            $localeState = 'Region';
            $localeZip = 'Zip / Postcode';
            $creditCardImage = 'creditcards.png';
        }
        elseif ( $options['currency'] === 'gbp' )
        {
            $currencySymbol = '£';
            $localeState = 'County';
            $localeZip = 'Postcode';
            $creditCardImage = 'creditcards.png';
        }

        return array(
            $currencySymbol,
            $localeState,
            $localeZip,
            $creditCardImage
        );
    }

}

MM_WPFSF::getInstance();
