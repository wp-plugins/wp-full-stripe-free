<?php
//Setup hooks and functions for admin menu

class MM_WPFSF_Admin_Menu
{
    function __construct()
    {
        add_action('admin_init', array($this, 'fullstripe_admin_init'));
        add_action('admin_menu', array($this, 'fullstripe_menu_pages'));
    }

    function fullstripe_admin_init()
    {
        wp_register_script('fullstripe-bootstrap-js', plugins_url('/js/bootstrap.min.js', dirname(__FILE__)));
        wp_register_style('fullstripe-bootstrap-css', plugins_url('/css/fullstripe.css', dirname(__FILE__)));
    }

    function fullstripe_menu_pages()
    {
        // Add the top-level admin menu
        $page_title = 'Full Stripe Free Settings';
        $menu_title = 'Full Stripe';
        $capability = 'manage_options';
        $menu_slug = 'fullstripe-settings-f';
        $function = array($this, 'fullstripe_settings');
        add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function);

        // Add submenu page with same slug as parent to ensure no duplicates
        $sub_menu_title = 'Settings';
        $menu_hook = add_submenu_page($menu_slug, $page_title, $sub_menu_title, $capability, $menu_slug, $function);
        add_action('admin_print_scripts-' . $menu_hook, array($this, 'fullstripe_admin_scripts')); //this ensures script/styles only loaded for this plugin admin pages

        $submenu_page_title = 'Full Stripe Payments';
        $submenu_title = 'Payments';
        $submenu_slug = 'fullstripe-payments-f';
        $submenu_function = array($this,'fullstripe_payments');
        $menu_hook = add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function);
        add_action('admin_print_scripts-' . $menu_hook, array($this, 'fullstripe_admin_scripts'));

        $submenu_page_title = 'Full Stripe Help';
        $submenu_title = 'Help';
        $submenu_slug = 'fullstripe-help-f';
        $submenu_function = array($this,'fullstripe_help');
        $menu_hook = add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function);
        add_action('admin_print_scripts-' . $menu_hook, array($this, 'fullstripe_admin_scripts'));

        $submenu_page_title = 'About WP Full Stripe';
        $submenu_title = 'About';
        $submenu_slug = 'fullstripe-about-f';
        $submenu_function = array($this,'fullstripe_about_page');
        add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function);

        //edit forms page - don't show on submenu
        $submenu_page_title = 'Full Stripe Edit Form';
        $submenu_title = 'Edit Form';
        $submenu_slug = 'fullstripe-edit-form-f';
        $submenu_function = array($this,'fullstripe_edit_form');
        $menu_hook = add_submenu_page(null, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function);
        add_action('admin_print_scripts-' . $menu_hook, array($this, 'fullstripe_admin_scripts'));

        do_action('fullstripe_admin_menus', $menu_slug);

    }

    function fullstripe_admin_scripts()
    {
        $options = get_option('fullstripe_options_f');
        wp_enqueue_media();
        wp_enqueue_script('fullstripe-bootstrap-js');
        wp_enqueue_script('stripe-js', 'https://js.stripe.com/v2/', array('jquery'));
        wp_enqueue_script('wp-full-stripe-admin-js', plugins_url('/js/wp-full-stripe-admin.js', dirname(__FILE__)), array('stripe-js'));
        if ($options['apiMode'] === 'test')
        {
            wp_localize_script('wp-full-stripe-admin-js', 'stripekey', $options['publishKey_test']);
        }
        else
        {
            wp_localize_script('wp-full-stripe-admin-js', 'stripekey', $options['publishKey_live']);
        }
        wp_localize_script('wp-full-stripe-admin-js', 'admin_ajaxurl', admin_url('admin-ajax.php'));
        wp_enqueue_style('fullstripe-bootstrap-css');

        do_action('fullstripe_admin_scripts');
    }

    function fullstripe_settings()
    {
        if (!current_user_can('manage_options'))
        {
            wp_die('You do not have sufficient permissions to access this page.');
        }

        include WP_FULL_STRIPE_DIR . '/pages/fullstripe_admin_page.php';
    }

    function fullstripe_payments()
    {
        if (!current_user_can('manage_options'))
        {
            wp_die('You do not have sufficient permissions to access this page.');
        }

        if (!class_exists('WP_List_Table'))
        {
            require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
        }
        if (!class_exists('WPFSF_Payments_Table'))
        {
            require_once(WP_FULL_STRIPE_DIR . '/include/wp-full-stripe-table-payments.php');
        }

        $table = new WPFSF_Payments_Table();
        $table->prepare_items();

        include WP_FULL_STRIPE_DIR . '/pages/fullstripe_payments_page.php';
    }

    function fullstripe_help()
    {
        if (!current_user_can('manage_options'))
        {
            wp_die('You do not have sufficient permissions to access this page.');
        }

        include WP_FULL_STRIPE_DIR . '/pages/fullstripe_help_page.php';
    }

    function fullstripe_edit_form()
    {
        if (!current_user_can('manage_options'))
        {
            wp_die('You do not have sufficient permissions to access this page.');
        }

        include WP_FULL_STRIPE_DIR . '/pages/fullstripe_edit_form_page.php';
    }

    function fullstripe_about_page()
    {
        if (!current_user_can('manage_options'))
        {
            wp_die('You do not have sufficient permissions to access this page.');
        }

        include WP_FULL_STRIPE_DIR . '/pages/fullstripe_about_page.php';
    }
}





