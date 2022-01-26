<?php
namespace PlugHacker\PlugPagamentos\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class UpgradeSchema
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();
        $version = $context->getVersion();

        if (version_compare($version, "1.0.2", "<")) {
            $setup = $this->updateVersionOneZeroTwo($setup);
        }

        if (version_compare($version, "1.0.14", "<")) {
            $setup = $this->updateVersionOneZeroTwelve($setup);
        }

        $installSchema = new InstallSchema();
        if (version_compare($version, "1.3.0", "<")) {
            $setup = $installSchema->installWebhook($setup);
            $setup = $installSchema->installOrder($setup);
            $setup = $installSchema->installCharge($setup);
            $setup = $installSchema->installTransaction($setup);
        }

        if (version_compare($version, "1.4.0", "<")) {
            $setup = $installSchema->installConfig($setup);
            $setup = $this->fixTransactionTable($setup);
        }

        if (version_compare($version, "1.7.0", "<")) {
            $setup = $installSchema->installSavedCard($setup);
            $setup = $installSchema->installCustomer($setup);
            $setup = $this->addBoletoInfoToTransactionTable($setup);
        }

        if (version_compare($version, "1.7.2", "<")) {
            $setup = $this->addStoreIdToConfigurationTable($setup);
            $setup = $this->addCardOwnerNameToCardsTable($setup);
        }

        if (version_compare($version, "1.8.1", "<")) {
            $setup = $this->addCreatedAtToCardsTable($setup);
        }

        if (version_compare($version, "1.8.7", "<")) {
            $setup = $this->addMetadataToChargeTable($setup);
            $setup = $this->addCustomerIdToChargeTable($setup);
            $setup = $this->addCardDataToTransactionTable($setup);
        }

        if (version_compare($version, "2.0.2", "<")) {
            $setup = $this->addTransactionDataToTransactionTable($setup);
        }

        if (version_compare($version, "2.1.0", "<")) {
            $setup = $this->fixTableDataSize($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param $setup
     * @return mixed
     */
    protected function updateVersionOneZeroTwo($setup)
    {
        $installer = $setup;
        $installer->startSetup();

        $tableName = $installer->getTable('plug_cards');
        // Check if the table already exists
        if ($installer->getConnection()->isTableExists($tableName) != true) {
            $table = $installer->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    'id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ],
                    'ID'
                )
                ->addColumn(
                    'customer_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'default' => '0'],
                    'Customer Id'
                )
                ->addColumn(
                    'card_token',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    'Card Token'
                )
                ->addColumn(
                    'card_id',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    'Card Id'
                )
                ->addColumn(
                    'last_four_numbers',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    'Last Four Numbers'
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_DATETIME,
                    null,
                    ['nullable' => false],
                    'Created At'
                )
                ->addColumn(
                    'updated_at',
                    Table::TYPE_DATETIME,
                    null,
                    ['nullable' => false],
                    'Updated At'
                )
                ->setComment('Plug Card Tokens')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
        return $setup;
    }

    /**
     * @param $setup
     * @return mixed
     */
    protected function updateVersionOneZeroTwelve($setup)
    {
        $installer = $setup;
        $installer->startSetup();
        $connection = $installer->getConnection();
        $connection->addColumn(
            $installer->getTable('plug_cards'),
            'brand',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => false,
                'default' => '',
                'comment' => 'Card Brand'
            ]
        );

        $installer->endSetup();
        return $setup;
    }

    /**
     * @param $setup
     * @return mixed
     */
    protected function fixTransactionTable($setup)
    {
        $installer = $setup;
        $connection = $installer->getConnection();
        $connection->modifyColumn(
            $installer->getTable('plug_module_core_transaction'),
            'acquirer_tid',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 300,
            ]
        )
        ->modifyColumn(
            $installer->getTable('plug_module_core_transaction'),
            'acquirer_nsu',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 300,
            ]
        )
        ->modifyColumn(
            $installer->getTable('plug_module_core_transaction'),
            'acquirer_auth_code',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 300,
            ]
        );

        return $setup;
    }

    /**
     * @param $setup
     * @return mixed
     */
    protected function addBoletoInfoToTransactionTable($setup)
    {
        $installer = $setup;
        $connection = $installer->getConnection();
        $tableName = $installer->getTable('plug_module_core_transaction');
        $connection->addColumn(
            $tableName,
            'boleto_url',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 500,
                'nullable' => true,
                'comment' => 'Boleto url'
            ]
        );

        return $setup;
    }

    /**
     * @param $setup
     * @return mixed
     */
    protected function addStoreIdToConfigurationTable($setup)
    {
        $installer = $setup;
        $connection = $installer->getConnection();
        $tableName = $installer->getTable('plug_module_core_configuration');
        $connection->addColumn(
            $tableName,
            'store_id',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 50,
                'nullable' => true,
                'comment' => 'Store id'
            ]
        );

        return $setup;
    }

    /**
     * @param $setup
     * @return mixed
     */
    protected function addCardOwnerNameToCardsTable($setup)
    {
        $installer = $setup;
        $connection = $installer->getConnection();
        $tableName = $installer->getTable('plug_module_core_saved_card');
        $connection->addColumn(
            $tableName,
            'owner_name',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 50,
                'nullable' => true,
                'comment' => 'Card owner name'
            ]
        );

        return $setup;
    }

    /**
     * @param $setup
     * @return mixed
     */
    protected function addCreatedAtToCardsTable($setup)
    {
        $installer = $setup;
        $connection = $installer->getConnection();
        $tableName = $installer->getTable('plug_module_core_saved_card');
        $connection->addColumn(
            $tableName,
            'created_at',
            [
                'type' => Table::TYPE_DATETIME,
                'nullable' => false,
                'comment' => 'Card createdAt'
            ]
        );

        return $setup;
    }

    /**
     * @param $setup
     * @return mixed
     */
    protected function addMetadataToChargeTable($setup)
    {
        $installer = $setup;
        $connection = $installer->getConnection();
        $tableName = $installer->getTable('plug_module_core_charge');
        $connection->addColumn(
            $tableName,
            'metadata',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 500,
                'nullable' => true,
                'comment' => 'Charge metadata'
            ]
        );

        return $setup;
    }

    /**
     * @param $setup
     * @return mixed
     */
    protected function addCustomerIdToChargeTable($setup)
    {
        $installer = $setup;
        $connection = $installer->getConnection();
        $tableName = $installer->getTable('plug_module_core_charge');
        $connection->addColumn(
            $tableName,
            'customer_id',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 50,
                'nullable' => true,
                'comment' => 'Charge customer id'
            ]
        );

        return $setup;
    }

    /**
     * @param $setup
     * @return mixed
     */
    protected function addCardDataToTransactionTable($setup)
    {
        $installer = $setup;
        $connection = $installer->getConnection();
        $tableName = $installer->getTable('plug_module_core_transaction');
        $connection->addColumn(
            $tableName,
            'card_data',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 600,
                'nullable' => true,
                'comment' => 'Card data'
            ]
        );

        return $setup;
    }

    /**
     * @param $setup
     * @return mixed
     */
    protected function addTransactionDataToTransactionTable($setup)
    {
        $installer = $setup;
        $connection = $installer->getConnection();
        $tableName = $installer->getTable('plug_module_core_transaction');
        $connection->addColumn(
            $tableName,
            'transaction_data',
            [
                'type' => Table::TYPE_TEXT,
                null,
                'nullable' => true,
                'comment' => 'Transaction Data'
            ]
        );

        return $setup;
    }

    /**
     * @param $setup
     * @return mixed
     */
    protected function fixTableDataSize($setup)
    {
        $installer = $setup;

        $connection = $installer->getConnection();
        $tableName = $installer->getTable('plug_module_core_webhook');
        if ($installer->getConnection()->isTableExists($tableName)) {
            $connection->modifyColumn(
                $tableName,
                'plug_id',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 100
                ]
            );
        }

        $tableName = $installer->getTable('plug_module_core_order');
        if ($installer->getConnection()->isTableExists($tableName)) {
            $connection->modifyColumn(
                $tableName,
                'plug_id',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 100
                ]
            );
        }

        $tableName = $installer->getTable('plug_module_core_charge');
        if ($installer->getConnection()->isTableExists($tableName)) {
            $connection->modifyColumn(
                $tableName,
                'plug_id',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 100
                ]
            )->modifyColumn(
                $tableName,
                'order_id',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 100
                ]
            );
        }

        $tableName = $installer->getTable('plug_module_core_transaction');
        if ($installer->getConnection()->isTableExists($tableName)) {
            $connection->modifyColumn(
                $tableName,
                'plug_id',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 100
                ]
            )->modifyColumn(
                $tableName,
                'charge_id',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 100
                ]
            );
        }

        $tableName = $installer->getTable('plug_module_core_saved_card');
        if ($installer->getConnection()->isTableExists($tableName)) {
            $connection->modifyColumn(
                $tableName,
                'plug_id',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 100
                ]
            )->modifyColumn(
                $tableName,
                'owner_id',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 100
                ]
            );
        }

        $tableName = $installer->getTable('plug_module_core_customer');
        if ($installer->getConnection()->isTableExists($tableName)) {
            $connection->modifyColumn(
                $tableName,
                'plug_id',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 100
                ]
            );
        }

        return $setup;
    }
}
