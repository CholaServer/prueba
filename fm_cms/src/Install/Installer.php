<?php

namespace Flormoments\Module\Cms\Install;

use Fm_Cms;

class Installer
{
    const TABLE_CMS_PAGE_EXTRA = _DB_PREFIX_ . 'cms_page_extra';
    const TABLE_CMS_CATEGORY_EXTRA = _DB_PREFIX_ . 'cms_category_extra';

    /**
     * @var Fm_Cms
     */
    private $module;

    /**
     * Installer constructor.
     * @param Fm_Cms $module
     */
    public function __construct(Fm_Cms $module)
    {
        $this->module = $module;
    }

    /**
     * @return bool
     */
    public function install(): bool
    {
        $hookDispatcher = $this->module->getHookDispatcher();
        if (!$this->module->registerHook($hookDispatcher->getHookNames())) {
            return false;
        }

        if (!$this->installDatabase()) {
            return false;
        }

        return true;
    }

    /**
     * Module's uninstallation entry point.
     *
     * @return bool
     */
    public function uninstall(): bool
    {
        return $this->uninstallDatabase();
    }

    /**
     * Install the database modifications required for this module.
     *
     * @return bool
     */
    private function installDatabase(): bool
    {
        return $this->executeQueries([
            'CREATE TABLE IF NOT EXISTS `' . self::TABLE_CMS_CATEGORY_EXTRA . '` (
              `id_cms_category_extra` int(11) NOT NULL AUTO_INCREMENT,
              `id_cms_category` int(11) NOT NULL,
              `id_lang` int(11) NOT NULL,
              `seo_text` text NOT NULL,
              `show_products` boolean NOT NULL DEFAULT 0,
              `featured_pages_ids` varchar(255) NOT NULL DEFAULT "",
              `related_categories_title` varchar(255) NOT NULL DEFAULT "",
              `related_categories_ids` varchar(255) NOT NULL DEFAULT "",
              `link_text` varchar(255) NOT NULL DEFAULT "",
              PRIMARY KEY (`id_cms_category_extra`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',
            // cms_page_extra
            'CREATE TABLE IF NOT EXISTS `' . self::TABLE_CMS_PAGE_EXTRA . '` (
              `id_cms_page_extra` int(11) NOT NULL AUTO_INCREMENT,
              `id_cms` int(11) NOT NULL,
              `id_lang` int(11) NOT NULL,
              `seo_text` text NOT NULL,
              `show_products` boolean NOT NULL DEFAULT 0,
              `related_pages_title` varchar(255) NOT NULL DEFAULT "",
              `related_pages_ids` varchar(255) NOT NULL DEFAULT "",
              `link_text` varchar(255) NOT NULL DEFAULT "",
              PRIMARY KEY (`id_cms_page_extra`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',
        ]);
    }

    /**
     * Uninstall database modifications.
     *
     * @return bool
     */
    private function uninstallDatabase(): bool
    {
        return $this->executeQueries([
            'DROP TABLE IF EXISTS `' . self::TABLE_CMS_CATEGORY_EXTRA . '`',
            'DROP TABLE IF EXISTS `' . self::TABLE_CMS_PAGE_EXTRA . '`',
        ]);
    }

    private function executeQueries(array $queries): bool
    {
        $db = $this->module->getDatabase();
        foreach ($queries as $query) {
            if (!$db->execute($query)) {
                return false;
            }
        }

        return true;
    }
}