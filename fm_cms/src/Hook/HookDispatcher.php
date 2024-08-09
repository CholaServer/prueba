<?php

namespace Flormoments\Module\Cms\Hook;

use Fm_Cms;
use RuntimeException;

class HookDispatcher
{
    const HOOK_CLASSES = [
        CmsPageCategoryHook::class,
        CmsPageHook::class
    ];

    /**
     * @var string[]
     */
    private $hookNames;

    /**
     * @var AbstractHook[]
     */
    private $hookInstances;

    /**
     * @var Fm_Cms
     */
    private $module;

    /**
     * HookDispatcher constructor.
     * @param Fm_Cms $module
     */
    public function __construct(Fm_Cms $module)
    {
        $this->module = $module;
        $this->hookNames = [];
        $this->hookInstances = [];
        $this->loadHooks();
    }

    /**
     * @return string[]
     */
    public function getHookNames(): array
    {
        return $this->hookNames;
    }

    public function dispatch($hookName, array $params = [])
    {
        $hookName = preg_replace('~^hook~', '', $hookName);

        foreach ($this->hookInstances as $hook) {
            if (method_exists($hook, $hookName)) {
                return call_user_func([$hook, $hookName], $params);
            }
        }

        throw new RuntimeException(sprintf('No hook with name %s found', $hookName));
    }

    private function loadHooks()
    {
        foreach (self::HOOK_CLASSES as $hookClass) {
            /** @var AbstractHook $hook */
            $hook = new $hookClass($this->module);
            $this->hookNames = array_merge($this->hookNames, $hook->getHooks());
            $this->hookInstances[] = $hook;
        }
    }


}