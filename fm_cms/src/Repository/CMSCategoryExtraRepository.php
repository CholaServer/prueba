<?php

namespace Flormoments\Module\Cms\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDOStatement;
use Doctrine\ORM\EntityRepository;
use Flormoments\Module\Cms\Dto\UpsertCategoryExtraDto;
use Flormoments\Module\Cms\Entity\CMSCategoryExtra;

class CMSCategoryExtraRepository extends EntityRepository
{
    public function findById(int $id, int $langId)
    {
        return $this->findOneBy([
            'cmsCategoryId' => $id,
            'langId' => $langId,
        ]);
    }

    public function upsert(UpsertCategoryExtraDto $upsertCategoryExtraDto)
    {
        /** @var CMSCategoryExtra $cmsCategoryExtra */
        $cmsCategoryExtra = $this->findById(
            $upsertCategoryExtraDto->getCmsCategoryId(),
            $upsertCategoryExtraDto->getLangId()
        );

        if (!$cmsCategoryExtra) {
            $cmsCategoryExtra = CMSCategoryExtra::create($upsertCategoryExtraDto);
        } else {
            $cmsCategoryExtra->upsert($upsertCategoryExtraDto);
        }

        $em = $this->getEntityManager();
        $em->persist($cmsCategoryExtra);
        $em->flush();
    }

    /**
     * @param array $ids
     * @param int $langId
     * @return array
     */
    public function findLinkTextWhereIdsIn(array $ids, int $langId): array
    {
        $connection = $this->getEntityManager()->getConnection();
        $qb = $connection->createQueryBuilder();

        $qb
            ->select('c.id_cms_category, c.link_text')
            ->from(_DB_PREFIX_ . 'cms_category_extra', 'c')
            ->where('c.id_lang = :langId AND c.id_cms_category IN (:ids)')
            ->setParameter('langId', $langId)
            ->setParameter('ids', $ids, Connection::PARAM_INT_ARRAY);

        return $qb->execute()->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

    public function deleteByCmsCategoryId($cmsCategoryId)
    {
        $cmsCategoryExtra = $this->findBy((['cmsCategoryId' => $cmsCategoryId]));
        $em = $this->getEntityManager();

        if ($cmsCategoryExtra) {
            foreach ($cmsCategoryExtra as $cmsCategoryExtraLang) {
                $em->remove($cmsCategoryExtraLang);
            }
            $em->flush();
        }
    }
}