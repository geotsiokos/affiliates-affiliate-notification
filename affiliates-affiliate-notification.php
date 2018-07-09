<?php
/**
 * Plugin Name: Affiliates Affiliate Notification
 * Plugin URI: http://www.netpad.gr
 * Description: Notify a user when registering as affiliate
 * Version: 1.0
 * Author: George Tsiokos
 * Author URI: http://www.netpad.gr
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Copyright (c) 2015-2016 "gtsiokos" George Tsiokos www.netpad.gr
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'check_logged_user' );

function check_logged_user() {
	if ( is_user_logged_in() ) {
		add_action( 'affiliates_added_affiliate', 'ntpd_affiliates_added_affiliate' );
	}
}

function ntpd_affiliates_added_affiliate( $affiliate_id ) {
	$plaintext_pass = ' ';
	if ( !class_exists( 'Affiliates_Registration' ) ) {
		include_once AFFILIATES_CORE_LIB . 'class-affiliates-registration.php';
	}
	$user_id = affiliates_get_affiliate_user( $affiliate_id );
	if ( $user_id = affiliates_get_affiliate_user( $affiliate_id ) ) {

		$user = get_userdata( $user_id );
		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		
		if ( get_option( 'aff_notify_affiliate_user', 'yes' ) != 'no' ) {
			$message  = sprintf( __( 'Username: %s', 'affiliates' ), $user->user_login) . "\r\n";
			$message .= sprintf( __( 'Password: %s', 'affiliates' ), $plaintext_pass ) . "\r\n";
			$message .= wp_login_url() . "\r\n";
			$params = array(
				'user_id'        => $user_id,
				'user'           => $user,
				'username'       => $user->user_login,
				'password'       => $plaintext_pass,
				'site_login_url' => wp_login_url(),
				'blogname'       => $blogname
			);
			@wp_mail(
				$user->user_email,
				apply_filters( 'affiliates_new_affiliate_user_registration_subject', sprintf( __( '[%s] Your username and password', 'affiliates' ), $blogname ), $params ),
				apply_filters( 'affiliates_new_affiliate_user_registration_message', $message, $params ),
				apply_filters( 'affiliates_new_affiliate_user_registration_headers', '', $params )
			);
		}
		Affiliates_Registration::new_affiliate_notification( $user_id );
	}
}