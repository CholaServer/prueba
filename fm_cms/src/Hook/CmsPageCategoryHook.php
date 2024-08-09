<?php

namespace Flormoments\Module\Cms\Hook;

use CMSCategory;
use Flormoments\Module\Cms\Dto\UpsertCategoryExtraDto;
use Flormoments\Module\Cms\Entity\CMSCategoryExtra;
use Flormoments\Module\Cms\Form\FormModifier\CmsPageCategoryFormBuilderModifier;
use Flormoments\Module\Cms\Helper\SmartyAssignProductsHelper;
use Flormoments\Module\Cms\Repository\CMSCategoryExtraRepository;
use Flormoments\Module\Cms\Repository\CMSPageExtraRepository;
use Flormoments\Module\Cms\Repository\CMSRepository;
use Fm_Cms;

class CmsPageCategoryHook extends AbstractHook
{
    const HOOKS = [
        'actionCmsPageCategoryFormBuilderModifier',
        'actionAfterUpdateCmsPageCategoryFormHandler',
        'actionAfterCreateCmsPageCategoryFormHandler',
        'actionObjectCMSCategoryDeleteAfter',
        'filterCmsCategoryContent',
        'displayCmsCategorySeoText',
        'displayCmsCategoryProducts',
        'displayCmsCategoryRelatedCategoriesLinks'
    ];

    /**
     * @var CmsPageCategoryFormBuilderModifier
     */
    private $cmsPageCategoryFormBuilderModifier;

    /**
     * @var CMSCategoryExtraRepository
     */
    private $cmsCategoryExtraRepository;

    /**
     * @var CMSPageExtraRepository
     */
    private $cmsPageExtraRepository;

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

        $this->cmsCategoryExtraRepository = $container->get(
            'fm.module.cms.repository.cms_category_extra_repository'
        );

        $this->cmsPageExtraRepository = $container->get(
            'fm.module.cms.repository.cms_page_extra_repository'
        );

        $this->cmsPageCategoryFormBuilderModifier = new CmsPageCategoryFormBuilderModifier(
            $this->module->getTranslator(),
            $this->module->getLanguages(),
            $this->module->getContext(),
            $this->cmsCategoryExtraRepository
        );

        $this->initialized = true;
    }

    public function actionCmsPageCategoryFormBuilderModifier(array $params)
    {
        $this->initialize();
        $this->cmsPageCategoryFormBuilderModifier->modify($params);
    }

    public function actionAfterCreateCmsPageCategoryFormHandler(array $params)
    {
        $this->initialize();
        $this->actionAfterUpdateCmsPageCategoryFormHandler($params);
    }

    public function actionAfterUpdateCmsPageCategoryFormHandler(array $params)
    {
        $this->initialize();
        $container = $this->module->getContainer();
        $langId = $this->module->getCurrentLanguageId();
        /** @var CMSCategoryExtraRepository $cmsCategoryExtraRepository */
        $cmsCategoryExtraRepository = $container->get(
            'fm.module.cms.repository.cms_category_extra_repository'
        );

        $seoText = array_shift($params['form_data']['seoText']);

        $upsertCategoryExtraDto = new UpsertCategoryExtraDto(
            $params['id'],
            $langId,
            $seoText,
            $params['form_data']['showProducts'] ?? false,
            $params['form_data']['featuredPagesIds'] ?? '',
            $params['form_data']['relatedCategoriesIds'] ?? '',
            $params['form_data']['relatedCategoriesTitle'] ?? '',
            $params['form_data']['linkText'] ?? ''
        );

        $cmsCategoryExtraRepository->upsert($upsertCategoryExtraDto);
    }

    public function actionObjectCMSCategoryDeleteAfter(array $params)
    {
        $this->initialize();
        $container = $this->module->getContainer();
        /** @var CMSCategory $cmsCategory */
        $cmsCategory = $params['object'];
        /** @var CMSCategoryExtraRepository $cmsCategoryExtraRepository */
        $cmsCategoryExtraRepository = $container->get(
            'fm.module.cms.repository.cms_category_extra_repository'
        );
        $cmsCategoryExtraRepository->deleteByCmsCategoryId($cmsCategory->id);
    }

    public function displayCmsCategorySeoText(array $params)
    {
        $this->initialize();
        $cmsCategoryId = $params['categoryId'];
        $context = $this->module->getContext();

        /** @var CMSCategoryExtra $categoryExtra */
        $categoryExtra = $this->findCategoryExtra($cmsCategoryId);

        if ($categoryExtra) {
            $context->smarty->assign('seoText', $categoryExtra->getSeoText());
        }

        return $context->smarty->fetch('module:fm_cms/views/templates/hook/cms_seo_text.tpl');
    }

    public function displayCmsCategoryProducts(array $params)
    {
        $this->initialize();
        $cmsCategoryId = $params['categoryId'];
        $context = $this->module->getContext();

        /** @var CMSCategoryExtra $categoryExtra */
        $categoryExtra = $this->findCategoryExtra($cmsCategoryId);

        if ($categoryExtra) {
            $context->smarty->assign('showProducts', $categoryExtra->isShowProducts());
        }

        return $context->smarty->fetch('module:fm_cms/views/templates/hook/cms_products.tpl');
    }

    public function displayCmsCategoryRelatedCategoriesLinks(array $params)
    {
        $this->initialize();
        $cmsCategoryId = $params['categoryId'];
        $context = $this->module->getContext();
        $container = $this->module->getContainer();

        /** @var CMSCategoryExtra $categoryExtra */
        $categoryExtra = $this->findCategoryExtra($cmsCategoryId);
        if ($categoryExtra) {
            /** @var CMSRepository $cmsPageRepository */
            $cmsPageRepository = $container->get('fm.module.cms.repository.cms_repository');
            $relatedCategories = $cmsPageRepository->findCategoriesTitlesWhereIdsIn(
                $categoryExtra->getRelatedCategoriesIdsAsArray(),
                $this->module->getCurrentLanguageId()
            );

            $context->smarty->assign([
                'relatedCategories' => $relatedCategories,
                'relatedCategoriesTitle' => $categoryExtra->getRelatedCategoriesTitle()
            ]);
        }

        return $context->smarty->fetch('module:fm_cms/views/templates/hook/related_categories_links.tpl');
    }

    public function filterCmsCategoryContent(array $params)
    {
        $this->initialize();
        $langId = $this->module->getCurrentLanguageId();
        $context = $this->module->getContext();
        $subcategoriesIds = array_keys($params['object']['sub_categories']);
        $pagesIds = array_keys($params['object']['cms_pages']);
        $categoriesLinkText = [];
        $pagesLinkText = [];

        if (!empty($subcategoriesIds)) {
            $categoriesLinkText = $this->cmsCategoryExtraRepository->findLinkTextWhereIdsIn($subcategoriesIds, $langId);
        }

        if (!empty($pagesIds)) {
            $pagesLinkText = $this->cmsPageExtraRepository->findLinkTextWhereIdsIn($pagesIds, $langId);
        }

        $context->smarty->assign([
            'categoriesLinkText' => $categoriesLinkText,
            'pagesLinkText' => $pagesLinkText,
        ]);

    }

    private function findCategoryExtra(int $categoryId)
    {
        $this->initialize();
        $langId = $this->module->getCurrentLanguageId();
        $container = $this->module->getContainer();

        $cmsCategoryExtraRepository = $container->get(
            'fm.module.cms.repository.cms_category_extra_repository'
        );
        /** @var CMSCategoryExtra $categoryExtra */
        $categoryExtra = $cmsCategoryExtraRepository->findOneBy([
            'cmsCategoryId' => $categoryId, 'langId' => $langId
        ]);

        return $categoryExtra;
    }
}