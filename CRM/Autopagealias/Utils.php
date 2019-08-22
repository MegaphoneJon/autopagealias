<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Utils
 *
 * @author jon
 */
class CRM_Autopagealias_Utils {

  /**
   * Given an entity type and an ID, return the corresponding public-facing page.
   * @param type $entity
   * @param type $id
   * @return type
   */
  public static function getOriginalPath($entity, $id) {
    // "ContributionPage" is correct, but it's wrong in core.  Hopefully to be fixed one day.
    if ($entity == 'ContributionPage' || $entity == 'Contribution') {
      $originalPath = CRM_Utils_System::url('civicrm/contribute/transact', "reset=1&id=$id", FALSE, NULL, FALSE);
    }
    else {
      $originalPath = CRM_Utils_System::url('civicrm/event/register', "reset=1&id=$id", FALSE, NULL, FALSE);
    }
    // Strip the leading slash.
    $originalPath = substr($originalPath, 1);
    return $originalPath;
  }

  /**
   * Given a path and a CMS type, check if an alias already exists.
   */
  public static function getAlias($originalPath, $cms) {
    switch ($cms) {
      case 'Drupal':
        $alias = self::getAliasDrupal($originalPath);
        break;

      case 'Backdrop':
        $alias = self::getAliasBackdrop($originalPath);
        break;

      default:
        $alias = FALSE;
    }
    return $alias;
  }

  public static function setAlias($originalPath, $slug, $cms) {
    switch ($cms) {
      case 'Drupal':
      case 'Backdrop':
        $alias = self::setAliasDrupal($originalPath, $slug);
        break;

      default:
        $alias = FALSE;
    }
    return $alias;
  }


  public static function getAliasDrupal($originalPath) {
    $alias = drupal_get_path_alias($originalPath);
    if ($alias == $originalPath) {
      $alias = FALSE;
    }
    return $alias;
  }

  public static function getAliasBackdrop($originalPath) {
    $alias = backdrop_get_path_alias($originalPath);
    if ($alias == $originalPath) {
      $alias = FALSE;
    }
    return $alias;
  }

  // Also works for Backdrop!
  public static function setAliasDrupal($originalPath, $slug) {
    $path = ['source' => $originalPath, 'alias' => $slug];
    path_save($path);
  }

  public static function slugify($text) {
    // replace non letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);

    // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);

    // trim
    $text = trim($text, '-');

    // remove duplicate -
    $text = preg_replace('~-+~', '-', $text);

    // lowercase
    $text = strtolower($text);

    if (empty($text)) {
      return 'n-a';
    }

    return $text;
  }

}
