<?php
/*
Plugin Name: ROI CF7 Notification
Plugin URI: https://www.upwork.com/freelancers/~01c536790406f2151a
Description: Custom ROI Notifications for Capital Innovations with Contact Form 7.
Author: elhardoum
Version: 0.1
Author URI: https://www.upwork.com/freelancers/~01c536790406f2151a
License: GPLv3
*/

if ( ! defined ( 'WPINC' ) ) {
    exit; // direct access
}

register_activation_hook(__FILE__, function()
{
    add_option('roi_cf7_notification_form_id', 452);
    add_option('roi_cf7_notification_calendly_link', 'https://calendly.com');
    add_option('roi_cf7_notification_form_id_redhat', 729);
    add_option('roi_cf7_notification_calendly_link_redhat', 'https://calendly.com/meetwithcap/30min');
});

function roi_cf7_calculate_migration_savings( int $current_cores, &$replace )
{
    switch ( $current_cores ) {
        case 16:
            $replace['<Year 2 Savings>'] = 768000 - 168960;
            $replace['<Months ROI Calculation>'] = ceil(((350000+104000)/224886)*12);
            break;

        case 64:
            $replace['<Year 2 Savings>'] = 3072000 - 675840;
            $replace['<Months ROI Calculation>'] = ceil(((525000+387200)/899543)*12);
            break;

        case 128:
            $replace['<Year 2 Savings>'] = 6144000 - 1351680;
            $replace['<Months ROI Calculation>'] = ceil(((787500+774400)/1799086)*12);
            break;

        case 256:
            $replace['<Year 2 Savings>'] = 12288000 - 2703360;
            $replace['<Months ROI Calculation>'] = ceil(((1181250+1548800)/3598172)*12);
            break;

        case 512:
            $replace['<Year 2 Savings>'] = 24576000 - 5406720;
            $replace['<Months ROI Calculation>'] = ceil(((1771875+3097600)/7196344)*12);
            break;

        case 1024:
            $replace['<Year 2 Savings>'] = 49152000 - 10813440;
            $replace['<Months ROI Calculation>'] = ceil(((2657813+6195200)/14392689)*12);
            break;
    }
}

add_action('wpcf7_mail_sent', function($contact_form)
{
    $form_id = (int) get_option('roi_cf7_notification_form_id');

    if ( ! $form_id || $form_id != $contact_form->id() )
        return;

    if ( ! $submission = \WPCF7_Submission::get_instance() )
        return;

    $data = $submission->get_posted_data();

    /**
      * Format email subject and body here
      * Use formatting keywords for data replacemenet:
      * <First Name> <Desired Plaftorm> <Months ROI Calculation> <Current System> <Calendly link> <Company Name> <Year 2 Savings>
      */
    $subject = '<First Name>, Save <Company Name> <Year 2 Savings> a Year!';
    $body = 'Hi <First Name>,

It’s hero time! Based on your input on our website, <Company Name> can save <Year 2 Savings> by migrating from <Current System> to <Desired Plaftorm>.

And it gets better, <Company Name> can fund this by re-allocating what you spend in <Months ROI Calculation> maintenance on <Current System>! And this doesn’t even begin to account for savings on infrastructure, expensive programming resources, etc.

Want some more detail? Grab some time on my calendar here <Calendly link>

P.S. Be glad to share my calculations as well as success stories from our customers on our call :)';

    /**
      * End formatting
      */

    $replace = [
        '<First Name>' => $data['text-firstname'] ?? '',
        '<Desired Plaftorm>' => $data['menu-newplatform'][0] ?? '',
        '<Months ROI Calculation>' => '',
        '<Current System>' =>  $data['menu-currentplatform'][0] ?? '',
        '<Calendly link>' => get_option('roi_cf7_notification_calendly_link'),
        '<Company Name>' => $data['text-companyname'] ?? '',
        '<Year 2 Savings>' => '',
    ];

    roi_cf7_calculate_migration_savings( intval($data['menu-currentcores'][0] ?? ''), $replace );

    ($replace['<Year 2 Savings>'] ?? '') && ($replace['<Year 2 Savings>'] = '$' . number_format($replace['<Year 2 Savings>'], 0, '', ','));

    is_email($data['email-email'] ?? '') && wp_mail(
        $data['email-email'],
        str_replace(array_keys($replace), array_values($replace), $subject),
        str_replace(array_keys($replace), array_values($replace), $body)
    );
});

function action_wpcf7_mail_sent( $contact_form ) { 

    $form_id = (int) get_option('roi_cf7_notification_form_id_redhat');
    $wpcf7      = WPCF7_ContactForm::get_current();
    $submission = WPCF7_Submission::get_instance();
    if(isset($form_id) && !empty($form_id) && ($form_id == $contact_form->id()) ){
    if ($submission) {
        $data = $submission->get_posted_data();

            $subject = '<First Name>, Save <Company Name> <Year 2 Savings> a Year!';
            $body = 'Hicc <First Name>,

It’s hero time! Based on your input on our website, Capital Innovations can save <Company Name> <Year 2 Savings> by migrating from <Current System> to RedHat Process Automation Manager.

And it gets better, Capital Innovations can fund this by re-allocating what you spend in <Months ROI Calculation> maintenance on <Current System>! And this doesn’t even begin to account for savings on infrastructure, expensive programming resources, etc.

Want some more detail? Grab some time on my calendar here: <Calendly link>

P.S. I’d be glad to share my calculations as well as success stories from our customers on our call :)';

    

            $replace = [
                '<First Name>' => $data['text-firstname'] ?? '',
                '<Desired Plaftorm>' => 'RedHat',
                '<Months ROI Calculation>' => '',
                '<Current System>' =>  $data['menu-currentplatform'][0] ?? '',
                '<Calendly link>' => get_option('roi_cf7_notification_calendly_link_redhat'),
                '<Company Name>' => $data['text-companyname'] ?? '',
                '<Year 2 Savings>' => '',
            ];

            roi_cf7_calculate_migration_savings( intval($data['menu-currentcores'][0] ?? ''), $replace );

            ($replace['<Year 2 Savings>'] ?? '') && ($replace['<Year 2 Savings>'] = '$' . number_format($replace['<Year 2 Savings>'], 0, '', ','));

            is_email($data['email-email'] ?? '') && wp_mail(
                $data['email-email'],
                str_replace(array_keys($replace), array_values($replace), $subject),
                str_replace(array_keys($replace), array_values($replace), $body)
            );

        }
    }
}; 
add_action( 'wpcf7_mail_sent', 'action_wpcf7_mail_sent', 10, 1 );