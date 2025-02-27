<?php

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

if (!class_exists('WC_Email_Vendor_New_Announcement')) :

    /**
     * New Announcement Email
     *
     * An email sent to the vendors when a new announcement is published.
     *
     * @class       WC_Email_Vendor_New_Announcement
     * @version     3.5.0
     * @package     WooCommerce/Classes/Emails
     * @author      WooThemes
     * @extends     WC_Email
     */
    class WC_Email_Vendor_New_Announcement extends WC_Email {
        public $vendor;
        public $single;
        /**
         * Constructor
         */
        function __construct() {
            global $WCMp;
            $this->id = 'vendor_new_announcement';
            $this->title = __('Vendor New Announcement', 'dc-woocommerce-multi-vendor');
            $this->description = __('New announcement notification emails are sent when new announcement published.', 'dc-woocommerce-multi-vendor');

            $this->template_html = 'emails/vendor-new-announcement.php';
            $this->template_plain = 'emails/plain/vendor-new-announcement.php';
            $this->template_base = $WCMp->plugin_path . 'templates/';

            // Call parent constructor
            parent::__construct();
        }


        /**
         * trigger function.
         *
         * @access public
         * @return void
         */
        function trigger( $post, $vendor_id, $single ) {
            global $WCMp;

            $vendor = get_wcmp_vendor($vendor_id);
            if ($vendor) {
                $this->object = $post;
                $this->find[] = '{title}';
                $this->post_title = $post->post_title;
                $this->post_content = $post->post_content;
                $this->replace[] = $this->post_title;

                $vendor_email = $vendor->user_data->user_email;
                $this->vendor = $vendor;
                $this->recipient = $vendor_email;

                $this->single = $single;

                if (!$this->is_enabled() || !$this->get_recipient()) {
                    return;
                }

                $result = $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
                    return $result;
            }
        }

        /**
         * Get email subject.
         *
         * @since  3.1.0
         * @return string
         */
        public function get_default_subject() {
            return apply_filters('wcmp_vendor_new_announcement_email_subject', __('Announcement - {title}', 'dc-woocommerce-multi-vendor'), $this->object);
        }

        /**
         * Get email heading.
         *
         * @since  3.1.0
         * @return string
         */
        public function get_default_heading() {
            return apply_filters('wcmp_vendor_new_announcement_email_heading', __('Announcement ', 'dc-woocommerce-multi-vendor'), $this->object);
        }

        /**
         * get_content_html function.
         *
         * @access public
         * @return string
         */
        function get_content_html() {
            return wc_get_template_html($this->template_html, array(
                'email_heading' => $this->get_heading(),
                'vendor' => $this->vendor,
                'post_title' => $this->post_title,
                'post_content'  => $this->post_content,
                'single' => $this->single,
                'sent_to_admin' => false,
                'plain_text' => false,
                'email'         => $this,
                    ), 'dc-product-vendor/', $this->template_base);
        }

        /**
         * get_content_plain function.
         *
         * @access public
         * @return string
         */
        function get_content_plain() {
            return wc_get_template_html($this->template_plain, array(
                'email_heading' => $this->get_heading(),
                'vendor' => $this->vendor,
                'post_title' => $this->post_title,
                'post_content'  => $this->post_content,
                'single' => $this->single,
                'sent_to_admin' => false,
                'plain_text' => true,
                'email'         => $this,
                    ), 'dc-product-vendor/', $this->template_base);
        }

        /**
         * Initialise Settings Form Fields
         *
         * @access public
         * @return void
         */
        function init_form_fields() {
            global $WCMp;
            $this->form_fields = array(
                'enabled' => array(
                    'title' => __('Enable/Disable', 'dc-woocommerce-multi-vendor'),
                    'type' => 'checkbox',
                    'label' => __('Enable this email notification.', 'dc-woocommerce-multi-vendor'),
                    'default' => 'yes'
                ),
                'subject' => array(
                    'title' => __('Subject', 'dc-woocommerce-multi-vendor'),
                    'type' => 'text',
                    'description' => sprintf(__('This controls the email subject line. Leave it blank to use the default subject: <code>%s</code>.', 'dc-woocommerce-multi-vendor'), $this->get_default_subject()),
                    'placeholder' => '',
                    'default' => ''
                ),
                'heading' => array(
                    'title' => __('Email Heading', 'dc-woocommerce-multi-vendor'),
                    'type' => 'text',
                    'description' => sprintf(__('This controls the main heading contained within the email notification. Leave it blank to use the default heading: <code>%s</code>.', 'dc-woocommerce-multi-vendor'), $this->get_default_heading()),
                    'placeholder' => '',
                    'default' => ''
                ),
                'email_type' => array(
                    'title' => __('Email Type', 'dc-woocommerce-multi-vendor'),
                    'type' => 'select',
                    'description' => __('Choose which format of email to be sent.', 'dc-woocommerce-multi-vendor'),
                    'default' => 'html',
                    'class' => 'email_type',
                    'options' => array(
                        'plain' => __('Plain Text', 'dc-woocommerce-multi-vendor'),
                        'html' => __('HTML', 'dc-woocommerce-multi-vendor'),
                        'multipart' => __('Multipart', 'dc-woocommerce-multi-vendor'),
                    )
                )
            );
        }
    }

endif;