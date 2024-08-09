<?php

namespace Flormoments\Module\Cms\Repository;


use Doctrine\DBAL\Connection;

class CMSRepository
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string the Database prefix
     */
    private $databasePrefix;

    /**
     * CMSPageRepository constructor.
     * @param Connection $connection
     * @param string $databasePrefix
     */
    public function __construct(Connection $connection, string $databasePrefix)
    {
        $this->connection = $connection;
        $this->databasePrefix = $databasePrefix;
    }

    /**
     * @param array $ids
     * @param int $langId
     * @return array
     */
    public function findPageTitlesWhereIdsIn(array $ids, int $langId): array
    {
        $qb = $this->connection->createQueryBuilder();

        $qb
            ->select('c.id_cms as id, c.meta_title as title, e.link_text as linkText')
            ->from($this->databasePrefix . 'cms_lang', 'c')
            ->leftJoin(
                'c',
                $this->databasePrefix . 'cms_page_extra',
                'e',
                'e.id_cms = c.id_cms AND e.id_lang = :langId'
            )
            ->where('c.id_lang = :langId AND c.id_cms IN (:ids)')
            ->setParameter('langId', $langId)
            ->setParameter('ids', $ids, Connection::PARAM_INT_ARRAY);


        return $qb->execute()->fetchAll();
    }

    /**
     * @param array $ids
     * @param int $langId
     * @return array
     */
    public function findCategoriesTitlesWhereIdsIn(array $ids, int $langId): array
    {
        $qb = $this->connection->createQueryBuilder();

        $qb
            ->select('c.id_cms_category as id, c.name as title')
            ->from($this->databasePrefix . 'cms_category_lang', 'c')
            ->where('c.id_lang = :langId AND c.id_cms_category IN (:ids)')
            ->setParameter('langId', $langId)
            ->setParameter('ids', $ids, Connection::PARAM_INT_ARRAY);

        return $qb->execute()->fetchAll();
    }
}
