<?php

namespace Flormoments\Module\Cms\Form\FormModifier;

use Flormoments\Module\Cms\Entity\CMSPageExtra;
use Flormoments\Module\Cms\Repository\CMSPageExtraRepository;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CmsPageFormBuilderModifier
{
    private $translator;
    private $languages;
    private $context;
    /**
     * @var CMSPageExtraRepository
     */
    private $cmsPageExtraRepository;

    /**
     * CmsPageFormBuilderModifier constructor.
     * @param $translator
     * @param $languages
     * @param $context
     * @param CMSPageExtraRepository $cmsPageExtraRepository
     */
    public function __construct($translator, $languages, $context, CMSPageExtraRepository $cmsPageExtraRepository)
    {
        $this->translator = $translator;
        $this->languages = $languages;
        $this->context = $context;
        $this->cmsPageExtraRepository = $cmsPageExtraRepository;
    }

    /**
     * @param array $params
     */
    public function modify(array $params)
    {
        $this->provideData($params);

        /** @var \Symfony\Component\Form\FormBuilder $formBuilder */
        $formBuilder = $params['form_builder'];

        $formBuilder
            ->add('seoText', TranslateType::class, [
                'label' => 'Texto seo', // TODO: Use translator
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

        $formBuilder->add('relatedPagesTitle', TextType::class, [
            'label' => 'Título bloque páginas relacionadas',
            'required' => false,
        ]);

        $formBuilder->add('relatedPagesIds', TextType::class, [
            'label' => 'Páginas relacionadas',
            'required' => false,
            'help' => 'Ids separados por coma de las páginas relacionadas con esta página'
        ]);

        $formBuilder->add('linkText', TextType::class, [
            'label' => 'Texto de enlace',
            'required' => false,
        ]);

        $formBuilder->setData($params['data'], $params);
    }

    private function provideData(array &$params)
    {
        $cmsId = $params['id'];

        if ($cmsId) {
            /** @var CMSPageExtra $cmsPageExtra */
            $cmsPageExtra = $this->cmsPageExtraRepository->findOneBy([
                'cmsId' => $cmsId,
                'langId' => $this->context->language->id
            ]);

            if ($cmsPageExtra) {
                $params['data']['seoText'] = [$cmsPageExtra->getLangId() => $cmsPageExtra->getSeoText()];
                $params['data']['showProducts'] = $cmsPageExtra->isShowProducts();
                $params['data']['relatedPagesIds'] = $cmsPageExtra->getRelatedPagesIds();
                $params['data']['relatedPagesTitle'] = $cmsPageExtra->getRelatedPagesTitle();
                $params['data']['linkText'] = $cmsPageExtra->getLinkText();
            }
        }
    }
}