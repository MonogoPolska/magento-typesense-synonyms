<?php

declare(strict_types=1);

namespace Monogo\TypesenseSynonyms\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;

class MigrateExternalIdToName implements DataPatchInterface, PatchVersionInterface
{
    private ModuleDataSetupInterface $moduleDataSetup;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * @inheritDoc
     */
    public function apply(): void
    {
        $this->moduleDataSetup->startSetup();

        $connection = $this->moduleDataSetup->getConnection();
        $tableName = $this->moduleDataSetup->getTable('typesense_synonym');

        if (!$connection->isTableExists($tableName)) {
            $this->moduleDataSetup->endSetup();
            return;
        }

        $columns = $connection->describeTable($tableName);
        if (!isset($columns['name'])) {
            $this->moduleDataSetup->endSetup();
            return;
        }

        $rows = $connection->select()->from(
            $tableName,
            ['id', 'external_id', 'name', 'assigned_collection']
        )->where(
            'name IS NULL OR name = ""'
        )->where(
            'external_id IS NOT NULL AND external_id != ""'
        );

        $items = $connection->fetchAll($rows);

        if (empty($items)) {
            $this->moduleDataSetup->endSetup();
            return;
        }

        $timestamp = time();

        foreach ($items as $item) {
            $newExternalId = md5(
                $item['assigned_collection'] .
                $item['id'] .
                $timestamp
            );

            $connection->update(
                $tableName,
                [
                    'name' => $item['external_id'],
                    'external_id' => $newExternalId,
                ],
                ['id = ?' => $item['id']]
            );
        }

        $this->moduleDataSetup->endSetup();
    }

    /**
     * @inheritDoc
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public static function getVersion(): string
    {
        return '1.0.3';
    }
}
