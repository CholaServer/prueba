<?php

class CMSCategory extends CMSCategoryCore
{

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        self::$definition['fields']['description'] = ['type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 3999999999999];
        parent::__construct($id, $id_lang, $id_shop);
    }
}