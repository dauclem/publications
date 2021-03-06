<?php

namespace Interfaces\Shared;

use Interfaces\Shared;

/**
 * Class to manage all Config
 */
interface Config extends Shared {
	/**
	 * Call this method for reset config install
	 */
	public function install();

	/**
	 * Get site base url
	 *
	 * @return string
	 */
	public function getSiteUrl();

	/**
	 * Get VCS ident to get correct class name
	 *
	 * @return string
	 */
	public function getVcsType();

	/**
	 * Get VCS repository base url
	 *
	 * @return string
	 */
	public function getVcsUrl();

	/**
	 * Get VCS user name
	 *
	 * @return string
	 */
	public function getVcsUser();

	/**
	 * Get VCS user password
	 *
	 * @return string
	 */
	public function getVcsPassword();

	/**
	 * Get VCS base url for web diff displayed
	 *
	 * @return string
	 */
	public function getVcsWebUrl();

	/**
	 * Get changelog file path from project root (for example : core/changelog)
	 *
	 * @return string
	 */
	public function getChangelogPath();

	/**
	 * Get bug tracker ident to get correct class name
	 *
	 * @return string
	 */
	public function getBugTrackerType();

	/**
	 * Get bug tracker base url
	 *
	 * @return string
	 */
	public function getBugTrackerUrl();

	/**
	 * Get bug tracker user name
	 *
	 * @return string
	 */
	public function getBugTrackerUser();

	/**
	 * Get bug tracker user password
	 *
	 * @return string
	 */
	public function getBugTrackerPassword();

	/**
	 * Get bug tracker query begin to get valid tracker object for publications
	 *
	 * @return string
	 */
	public function getBugTrackerQuery();

	/**
	 * Get Mail content template
	 *
	 * @return string
	 */
	public function getMailContent();

	/**
	 * Get Mail subject template
	 *
	 * @return string
	 */
	public function getMailSubject();

	/**
	 * Get mail sender address to automatic emails
	 *
	 * @return string
	 */
	public function getMailSender();

	/**
	 * Get Mail content template in case of post publication
	 *
	 * @return string
	 */
	public function getMailPostPubliContent();

	/**
	 * Get Mail subject template in case of post publication
	 *
	 * @return string
	 */
	public function getMailPostPubliSubject();

	/**
	 * Get Bug Tracker additional field to send notification is not empty
	 *
	 * @return string
	 */
	public function getBugTrackerFieldRestrictNotif();

	/**
	 * Get Mail subject template in case of notification on restrict bug tracker field
	 *
	 * @return string
	 */
	public function getMailRestrictSubject();

	/**
	 * Get Mail content template in case of notification on restrict bug tracker field
	 *
	 * @return string
	 */
	public function getMailRestrictContent();

	/**
	 * Set VCS ident to set correct class name
	 *
	 * @param string $vcs_type
	 */
	public function setVcsType($vcs_type);

	/**
	 * Set VCS repository base url
	 *
	 * @param string $vcs_url
	 */
	public function setVcsUrl($vcs_url);

	/**
	 * Set VCS user name
	 *
	 * @param string $vcs_user
	 */
	public function setVcsUser($vcs_user);

	/**
	 * Set VCS user password
	 *
	 * @param string $vcs_password
	 */
	public function setVcsPassword($vcs_password);

	/**
	 * Set VCS base url for web diff displayed
	 *
	 * @param string $vcs_web_url
	 */
	public function setVcsWebUrl($vcs_web_url);

	/**
	 * Set changelog file path
	 *
	 * @param string $changelog_path
	 */
	public function setChangelogPath($changelog_path);

	/**
	 * Set bug tracker ident to set correct class name
	 *
	 * @param string $bug_tracker_type
	 */
	public function setBugTrackerType($bug_tracker_type);

	/**
	 * Set bug tracker base url
	 *
	 * @param string $bug_tracker_url
	 */
	public function setBugTrackerUrl($bug_tracker_url);

	/**
	 * Set bug tracker user name
	 *
	 * @param string $bug_tracker_user
	 */
	public function setBugTrackerUser($bug_tracker_user);

	/**
	 * Set bug tracker user password
	 *
	 * @param string $bug_tracker_password
	 */
	public function setBugTrackerPassword($bug_tracker_password);

	/**
	 * Set bug tracker query begin to get valid tracker object for publications
	 *
	 * @param string $bug_tracker_query
	 */
	public function setBugTrackerQuery($bug_tracker_query);

	/**
	 * Set mail content template
	 *
	 * @param string $mail_content
	 */
	public function setMailContent($mail_content);

	/**
	 * Set mail subject template
	 *
	 * @param string $mail_subject
	 */
	public function setMailSubject($mail_subject);

	/**
	 * Set mail sender address to automatic emails
	 *
	 * @param string $mail_sender
	 */
	public function setMailSender($mail_sender);

	/**
	 * Set mail content template in case of post publication
	 *
	 * @param string $mail_post_publi_content
	 */
	public function setMailPostPubliContent($mail_post_publi_content);

	/**
	 * Set mail subject template in case of post publication
	 *
	 * @param string $mail_post_publi_subject
	 */
	public function setMailPostPubliSubject($mail_post_publi_subject);

	/**
	 * Set Bug Tracker additional field to send notification is not empty
	 *
	 * @param string $bug_tracker_field_restrict_notif
	 */
	public function setBugTrackerFieldRestrictNotif($bug_tracker_field_restrict_notif);

	/**
	 * Set Mail subject template in case of notification on restrict bug tracker field
	 *
	 * @param string $mail_restrict_subject
	 */
	public function setMailRestrictSubject($mail_restrict_subject);

	/**
	 * Set Mail content template in case of notification on restrict bug tracker field
	 *
	 * @param string $mail_restrict_content
	 */
	public function setMailRestrictContent($mail_restrict_content);

	/**
	 * Add a recipient for this all projects
	 *
	 * @param string $email must be only email address
	 */
	public function addRecipient($email);

	/**
	 * Remove a recipient for all projects
	 *
	 * @param string $email must be only email address
	 */
	public function removeRecipient($email);

	/**
	 * Get list of Email recipients for publication notification for all projects
	 *
	 * @return string[]
	 */
	public function getRecipients();
}
