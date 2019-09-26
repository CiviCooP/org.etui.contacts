<?php

require_once 'contacts.civix.php';
use CRM_Contacts_ExtensionUtil as E;

function contacts_civicrm_tokens(&$tokens) {
  $tokens['ETUI'] = array(
    'etui.etui_addressee' => 'Person and/or Organisation name',
  );
}

function contacts_civicrm_tokenValues(&$values, $cids, $job = null, $tokens = array(), $context = null) {
  if (array_key_exists('etui', $tokens) && in_array('etui_addressee', $tokens['etui'])) {
    foreach ($cids as $cid) {
      // check the type of contact
      if ($values[$cid]['contact_type'] == 'Individual') {
        // check what type of address we have
        if (isset($values[$cid]['address_id'])) {
          $sql = "
            select 
              c.organization_name employer_name
              , c.addressee_display
              , a.location_type_id
              , c_master.display_name master_organization_name
            from 
              civicrm_contact c
            left outer join
              civicrm_address a ON a.contact_id = c.id and a.id = %2
            left outer join
              civicrm_address a_master ON a.master_id = a_master.id
            left outer join
              civicrm_contact c_master ON c_master.id = a_master.contact_id
            where
            c.id = %1
          ";
          $sqlParams = [
            1 => [$cid, 'Integer'],
            2 => [$values[$cid]['address_id'], 'Integer'],
          ];
          $dao = CRM_Core_DAO::executeQuery($sql, $sqlParams);
          if ($dao->fetch()) {
            if ($dao->location_type_id == 1 || $dao->location_type_id == 9) {
              // that's a home address or magazine address without organization
              // just show the addresse name of the person
              $values[$cid]['etui.etui_addressee'] = $dao->addressee_display;
            }
            else {
              // add the name of the person
              $addressee = $dao->addressee_display;

              // add the name of the correct organization
              // (if the address is linked to a contact, it gets precedence over the current employer
              if ($dao->master_organization_name) {
                $addressee .= "\n" . $dao->master_organization_name;
              }
              elseif ($dao->employer_name) {
                $addressee .= "\n" . $dao->employer_name;
              }

              $values[$cid]['etui.etui_addressee'] = $addressee;
            }
          }
          else {
            // no record?!? just show the display name
            $values[$cid]['etui.etui_addressee'] = $values[$cid]['display_name'];
          }
        }
        else {
          // no address id, just show the display name
          $values[$cid]['etui.etui_addressee'] = $values[$cid]['display_name'];
        }
      }
      else {
        // this is an organization: just show the display name
        $values[$cid]['etui.etui_addressee'] = $values[$cid]['display_name'];
      }
    }
  }
}

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function contacts_civicrm_config(&$config) {
  _contacts_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function contacts_civicrm_xmlMenu(&$files) {
  _contacts_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function contacts_civicrm_install() {
  _contacts_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function contacts_civicrm_postInstall() {
  _contacts_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function contacts_civicrm_uninstall() {
  _contacts_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function contacts_civicrm_enable() {
  _contacts_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function contacts_civicrm_disable() {
  _contacts_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function contacts_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _contacts_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function contacts_civicrm_managed(&$entities) {
  _contacts_civix_civicrm_managed($entities);
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
function contacts_civicrm_caseTypes(&$caseTypes) {
  _contacts_civix_civicrm_caseTypes($caseTypes);
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
function contacts_civicrm_angularModules(&$angularModules) {
  _contacts_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function contacts_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _contacts_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_entityTypes
 */
function contacts_civicrm_entityTypes(&$entityTypes) {
  _contacts_civix_civicrm_entityTypes($entityTypes);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function contacts_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function contacts_civicrm_navigationMenu(&$menu) {
  _contacts_civix_insert_navigation_menu($menu, 'Mailings', array(
    'label' => E::ts('New subliminal message'),
    'name' => 'mailing_subliminal_message',
    'url' => 'civicrm/mailing/subliminal',
    'permission' => 'access CiviMail',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _contacts_civix_navigationMenu($menu);
} // */
