<?php

namespace Flormoments\Module\Cms\Helper;

use Context;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Adapter\Product\ProductDataProvider;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use Product;
use ProductAssembler;
use ProductPresenterFactory;

class SmartyAssignProductsHelper
{
    /**
     * @var Context
     */
    private $context;

    /**
     * SmartyAssignProductsHelper constructor.
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function assign()
    {
        $productProvider = new ProductDataProvider();
        $productObj = $productProvider->getProductInstance();
        $products = $productObj->getProducts($this->context->language->id, 0, 0, 'id_product', 'DESC');

        $assembler = new ProductAssembler($this->context);
        $presenterFactory = new ProductPresenterFactory($this->context);
        $presentationSettings = $presenterFactory->getPresentationSettings();
        $presenter = new ProductListingPresenter(
            new ImageRetriever(
                $this->context->link
            ),
            $this->context->link,
            new PriceFormatter(),
            new ProductColorsRetriever(),
            $this->context->getTranslator()
        );

        $products_for_template = [];
        foreach ($products as $rawProduct) {
            $products_for_template[] = $presenter->present(
                $presentationSettings,
                $assembler->assembleProduct($rawProduct),
                $this->context->language
            );
        }

        $this->context->smarty->assign('products', $products_for_template);
        $this->context->smarty->assign('feedtype', "cmsProductsFeed");
    }
}