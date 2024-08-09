<?php

namespace Flormoments\Module\Cms\Hook;

use Fm_Cms;

abstract class AbstractHook
{
    const HOOKS = [];

    /**
     * @var Fm_Cms
     */
    protected $module;

    /**
     * AbstractHook constructor.
     * @param Fm_Cms $module
     */
    public function __construct(Fm_Cms $module)
    {
        $this->module = $module;
    }

    /**
     * @return array
     */
    public function getHooks()
    {
        return static::HOOKS;
    }
}