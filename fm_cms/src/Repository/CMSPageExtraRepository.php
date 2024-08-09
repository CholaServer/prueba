<?php

namespace Flormoments\Module\Cms\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use Flormoments\Module\Cms\Dto\UpsertCMSPageExtraDto;
use Flormoments\Module\Cms\Entity\CMSPageExtra;

class CMSPageExtraRepository extends EntityRepository
{
    public function upsert(UpsertCMSPageExtraDto $upsertCMSPageExtraDto)
    {
        /** @var CMSPageExtra $cmsPageExtra */
        $cmsPageExtra = $this->findOneBy([
            'cmsId' => $upsertCMSPageExtraDto->getCmsId(),
            'langId' => $upsertCMSPageExtraDto->getLangId()
        ]);

        if (!$cmsPageExtra) {
            $cmsPageExtra = CMSPageExtra::create($upsertCMSPageExtraDto);
        } else {
            $cmsPageExtra->upsert($upsertCMSPageExtraDto);
        }

        $em = $this->getEntityManager();
        $em->persist($cmsPageExtra);
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
            ->select('c.id_cms, c.link_text')
            ->from(_DB_PREFIX_ . 'cms_page_extra', 'c')
            ->where('c.id_lang = :langId AND c.id_cms IN (:ids)')
            ->setParameter('langId', $langId)
            ->setParameter('ids', $ids, Connection::PARAM_INT_ARRAY);

        return $qb->execute()->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

    public function deleteByCmsId($cmsId)
    {
        $cmsPageExtra = $this->findBy((['cmsId' => $cmsId]));
        if ($cmsPageExtra) {
            $em = $this->getEntityManager();
            foreach ($cmsPageExtra as $cmsPageExtraLang) {
                $em->remove($cmsPageExtraLang);
            }
            $em->flush();
        }
    }

}