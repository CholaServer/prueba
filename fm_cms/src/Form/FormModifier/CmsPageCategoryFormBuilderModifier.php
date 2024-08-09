<?php

namespace Flormoments\Module\Cms\Form\FormModifier;

use Flormoments\Module\Cms\Entity\CMSCategoryExtra;
use Flormoments\Module\Cms\Repository\CMSCategoryExtraRepository;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CmsPageCategoryFormBuilderModifier
{
    private $translator;
    private $languages;
    private $context;
    /**
     * @var CMSCategoryExtraRepository
     */
    private $cmsCategoryExtraRepository;

    /**
     * CmsPageCategoryFormBuilderModifier constructor.
     * @param $translator
     * @param $languages
     * @param $context
     * @param CMSCategoryExtraRepository $cmsCategoryExtraRepository
     */
    public function __construct($translator, $languages, $context, CMSCategoryExtraRepository $cmsCategoryExtraRepository)
    {
        $this->translator = $translator;
        $this->languages = $languages;
        $this->context = $context;
        $this->cmsCategoryExtraRepository = $cmsCategoryExtraRepository;
    }

    public function modify(array $params)
    {
        $this->provideData($params);

        /** @var \Symfony\Component\Form\FormBuilder $formBuilder */
        $formBuilder = $params['form_builder'];

        $formBuilder
            ->add('description', TranslateType::class, [
                'type' => FormattedTextareaType::class,
                'locales' => $this->languages,
                'hideTabs' => false,
                'required' => false,
                'options' => [
                    'constraints' => [
                        new CleanHtml([
                            'message' => $this->translator->trans(
                                '%s is invalid.',
                                [],
                                'Admin.Notifications.Error'
                            ),
                        ]),
                    ],
                ],
            ]);

        $formBuilder
            ->add('seoText', TranslateType::class, [
                'type' => FormattedTextareaType::class,
                'locales' => $this->languages,
                'hideTabs' => false,
                'required' => false,
                'options' => [
                    'constraints' => [
                        new CleanHtml([
                            'message' => $this->translator->trans(
                                '%s is invalid.',
                                [],
                                'Admin.Notifications.Error'
                            ),
                        ]),
                    ],
                ],
            ]);

        $formBuilder->add('showProducts', SwitchType::class, [
            'label' => 'Mostrar productos',
            'required' => false,
        ]);

        $formBuilder->add('featuredPagesIds', TextType::class, [
            'label' => 'Páginas destacadas',
            'required' => false,
        ]);

        $formBuilder->add('relatedCategoriesTitle', TextType::class, [
            'label' => 'Título bloque categorías relacionadas',
            'required' => false,
        ]);

        $formBuilder->add('relatedCategoriesIds', TextType::class, [
            'label' => 'Categorías relacionadas',
            'required' => false,
            'help' => 'Ids separados por coma de las categorías relacionadas con esta página'
        ]);

        $formBuilder->add('linkText', TextType::class, [
            'label' => 'Texto de enlace',
            'required' => false,
        ]);

        $formBuilder->setData($params['data'], $params);
    }

    /**
     * @param array $params
     */
    private function provideData(array &$params)
    {
        $cmsCategoryId = $params['id'];

        if ($cmsCategoryId) {
            /** @var CMSCategoryExtra $cmsCategoryExtra */
            $cmsCategoryExtra = $this->cmsCategoryExtraRepository->findOneBy([
                'cmsCategoryId' => $cmsCategoryId,
                'langId' => $this->context->language->id
            ]);

            if ($cmsCategoryExtra) {
                $params['data']['seoText'] = [$cmsCategoryExtra->getLangId() => $cmsCategoryExtra->getSeoText()];
                $params['data']['showProducts'] = $cmsCategoryExtra->isShowProducts();
                $params['data']['featuredPagesIds'] = $cmsCategoryExtra->getFeaturedPagesIds();
                $params['data']['relatedCategoriesIds'] = $cmsCategoryExtra->getRelatedCategoriesIds();
                $params['data']['relatedCategoriesTitle'] = $cmsCategoryExtra->getRelatedCategoriesTitle();
                $params['data']['linkText'] = $cmsCategoryExtra->getLinkText();
            }
        }
    }
}