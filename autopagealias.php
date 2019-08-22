<?php

require_once 'autopagealias.civix.php';

/**
 * Implements hook_civicrm_post().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_post
 *
 */
function autopagealias_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  // Are we updating a contribution page or event?
  if (!(in_array($op, ['create', 'edit']) && in_array($objectName, ['ContributionPage', 'Event']))) {
    return;
  }
  // If this pag isn't active, and/or this event isn't public, don't bother.
  $pageLookupParams = ['id' => $objectId, 'is_avtive' => 1, 'sequential' => 1];
  if ($objectName == 'Event') {
    $pageLookupParams['is_online_registration'] = 1;
  }
  $pageDetails = civicrm_api3($objectName, 'get', $pageLookupParams);
  if (!$pageDetails['count']) {
    return;
  }

  $originalPath = CRM_Autopagealias_Utils::getOriginalPath($entity, $objectId);
  // Get the CMS.
  $cms = CRM_Core_Config::singleton()->userFramework;

  // Do we already have a path alias?
  // NOTE: You can have multiple aliases to a single page, so this logic is somewhat arbitrary.
  $alias = CRM_Autopagealias_Utils::getAlias($originalPath, $cms);

  if (!$alias) {
    // OK, let's make up a new title.
    $humanTitle = $pageDetails['values'][0]['title'];
    $slug = CRM_Autopagealias_Utils::slugify($humanTitle);
    CRM_Autopagealias_Utils::setAlias($originalPath, $slug, $cms);
  }
}
/**
 * Display the alias on the settings page if we know it.
 * @param type $formName
 * @param type $form
 */
function autopagealias_civicrm_buildForm($formName, $form) {
  if (in_array($formName, ['CRM_Contribute_Form_ContributionPage_Settings', 'CRM_Event_Form_ManageEvent_EventInfo'])) {
    $entity = $form->getDefaultEntity();
    $originalPath = CRM_Autopagealias_Utils::getOriginalPath($entity, $form->getVar('_id'));
    $cms = CRM_Core_Config::singleton()->userFramework;
    $alias = CRM_Autopagealias_Utils::getAlias($originalPath, $cms);
    if ($alias) {
      $url = CRM_Utils_System::url($alias, NULL, TRUE);
      CRM_Core_Region::instance('form-top')->add([
        'markup' => "<span class=\"description\">Alias URL: <a href=\"$url\">$url</a></span>"
      ]);
    }

  }
}

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function autopagealias_civicrm_config(&$config) {
  _autopagealias_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function autopagealias_civicrm_xmlMenu(&$files) {
  _autopagealias_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function autopagealias_civicrm_install() {
  _autopagealias_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function autopagealias_civicrm_postInstall() {
  _autopagealias_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function autopagealias_civicrm_uninstall() {
  _autopagealias_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function autopagealias_civicrm_enable() {
  _autopagealias_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function autopagealias_civicrm_disable() {
  _autopagealias_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function autopagealias_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _autopagealias_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function autopagealias_civicrm_managed(&$entities) {
  _autopagealias_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function autopagealias_civicrm_caseTypes(&$caseTypes) {
  _autopagealias_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function autopagealias_civicrm_angularModules(&$angularModules) {
  _autopagealias_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function autopagealias_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _autopagealias_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_entityTypes
 */
function autopagealias_civicrm_entityTypes(&$entityTypes) {
  _autopagealias_civix_civicrm_entityTypes($entityTypes);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function autopagealias_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function autopagealias_civicrm_navigationMenu(&$menu) {
  _autopagealias_civix_insert_navigation_menu($menu, 'Mailings', array(
    'label' => E::ts('New subliminal message'),
    'name' => 'mailing_subliminal_message',
    'url' => 'civicrm/mailing/subliminal',
    'permission' => 'access CiviMail',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _autopagealias_civix_navigationMenu($menu);
} // */
