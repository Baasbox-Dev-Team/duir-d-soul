<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://baasbox.com
 * @since      1.0.0
 *
 * @package    Bb_Wiki
 * @subpackage Bb_Wiki/includes
 */

class Bb_Wiki_License_Checker {

    public function is_license_active() {

        $options = get_option('bb-wiki');

        if($options != null && $options['license_number']) {
            $body = [
                "license-number" => $options['license_number']
            ];

            $args = array(
                'body'        => $body,
                'timeout'     => '5',
                'redirection' => '5',
                'httpversion' => '1.0',
                'blocking'    => true,
                'headers'     => array(),
                'cookies'     => array(),
            );

            if(get_transient('bb_wiki_active_license') == false) {
                $response = wp_remote_post( 'https://4b048847-0aff-4a7a-9a5c-2d1db3194726.mock.pstmn.io/api/check-license', $args );
                $license_details = json_decode($response["body"]);

                if($license_details->is_active == true) {
                    set_transient('bb_wiki_active_license', true, 43200);
                    return true;
                } else {
                    return false;
                }
            } else {
                return get_transient('bb_wiki_active_license');
            }
        } else {
            return false;
        }

    }

}