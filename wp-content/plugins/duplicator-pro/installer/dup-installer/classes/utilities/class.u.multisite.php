<?php
/**
 * Utility class for setting up Multi-site data
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @package SC\DUPX\MU
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

class DUPX_MU
{

    /**
     * 
     * @return int
     * 
     * @throws Exception
     */
    public static function newSiteAction()
    {
        $archiveConfig = DUPX_ArchiveConfig::getInstance();
        if ($archiveConfig->mu_mode == 0) {
            return DUPX_MultisiteMode::SingleSite;
        } else if (DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_MULTISITE_INST_TYPE) === 0) {
            switch ($archiveConfig->mu_mode) {
                case DUPX_MultisiteMode::Subdirectory:
                case DUPX_MultisiteMode::Subdomain:
                    return $archiveConfig->mu_mode;
                default:
                    throw new Exception('Multisi mode not valid');
            }
        } else {
            return DUPX_MultisiteMode::Standalone;
        }
    }

    public static function newSiteIsMultisite()
    {
        $newSiteAction = self::newSiteAction();
        return $newSiteAction == DUPX_MultisiteMode::Subdirectory || $newSiteAction == DUPX_MultisiteMode::Subdomain;
    }

    public static function convertSubsiteToStandalone($subsite_id)
    {
        DUPX_Log::info("STANDALONE: Convert to standalone subsite id ".DUPX_Log::varToString($subsite_id));
        //Had to move this up, so we can update the active_plugins option before it gets moved.
        self::makeSubsiteFilesStandalone($subsite_id);
    }

    private static function makeSubsiteFilesStandalone($subsite_id)
    {
        $success        = true;
        $archive_config = DUPX_ArchiveConfig::getInstance();
        $nManager       = DUPX_NOTICE_MANAGER::getInstance();
        $paramsManager  = DUPX_Paramas_Manager::getInstance();

        $is_old_mu         = $archive_config->mu_generation === 1 ? true : false;
        $wp_content_dir    = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_CONTENT_NEW);
        $subsite_blogs_dir = $wp_content_dir.'/blogs.dir';
        $uploads_dir       = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_UPLOADS_NEW);
        $uploads_sites_dir = $is_old_mu ? $subsite_blogs_dir : $uploads_dir.'/sites';
        $subsite_id        = (int) $subsite_id;

        DUPX_Log::info("STANDALONE: wp content dir ".DUPX_Log::varToString($wp_content_dir), DUPX_Log::LV_DETAILED);
        if ($subsite_id === 1) {
            try {
                if (!$is_old_mu) {
                    DUPX_U::deleteDirectory($uploads_sites_dir, true);
                } else {
                    DUPX_U::deleteDirectory($subsite_blogs_dir, true);
                }
            }
            catch (Exception $ex) {
                //RSR TODO: Technically it can complete but this should be brought to their attention more than just writing info
                DUPX_Log::info("STANDALONE ERROR : Problem deleting ".DUPX_Log::varToString($uploads_sites_dir)." MSG: ".$ex->getMessage());
                $nManager->addFinalReportNotice(array(
                    'shortMsg' => 'Problem deleting sites directory',
                    'level'    => DUPX_NOTICE_ITEM::SOFT_WARNING,
                    'longMsg'  => "STANDALONE ERROR : Problem deleting ".DUPX_Log::varToString($uploads_sites_dir)."\nMSG: ".$ex->getMessage(),
                    'sections' => 'files'
                ));
            }
        } else {
            $subsite_uploads_dir = $is_old_mu ? "{$uploads_sites_dir}/{$subsite_id}/files" : "{$uploads_sites_dir}/{$subsite_id}";
            DUPX_Log::info("STANDALONE: uploads dir ".DUPX_Log::varToString($subsite_uploads_dir));

            try {
                // Get a list of all files/subdirectories within the core uploads dir. For all 'non-sites' directories do a recursive delete. For all files, delete.
                if (file_exists($uploads_dir)) {
                    $filenames = array_diff(scandir($uploads_dir), array('.', '..'));
                    foreach ($filenames as $filename) {
                        $full_path = "$uploads_dir/$filename";
                        if (is_dir($full_path)) {
                            if ($filename != 'sites' || $is_old_mu) {
                                DUPX_Log::info("STANDALONE: Recursively deleting ".DUPX_Log::varToString($full_path), DUPX_Log::LV_DETAILED);
                                DUPX_U::deleteDirectory($full_path, true);
                            } else {
                                DUPX_Log::info("STANDALONE: Skipping ".DUPX_Log::varToString($full_path), DUPX_Log::LV_DETAILED);
                            }
                        } else {
                            $success = @unlink($full_path);
                        }
                    }
                }
            }
            catch (Exception $ex) {
                DUPX_Log::info("STANDALONE ERROR : Problem deleting ".DUPX_Log::varToString($uploads_dir)." MSG: ".$ex->getMessage());
                $nManager->addFinalReportNotice(array(
                    'shortMsg' => 'Problem deleting sites directory',
                    'level'    => DUPX_NOTICE_ITEM::SOFT_WARNING,
                    'longMsg'  => "STANDALONE ERROR : Problem deleting ".DUPX_Log::varToString($uploads_dir)."\nMSG: ".$ex->getMessage(),
                    'sections' => 'files'
                ));
            }

            DUPX_Log::info("STANDALONE: copy ".DUPX_Log::varToString($subsite_uploads_dir).' TO '.DUPX_Log::varToString($uploads_dir));
            // Recursively copy files in /wp-content/uploads/sites/$subsite_id to /wp-content/uploads
            DUPX_U::copyDirectory($subsite_uploads_dir, $uploads_dir);

            try {
                DUPX_Log::info("STANDALONE: Recursively deleting ".DUPX_Log::varToString($uploads_sites_dir), DUPX_Log::LV_DETAILED);
                // Delete /wp-content/uploads/sites (will get rid of all subsite directories)
                DUPX_U::deleteDirectory($uploads_sites_dir, true);
            }
            catch (Exception $ex) {
                DUPX_Log::info("STANDALONE ERROR : Problem deleting ".DUPX_Log::varToString($uploads_sites_dir)." MSG: ".$ex->getMessage());
                $nManager->addFinalReportNotice(array(
                    'shortMsg' => 'Problem deleting sites directory',
                    'level'    => DUPX_NOTICE_ITEM::SOFT_WARNING,
                    'longMsg'  => "STANDALONE ERROR : Problem deleting ".DUPX_Log::varToString($uploads_sites_dir)."\nMSG: ".$ex->getMessage(),
                    'sections' => 'files'
                ));
            }
        }
    }

    /**
     * 
     * @return array
     */
    public static function getSuperAdminsUserIds($dbh)
    {
        $result        = array();
        $paramsManager = DUPX_Paramas_Manager::getInstance();
        $archiveConfig = DUPX_ArchiveConfig::getInstance();

        $base_prefix      = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_TABLE_PREFIX);
        $users_table_name = "{$base_prefix}users";

        // Super admin should remain
        $siteAdmins = is_array($archiveConfig->mu_siteadmins) ? $archiveConfig->mu_siteadmins : array();
        if (!empty($siteAdmins)) {
            $sql                  = "SELECT ID FROM {$users_table_name} WHERE user_login IN ('".implode("','", $siteAdmins)."')";
            $super_admins_results = DUPX_DB::queryToArray($dbh, $sql);
            foreach ($super_admins_results as $super_admins_result) {
                $result[] = $super_admins_result[0];
            }
        }
        return $result;
    }

    public static function updateOptionsTable($retained_subsite_id, $dbh)
    {
        $paramsManager = DUPX_Paramas_Manager::getInstance();
        $archiveConfig = DUPX_ArchiveConfig::getInstance();

        $base_prefix             = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_TABLE_PREFIX);
        $retained_subsite_prefix = $archiveConfig->getSubsitePrefixByParam($retained_subsite_id);
        $options_table_name      = "{$base_prefix}options";

        if ($retained_subsite_prefix != $base_prefix) {
            DUPX_UpdateEngine::updateTablePrefix($dbh, $options_table_name, 'option_name', $retained_subsite_prefix, $base_prefix);
        }
    }

    // Purge non_site where meta_key in wp_usermeta starts with data from other subsite or root site,
    public static function purgeRedundantData($retained_subsite_id, $dbh)
    {
        $paramsManager = DUPX_Paramas_Manager::getInstance();
        if ($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_KEEP_TARGET_SITE_USERS) > 0) {
            DUPX_Log::info("STANDALONE: skip purging redundant data beacause keep target site users is enable ");
            return;
        }

        DUPX_Log::info("STANDALONE: purging redundant data. Considering ");

        $archiveConfig    = DUPX_ArchiveConfig::getInstance();
        $base_prefix      = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_TABLE_PREFIX);
        $remove_redundant = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_REMOVE_RENDUNDANT);

        $users_table_name        = "{$base_prefix}users";
        $usermeta_table_name     = "{$base_prefix}usermeta";
        $retained_subsite_prefix = $archiveConfig->getSubsitePrefixByParam($retained_subsite_id);
        $superAdminUsersIds      = self::getSuperAdminsUserIds($dbh);
        DUPX_Log::info("SUPER USER IDS: ".DUPX_Log::varToString($superAdminUsersIds), DUPX_Log::LV_DETAILED);

        //Remove all users which are not associated with the subsite that is being installed
        if ($remove_redundant) {
            $sql             = "SELECT user_id,meta_key FROM {$usermeta_table_name} WHERE meta_key LIKE '{$base_prefix}%_capabilities' OR meta_key = '{$base_prefix}capabilities'";
            $retain_meta_key = $retained_subsite_prefix."capabilities";
            $results         = DUPX_DB::queryToArray($dbh, $sql);
            DUPX_Log::info(print_r($results, true));
            $keep_users      = $superAdminUsersIds;
            foreach ($results as $result) {
                //$result[0] - user_id
                //$result[1] - meta_key
                if ($result[1] == $retain_meta_key) {
                    $keep_users[] = $result[0];
                }
            }
            $keep_users     = array_unique($keep_users);
            $keep_users_str = '('.implode(',', $keep_users).')';

            DUPX_Log::info("KEEP USERS IDS: ".DUPX_Log::varToString($keep_users), DUPX_Log::LV_DETAILED);
            $sql = "DELETE FROM {$users_table_name} WHERE id  NOT IN ".$keep_users_str;
            DUPX_DB::queryNoReturn($dbh, $sql);

            $sql = "DELETE FROM {$usermeta_table_name} WHERE user_id NOT IN ".$keep_users_str;
            DUPX_DB::queryNoReturn($dbh, $sql);
        }

        $escPergPrefix        = mysqli_real_escape_string($dbh, preg_quote($base_prefix, null /* no delimiter */));
        $escPergSubsitePrefix = mysqli_real_escape_string($dbh, preg_quote($retained_subsite_prefix, null /* no delimiter */));
        if ($retained_subsite_prefix == $base_prefix) {
            $sql = "DELETE FROM $usermeta_table_name WHERE meta_key REGEXP '^".$escPergPrefix."[0-9]+_';";
        } else {
            $sql = "DELETE FROM $usermeta_table_name WHERE meta_key NOT REGEXP '^".$escPergSubsitePrefix."' AND meta_key REGEXP '^".$escPergPrefix."';";
        }
        DUPX_DB::queryNoReturn($dbh, $sql);

        if ($retained_subsite_prefix != $base_prefix) {
            DUPX_UpdateEngine::updateTablePrefix($dbh, $usermeta_table_name, 'meta_key', $retained_subsite_prefix, $base_prefix);
        }

        if (!empty($superAdminUsersIds)) {
            $updateables = array(
                $base_prefix.'capabilities' => mysqli_real_escape_string($dbh, DUPX_WPConfig::ADMIN_SERIALIZED_SECURITY_STRING),
                $base_prefix.'user_level'   => DUPX_WPConfig::ADMIN_LEVEL
            );

            // Ad permission for superadmin users
            foreach ($superAdminUsersIds as $suId) {
                foreach ($updateables as $meta_key => $meta_value) {
                    if (($result = DUPX_DB::mysqli_query($dbh, "SELECT `umeta_id` FROM {$usermeta_table_name} WHERE `user_id` = {$suId} AND meta_key = '{$meta_key}'")) !== false) {
                        //If entry is present UPDATE otherwise INSERT
                        if ($result->num_rows > 0) {
                            $umeta_id = $result->fetch_object()->umeta_id;
                            if (DUPX_DB::mysqli_query($dbh, "UPDATE {$usermeta_table_name} SET `meta_value` = '{$meta_value}' WHERE `umeta_id` = {$umeta_id}") === false) {
                                DUPX_Log::info("Could not update meta field {$meta_key} for user with id {$suId}");
                            }
                        } else {
                            if (DUPX_DB::mysqli_query($dbh, "INSERT INTO `{$usermeta_table_name}` (user_id, meta_key, meta_value) VALUES ({$suId}, '{$meta_key}', '$meta_value')") === false) {
                                DUPX_Log::info("Could not populate meta field {$meta_key} with the value {$meta_value} for user with id {$suId}");
                            }
                        }
                        $result->free();
                    }
                }
            }
        }
    }
}