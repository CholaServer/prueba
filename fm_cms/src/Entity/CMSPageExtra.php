<?php

namespace Flormoments\Module\Cms\Entity;

use Doctrine\ORM\Mapping as ORM;
use Flormoments\Module\Cms\Dto\UpsertCMSPageExtraDto;

/**
 * @ORM\Table("ps_cms_page_extra")
 * @ORM\Entity(repositoryClass="Flormoments\Module\Cms\Repository\CMSPageExtraRepository")
 */
class CMSPageExtra implements ExtraContentInterface
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id_cms_page_extra", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="id_cms", type="integer")
     */
    private $cmsId;

    /**
     * @var int
     *
     * @ORM\Column(name="id_lang", type="integer")
     */
    private $langId;

    /**
     * @var string
     *
     * @ORM\Column(name="seo_text", type="text")
     */
    private $seoText;

    /**
     * @var bool
     *
     * @ORM\Column(name="show_products", type="boolean")
     */
    private $showProducts;


    /**
     * @var string
     *
     * @ORM\Column(name="related_pages_title", type="string")
     */
    private $relatedPagesTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="related_pages_ids", type="string")
     */
    private $relatedPagesIds;

    /**
     * @var string
     *
     * @ORM\Column(name="link_text", type="string")
     */
    private $linkText;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getCmsId(): int
    {
        return $this->cmsId;
    }

    /**
     * @param int $cmsId
     */
    public function setCmsId(int $cmsId): void
    {
        $this->cmsId = $cmsId;
    }

    /**
     * @return int
     */
    public function getLangId(): int
    {
        return $this->langId;
    }

    /**
     * @param int $langId
     */
    public function setLangId(int $langId): void
    {
        $this->langId = $langId;
    }

    /**
     * @return string
     */
    public function getSeoText(): string
    {
        return $this->seoText;
    }

    /**
     * @param string $seoText
     */
    public function setSeoText(string $seoText): void
    {
        $this->seoText = $seoText;
    }

    /**
     * @return bool
     */
    public function isShowProducts(): bool
    {
        return $this->showProducts;
    }

    /**
     * @param bool $showProducts
     */
    public function setShowProducts(bool $showProducts): void
    {
        $this->showProducts = $showProducts;
    }

    /**
     * @return string
     */
    public function getRelatedPagesIds(): string
    {
        return $this->relatedPagesIds;
    }

    /**
     * @param string $relatedPagesIds
     */
    public function setRelatedPagesIds(string $relatedPagesIds): void
    {
        $this->relatedPagesIds = $relatedPagesIds;
    }

    /**
     * @return array
     */
    public function getRelatedPagesIdsAsArray(): array
    {
        return explode(',', $this->getRelatedPagesIds());
    }

    /**
     * @return string
     */
    public function getRelatedPagesTitle(): string
    {
        return $this->relatedPagesTitle;
    }

    /**
     * @param string $relatedPagesTitle
     */
    public function setRelatedPagesTitle(string $relatedPagesTitle): void
    {
        $this->relatedPagesTitle = $relatedPagesTitle;
    }

    /**
     * @return string
     */
    public function getLinkText(): string
    {
        return $this->linkText;
    }

    /**
     * @param string $linkText
     */
    public function setLinkText(string $linkText): void
    {
        $this->linkText = $linkText;
    }

    public static function create(UpsertCMSPageExtraDto $upsertCMSPageExtraDto): self
    {
        $cmsPageExtra = new self();
        $cmsPageExtra->setCmsId($upsertCMSPageExtraDto->getCmsId());
        $cmsPageExtra->setLangId($upsertCMSPageExtraDto->getLangId());
        $cmsPageExtra->upsert($upsertCMSPageExtraDto);

        return $cmsPageExtra;
    }

    public function upsert(UpsertCMSPageExtraDto $upsertCMSPageExtraDto): void
    {
        $this->setSeoText($upsertCMSPageExtraDto->getSeoText());
        $this->setShowProducts($upsertCMSPageExtraDto->isShowProducts());
        $this->setRelatedPagesIds($upsertCMSPageExtraDto->getRelatedPagesIds());
        $this->setRelatedPagesTitle($upsertCMSPageExtraDto->getRelatedPagesTitle());
        $this->setLinkText($upsertCMSPageExtraDto->getLinkText());
    }
}