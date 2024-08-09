<?php

namespace Flormoments\Module\Cms\Dto;

class UpsertCategoryExtraDto
{
    /**
     * @var int
     */
    private $cmsCategoryId;
    /**
     * @var int
     */
    private $langId;
    /**
     * @var string
     */
    private $seoText;
    /**
     * @var boolean
     */
    private $showProducts;
    /**
     * @var string
     */
    private $featuredPagesIds;

    /**
     * @var string
     */
    private $relatedCategoriesIds;

    /**
     * @var string
     */
    private $relatedCategoriesTitle;

    /**
     * @var string
     */
    private $linkText;

    /**
     * UpsertCategoryExtraDto constructor.
     * @param int $cmsCategoryId
     * @param int $langId
     * @param string $seoText
     * @param bool $showProducts
     * @param string $featuredPagesIds
     * @param string $relatedCategoriesIds
     * @param string $relatedCategoriesTitle
     * @param string $linkText
     */
    public function __construct(int $cmsCategoryId, int $langId, string $seoText, bool $showProducts, string $featuredPagesIds, string $relatedCategoriesIds, string $relatedCategoriesTitle, string $linkText)
    {
        $this->cmsCategoryId = $cmsCategoryId;
        $this->langId = $langId;
        $this->seoText = $seoText;
        $this->showProducts = $showProducts;
        $this->featuredPagesIds = $featuredPagesIds;
        $this->relatedCategoriesIds = $relatedCategoriesIds;
        $this->relatedCategoriesTitle = $relatedCategoriesTitle;
        $this->linkText = $linkText;
    }


    /**
     * @return int
     */
    public function getCmsCategoryId(): int
    {
        return $this->cmsCategoryId;
    }

    /**
     * @return int
     */
    public function getLangId(): int
    {
        return $this->langId;
    }

    /**
     * @return string
     */
    public function getSeoText(): string
    {
        return $this->seoText;
    }

    /**
     * @return bool
     */
    public function isShowProducts(): bool
    {
        return $this->showProducts;
    }

    /**
     * @return string
     */
    public function getFeaturedPagesIds(): string
    {
        return $this->featuredPagesIds ?? '';
    }

    /**
     * @return string
     */
    public function getRelatedCategoriesIds(): string
    {
        return $this->relatedCategoriesIds;
    }

    /**
     * @return string
     */
    public function getRelatedCategoriesTitle(): string
    {
        return $this->relatedCategoriesTitle;
    }

    /**
     * @return string
     */
    public function getLinkText(): string
    {
        return $this->linkText;
    }

}