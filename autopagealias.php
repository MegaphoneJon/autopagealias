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
  $pageLookupParams = ['id' => $objectId, 'is_active' => 1, 'sequential' => 1];
  if ($objectName == 'Event') {
    $pageLookupParams['is_online_registration'] = 1;
  }
  $pageDetails = civicrm_api3($objectName, 'get', $pageLookupParams);
  if (!$pageDetails['count']) {
    return;
  }

  $originalPath = CRM_Autopagealias_Utils::getOriginalPath($objectName, $objectId);
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
      $form->assign('autopagealias', $url);
      CRM_Core_Region::instance('form-top')->add([
        'template' => 'autopagealias.tpl',
      ]);
      CRM_Core_Resources::singleton()->addScriptFile('autopagealias', 'js/autopagealias.js');
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
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function autopagealias_civicrm_install() {
  _autopagealias_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function autopagealias_civicrm_enable() {
  _autopagealias_civix_civicrm_enable();
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *

 // */

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
