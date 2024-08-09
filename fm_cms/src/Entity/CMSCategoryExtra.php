<?php

namespace Flormoments\Module\Cms\Entity;

use Doctrine\ORM\Mapping as ORM;
use Flormoments\Module\Cms\Dto\UpsertCategoryExtraDto;

/**
 * @ORM\Table("ps_cms_category_extra")
 * @ORM\Entity(repositoryClass="Flormoments\Module\Cms\Repository\CMSCategoryExtraRepository")
 */
class CMSCategoryExtra implements ExtraContentInterface
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id_cms_category_extra", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="id_cms_category", type="integer")
     */
    private $cmsCategoryId;

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
     * @ORM\Column(name="featured_pages_ids", type="string")
     */
    private $featuredPagesIds;

    /**
     * @var string
     *
     * @ORM\Column(name="related_categories_ids", type="string")
     */
    private $relatedCategoriesIds;

    /**
     * @var string
     *
     * @ORM\Column(name="related_categories_title", type="string")
     */
    private $relatedCategoriesTitle;

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
    public function getCmsCategoryId(): int
    {
        return $this->cmsCategoryId;
    }

    /**
     * @param int $cmsCategoryId
     */
    public function setCmsCategoryId(int $cmsCategoryId): void
    {
        $this->cmsCategoryId = $cmsCategoryId;
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
    public function getFeaturedPagesIds(): string
    {
        return $this->featuredPagesIds ?? '';
    }

    /**
     * @return array
     */
    public function getFeaturedPagesIdsAsArray(): array
    {
        return explode(',', $this->getFeaturedPagesIds());
    }

    /**
     * @param string $featuredPagesIds
     */
    public function setFeaturedPagesIds(string $featuredPagesIds): void
    {
        $this->featuredPagesIds = $featuredPagesIds;
    }

    /**
     * @return string
     */
    public function getRelatedCategoriesIds(): string
    {
        return $this->relatedCategoriesIds;
    }

    /**
     * @return array
     */
    public function getRelatedCategoriesIdsAsArray(): array
    {
        return explode(',', $this->getRelatedCategoriesIds());
    }

    /**
     * @param string $relatedCategoriesIds
     */
    public function setRelatedCategoriesIds(string $relatedCategoriesIds): void
    {
        $this->relatedCategoriesIds = $relatedCategoriesIds;
    }

    /**
     * @return string
     */
    public function getRelatedCategoriesTitle(): string
    {
        return $this->relatedCategoriesTitle;
    }

    /**
     * @param string $relatedCategoriesTitle
     */
    public function setRelatedCategoriesTitle(string $relatedCategoriesTitle): void
    {
        $this->relatedCategoriesTitle = $relatedCategoriesTitle;
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

    /**
     * @param UpsertCategoryExtraDto $upsertCategoryExtraDto
     * @return static
     */
    public static function create(UpsertCategoryExtraDto $upsertCategoryExtraDto): self
    {
        $categoryExtra = new self();
        $categoryExtra->setCmsCategoryId($upsertCategoryExtraDto->getCmsCategoryId());
        $categoryExtra->setLangId($upsertCategoryExtraDto->getLangId());
        $categoryExtra->upsert($upsertCategoryExtraDto);

        return $categoryExtra;
    }

    /**
     * @param UpsertCategoryExtraDto $upsertCategoryExtraDto
     */
    public function upsert(UpsertCategoryExtraDto $upsertCategoryExtraDto): void
    {
        $this->setSeoText($upsertCategoryExtraDto->getSeoText());
        $this->setShowProducts($upsertCategoryExtraDto->isShowProducts());
        $this->setFeaturedPagesIds($upsertCategoryExtraDto->getFeaturedPagesIds());
        $this->setRelatedCategoriesIds($upsertCategoryExtraDto->getRelatedCategoriesIds());
        $this->setRelatedCategoriesTitle($upsertCategoryExtraDto->getRelatedCategoriesTitle());
        $this->setLinkText($upsertCategoryExtraDto->getLinkText());
    }


}