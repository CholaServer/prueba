<?php

namespace Flormoments\Module\Cms\Entity;

interface ExtraContentInterface
{
    /**
     * @return string
     */
    public function getSeoText(): string;

    /**
     * @param string $seoText
     */
    public function setSeoText(string $seoText): void;

    /**
     * @return bool
     */
    public function isShowProducts(): bool;

    /**
     * @param bool $showProducts
     */
    public function setShowProducts(bool $showProducts): void;
}