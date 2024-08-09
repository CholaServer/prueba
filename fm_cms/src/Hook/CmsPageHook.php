<?php

namespace Flormoments\Module\Cms\Hook;

use CMSCategory;
use CmsControllerCore;
use CMSCore;
use Flormoments\Module\Cms\Dto\UpsertCMSPageExtraDto;
use Flormoments\Module\Cms\Entity\CMSCategoryExtra;
use Flormoments\Module\Cms\Entity\CMSPageExtra;
use Flormoments\Module\Cms\Form\FormModifier\CmsPageFormBuilderModifier;
use Flormoments\Module\Cms\Helper\SmartyAssignProductsHelper;
use Flormoments\Module\Cms\Repository\CMSCategoryExtraRepository;
use Flormoments\Module\Cms\Repository\CMSPageExtraRepository;
use Flormoments\Module\Cms\Repository\CMSRepository;
use Fm_Cms;

class CmsPageHook extends AbstractHook
{
    const HOOKS = [
        'actionCmsPageFormBuilderModifier',
        'actionAfterUpdateCmsPageFormHandler',
        'actionAfterCreateCmsPageFormHandler',
        'actionObjectCMSDeleteAfter',
        'actionFrontControllerSetMedia',
        'displayCmsPageRelatedPagesLinks',
        'displayCmsPageFeaturedPagesLinks',
        'displayCmsPageProducts',
        'displayCmsPageSeoText'
    ];

    /**
     * @var CmsPageFormBuilderModifier
     */
    private $cmsPageFormBuilderModifier;

    /**
     * @var CMSPageExtraRepository
     */
    private $cmsPageExtraRepository;

    /**
     * @var CMSCategoryExtraRepository
     */
    private $cmsCategoryExtraRepository;

    /**
     * @var CMSRepository
     */
    private $cmsPageRepository;

    /**
     * @var boolean
     */
    private $initialized;

    public function __construct(Fm_Cms $module)
    {
        parent::__construct($module);
        $this->initialized = false;
    }

    private function initialize()
    {
        if($this->initialized) {
            return;
        }

        $container = $this->module->getContainer();

        $this->cmsPageExtraRepository = $container->get(
            'fm.module.cms.repository.cms_page_extra_repository'
        );

        $this->cmsCategoryExtraRepository = $container->get(
            'fm.module.cms.repository.cms_category_extra_repository'
        );

        $this->cmsPageRepository = $container->get('fm.module.cms.repository.cms_repository');

        $this->cmsPageFormBuilderModifier = new CmsPageFormBuilderModifier(
            $this->module->getTranslator(),
            $this->module->getLanguages(),
            $this->module->getContext(),
            $this->cmsPageExtraRepository
        );

        $this->initialized = true;
    }

    /**
     * Load js/css from productcomments module
     */
    public function actionFrontControllerSetMedia()
    {        
        $cssPath = '/modules/productcomments/views/css/productcomments.css';
        $jsPath = '/modules/productcomments/views/js/jquery.rating.plugin.js';
        $context = $this->module->getContext();

        if ($context->controller instanceof CmsControllerCore) {
            $context->controller->registerStylesheet(sha1($cssPath), $cssPath, ['media' => 'all', 'priority' => 80]);
            $context->controller->registerJavascript(sha1($jsPath), $jsPath, ['position' => 'bottom', 'priority' => 80]);
        }
    }

    public function actionCmsPageFormBuilderModifier(array $params)
    {
        $this->initialize();
        
        $this->cmsPageFormBuilderModifier->modify($params);
    }

    public function actionAfterCreateCmsPageFormHandler(array $params)
    {
        $this->actionAfterUpdateCmsPageFormHandler($params);
    }

    public function actionAfterUpdateCmsPageFormHandler(array $params)
    {
        $this->initialize();

        $langId = $this->module->getCurrentLanguageId();
        $seoText = array_shift($params['form_data']['seoText']);

        $upsertCmsPageExtra = new UpsertCMSPageExtraDto(
            $params['id'],
            $langId,
            $seoText,
            $params['form_data']['showProducts'] ?? false,
            $params['form_data']['relatedPagesIds'] ?? '',
            $params['form_data']['relatedPagesTitle'] ?? '',
            $params['form_data']['linkText'] ?? ''
        );

        $this->cmsPageExtraRepository->upsert($upsertCmsPageExtra);
    }

    public function actionObjectCMSDeleteAfter(array $params)
    {
        $this->initialize();

        /** @var CMSCore $cms */
        $cms = $params['object'];
        $this->cmsPageExtraRepository->deleteByCmsId($cms->id);
    }

    public function displayCmsPageRelatedPagesLinks(array $params)
    {
        $this->initialize();

        if (empty($params['pageId'])) {
            throw new \RuntimeException("Missing parameter pageId to hook displayCmsPageRelatedPagesLinks");
        }

        $pageId = $params['pageId'];
        $context = $this->module->getContext();
        $langId = $this->module->getCurrentLanguageId();

        /** @var CMSPageExtra $cmsPageExtra */
        $cmsPageExtra = $this->findPageExtra($pageId);

        if ($cmsPageExtra) {
            $relatedPagesIds = $cmsPageExtra->getRelatedPagesIdsAsArray();
            $relatedPages = $this->cmsPageRepository->findPageTitlesWhereIdsIn($relatedPagesIds, $langId);

            $context->smarty->assign([
                'relatedPages' => $relatedPages,
                'relatedPagesTitle' => $cmsPageExtra->getRelatedPagesTitle()
            ]);
        }

        return $context->smarty->fetch('module:fm_cms/views/templates/hook/related_pages_links.tpl');
    }

    public function displayCmsPageFeaturedPagesLinks(array $params)
    {
        $this->initialize();

        if (empty($params['categoryId'])) {
            throw new \RuntimeException("Missing parameter categoryId to hook displayCmsPageFeaturedPagesLinks");
        }

        $maxLevelDepth = 2;
        $categoryId = $params['categoryId'];
        $context = $this->module->getContext();
        $langId = $this->module->getCurrentLanguageId();

        /** @var CMSCategory $cmsCategory */
        $cmsCategory = new CMSCategory($categoryId);
        while ($cmsCategory && $cmsCategory->level_depth > $maxLevelDepth) {
            $cmsCategory = new CMSCategory($cmsCategory->id_parent);
        }

        if ($cmsCategory) {
            /** @var CMSCategoryExtra $cmsCategoryExtra */
            $cmsCategoryExtra = $this->cmsCategoryExtraRepository->findOneBy([
                'cmsCategoryId' => $cmsCategory->id,
                'langId' => $langId
            ]);

            if ($cmsCategoryExtra) {
                $featuredPages = $this->cmsPageRepository->findPageTitlesWhereIdsIn(
                    $cmsCategoryExtra->getFeaturedPagesIdsAsArray(),
                    $langId
                );

                $context->smarty->assign('featuredPages', $featuredPages);
            }
        }

        return $context->smarty->fetch('module:fm_cms/views/templates/hook/featured_pages_links.tpl');
    }

    public function displayCmsPageProducts(array $params)
    {
        $this->initialize();

        $pageId = $params['pageId'];
        $context = $this->module->getContext();

        /** @var CMSPageExtra $pageExtra */
        $pageExtra = $this->findPageExtra($pageId);

        if ($pageExtra) {
            $context->smarty->assign('showProducts', $pageExtra->isShowProducts());
        }

        return $context->smarty->fetch('module:fm_cms/views/templates/hook/cms_products.tpl');
    }

    public function displayCmsPageSeoText(array $params)
    {
        $this->initialize();

        $pageId = $params['pageId'];
        $context = $this->module->getContext();

        /** @var CMSCategoryExtra $pageExtra */
        $pageExtra = $this->findPageExtra($pageId);

        if ($pageExtra) {
            $context->smarty->assign('seoText', $pageExtra->getSeoText());
        }

        return $context->smarty->fetch('module:fm_cms/views/templates/hook/cms_seo_text.tpl');
    }

    private function findPageExtra(int $cmsId)
    {
        $langId = $this->module->getCurrentLanguageId();

        /** @var CMSPageExtra $cmsPageExtra */
        $cmsPageExtra = $this->cmsPageExtraRepository->findOneBy([
            'cmsId' => $cmsId,
            'langId' => $langId
        ]);

        return $cmsPageExtra;
    }
}