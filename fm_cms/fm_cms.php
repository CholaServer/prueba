<?php

use Flormoments\Module\Cms\Hook\HookDispatcher;
use Flormoments\Module\Cms\Install\Installer;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';


class Fm_Cms extends Module
{
    const MODULE_NAME = 'fm_cms';

    /**
     * @var Db
     */
    private $db;

    /**
     * @var HookDispatcher
     */
    private $hookDispatcher;

    public function __construct()
    {
        $this->name = self::MODULE_NAME;
        $this->version = '1.0.0';
        $this->author = 'Flormoments';

        parent::__construct();

        $this->displayName = $this->trans(
            'Flormoments CMS extend',
            [],
            'Modules.Flormoments.Admin'
        );

        $this->description =
            $this->trans(
                'Module to extend the functionalities of the CMS pages',
                [],
                'Modules.Flormoments.Admin'
            );

        $this->ps_versions_compliancy = [
            'min' => '1.7.6.0',
            'max' => _PS_VERSION_,
        ];

        $this->hookDispatcher = new HookDispatcher($this);
    }

    public function isUsingNewTranslationSystem()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        $installer = new Installer($this);

        return $installer->install();
    }

    /**
     * @return bool
     */
    public function uninstall()
    {
        $installer = new Installer($this);

        return $installer->uninstall() && parent::uninstall();
    }

    /**
     * @return Db
     */
    public function getDatabase()
    {
        if (null === $this->db) {
            $this->db = Db::getInstance();
        }

        return $this->db;
    }

    /**
     * @return Context
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return HookDispatcher
     */
    public function getHookDispatcher(): HookDispatcher
    {
        return $this->hookDispatcher;
    }

    /**
     * Returns available languages.
     *
     * @param bool $active Select only active languages
     * @param int|bool $id_shop Shop ID
     * @param bool $ids_only If true, returns an array of language IDs
     *
     * @return array Languages
     */
    public function getLanguages($active = true, $id_shop = false, $ids_only = false)
    {
        return Language::getLanguages($active, $id_shop, $ids_only);
    }

    /**
     * @return int
     */
    public function getCurrentLanguageId()
    {
        return $this->context->language->id;
    }

    /**
     * Dispatch hooks
     *
     * @param string $methodName
     * @param array $arguments
     */
    public function __call($methodName, array $arguments)
    {
        return $this->getHookDispatcher()->dispatch(
            $methodName,
            !empty($arguments[0]) ? $arguments[0] : []
        );
    }
}