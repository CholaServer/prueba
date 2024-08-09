<?php


namespace Flormoments\Module\Cms\Dto;


class UpsertCMSPageExtraDto
{
    /**
     * @var int
     */
    private $cmsId;

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
    private $relatedPagesIds;

    /**
     * @var string
     */
    private $relatedPagesTitle;

    /**
     * @var string
     */
    private $linkText;

    /**
     * UpsertCMSPageExtraDto constructor.
     * @param int $cmsId
     * @param int $langId
     * @param string $seoText
     * @param bool $showProducts
     * @param string $relatedPagesIds
     * @param string $relatedPagesTitle
     * @param string $linkText
     */
    public function __construct(int $cmsId, int $langId, string $seoText, bool $showProducts, string $relatedPagesIds, string $relatedPagesTitle, string $linkText)
    {
        $this->cmsId = $cmsId;
        $this->langId = $langId;
        $this->seoText = $seoText;
        $this->showProducts = $showProducts;
        $this->relatedPagesIds = $relatedPagesIds;
        $this->relatedPagesTitle = $relatedPagesTitle;
        $this->linkText = $linkText;
    }

    /**
     * @return int
     */
    public function getCmsId(): int
    {
        return $this->cmsId;
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
    public function getRelatedPagesIds(): string
    {
        return $this->relatedPagesIds;
    }

    /**
     * @return string
     */
    public function getRelatedPagesTitle(): string
    {
        return $this->relatedPagesTitle;
    }

    public function getLinkText(): string
    {
        return $this->linkText;
    }


}