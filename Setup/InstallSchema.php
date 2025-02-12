<?php
namespace PlugHacker\PlugPagamentos\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $this->installConfig($setup);
        $this->installWebhook($setup);
        $this->installOrder($setup);
        $this->installCharge($setup);
        $this->installTransaction($setup);
        $this->installSavedCard($setup);
        $this->installCustomer($setup);

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $installer
     * @return SchemaSetupInterface
     */
    public function installConfig(SchemaSetupInterface $installer)
    {
        $tableName = $installer->getTable('plug_module_core_configuration');
        if (!$installer->getConnection()->isTableExists($tableName)) {
            try {
                $configTable = $installer->getConnection()
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
                        'data',
                        Table::TYPE_TEXT,
                        null,
                        [
                            'nullable' => false
                        ],
                        'data'
                    )
                    ->setComment('Configuration Table')
                    ->setOption('charset', 'utf8');

                $installer->getConnection()->createTable($configTable);
            } catch (\Zend_Db_Exception $e) {
            }

        }
        return $installer;
    }

    /**
     * @param SchemaSetupInterface $installer
     * @return SchemaSetupInterface
     */
    public function installWebhook(SchemaSetupInterface $installer)
    {
        $tableName = $installer->getTable('plug_module_core_webhook');
        if (!$installer->getConnection()->isTableExists($tableName)) {
            try {
                $webhookTable = $installer->getConnection()
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
                        'plug_id',
                        Table::TYPE_TEXT,
                        21,
                        [
                            'nullable' => false
                        ],
                        'format: hook_xxxxxxxxxxxxxxxx'
                    )
                    ->addColumn(
                        'handled_at',
                        Table::TYPE_TIMESTAMP,
                        null,
                        [
                            'nullable' => false,
                            'default' => Table::TIMESTAMP_INIT
                        ],
                        'When the webhook was handled.'
                    )
                    ->setComment('Webhook Table')
                    ->setOption('charset', 'utf8');

                $installer->getConnection()->createTable($webhookTable);
            } catch (\Zend_Db_Exception $e) {
            }
        }
        return $installer;
    }

    /**
     * @param SchemaSetupInterface $installer
     * @return SchemaSetupInterface
     */
    public function installOrder(SchemaSetupInterface $installer)
    {
        $tableName = $installer->getTable('plug_module_core_order');
        if (!$installer->getConnection()->isTableExists($tableName)) {
            try {
                $webhookTable = $installer->getConnection()
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
                        'plug_id',
                        Table::TYPE_TEXT,
                        19,
                        [
                            'nullable' => false
                        ],
                        'format: or_xxxxxxxxxxxxxxxx'
                    )
                    ->addColumn(
                        'code',
                        Table::TYPE_TEXT,
                        100,
                        [
                            'nullable' => false,
                        ],
                        'Code'
                    )
                    ->addColumn(
                        'status',
                        Table::TYPE_TEXT,
                        30,
                        [
                            'unsigned' => true,
                            'nullable' => false,
                        ],
                        'Status'
                    )
                    ->setComment('Order Table')
                    ->setOption('charset', 'utf8');
                $installer->getConnection()->createTable($webhookTable);
            } catch (\Zend_Db_Exception $e) {
            }
        }
        return $installer;
    }

    /**
     * @param SchemaSetupInterface $installer
     * @return SchemaSetupInterface
     */
    public function installCharge(SchemaSetupInterface $installer)
    {
        $tableName = $installer->getTable('plug_module_core_charge');
        if (!$installer->getConnection()->isTableExists($tableName)) {
            try {
                $webhookTable = $installer->getConnection()
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
                        'plug_id',
                        Table::TYPE_TEXT,
                        19,
                        [
                            'nullable' => false
                        ],
                        'format: ch_xxxxxxxxxxxxxxxx'
                    )
                    ->addColumn(
                        'order_id',
                        Table::TYPE_TEXT,
                        19,
                        [
                            'nullable' => false
                        ],
                        'format: or_xxxxxxxxxxxxxxxx'
                    )
                    ->addColumn(
                        'code',
                        Table::TYPE_TEXT,
                        100,
                        [
                            'nullable' => false,
                        ],
                        'Code'
                    )
                    ->addColumn(
                        'amount',
                        Table::TYPE_INTEGER,
                        null,
                        [
                            'unsigned' => true,
                            'nullable' => false,
                        ],
                        'amount'
                    )
                    ->addColumn(
                        'paid_amount',
                        Table::TYPE_INTEGER,
                        null,
                        [
                            'unsigned' => true,
                            'nullable' => false,
                        ],
                        'Paid Amount'
                    )
                    ->addColumn(
                        'canceled_amount',
                        Table::TYPE_INTEGER,
                        null,
                        [
                            'unsigned' => true,
                            'nullable' => false,
                        ],
                        'Canceled Amount'
                    )
                    ->addColumn(
                        'refunded_amount',
                        Table::TYPE_INTEGER,
                        null,
                        [
                            'unsigned' => true,
                            'nullable' => false,
                        ],
                        'Refunded Amount'
                    )
                    ->addColumn(
                        'status',
                        Table::TYPE_TEXT,
                        30,
                        [
                            'unsigned' => true,
                            'nullable' => false,
                        ],
                        'Status'
                    )
                    ->setComment('Charge Table')
                    ->setOption('charset', 'utf8');
                $installer->getConnection()->createTable($webhookTable);
            } catch (\Zend_Db_Exception $e) {
            }
        }
        return $installer;
    }

    /**
     * @param SchemaSetupInterface $installer
     * @return SchemaSetupInterface
     */
    public function installTransaction(SchemaSetupInterface $installer)
    {
        $tableName = $installer->getTable('plug_module_core_transaction');
        if (!$installer->getConnection()->isTableExists($tableName)) {
            try {
                $webhookTable = $installer->getConnection()
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
                        'plug_id',
                        Table::TYPE_TEXT,
                        21,
                        [
                            'nullable' => false
                        ],
                        'format: tran_xxxxxxxxxxxxxxxx'
                    )
                    ->addColumn(
                        'charge_id',
                        Table::TYPE_TEXT,
                        19,
                        [
                            'nullable' => false
                        ],
                        'format: ch_xxxxxxxxxxxxxxxx'
                    )
                    ->addColumn(
                        'amount',
                        Table::TYPE_INTEGER,
                        null,
                        [
                            'unsigned' => true,
                            'nullable' => false,
                        ],
                        'amount'
                    )
                    ->addColumn(
                        'paid_amount',
                        Table::TYPE_INTEGER,
                        null,
                        [
                            'unsigned' => true,
                            'nullable' => false,
                        ],
                        'paid amount'
                    )
                    ->addColumn(
                        'acquirer_tid',
                        Table::TYPE_TEXT,
                        300,
                        [
                            'nullable' => false,
                        ],
                        'acquirer tid'
                    )
                    ->addColumn(
                        'acquirer_nsu',
                        Table::TYPE_TEXT,
                        300,
                        [
                            'nullable' => false,
                        ],
                        'acquirer nsu'
                    )
                    ->addColumn(
                        'acquirer_auth_code',
                        Table::TYPE_TEXT,
                        300,
                        [
                            'nullable' => false,
                        ],
                        'acquirer auth code'
                    )
                    ->addColumn(
                        'acquirer_name',
                        Table::TYPE_TEXT,
                        300,
                        [
                            'nullable' => false,
                        ],
                        'Type'
                    )
                    ->addColumn(
                        'acquirer_message',
                        Table::TYPE_TEXT,
                        300,
                        [
                            'nullable' => false,
                        ],
                        'Type'
                    )
                    ->addColumn(
                        'type',
                        Table::TYPE_TEXT,
                        30,
                        [
                            'nullable' => false,
                        ],
                        'Type'
                    )
                    ->addColumn(
                        'status',
                        Table::TYPE_TEXT,
                        30,
                        [
                            'nullable' => false,
                        ],
                        'Status'
                    )
                    ->addColumn(
                        'created_at',
                        Table::TYPE_DATETIME,
                        null,
                        [

                            'nullable' => false,
                        ],
                        'Created At'
                    )
                    ->setComment('Transaction Table')
                    ->setOption('charset', 'utf8');
                $installer->getConnection()->createTable($webhookTable);
            } catch (\Zend_Db_Exception $e) {
            }
        }
        return $installer;
    }

    /**
     * @param SchemaSetupInterface $installer
     * @return SchemaSetupInterface
     */
    public function installSavedCard(SchemaSetupInterface $installer)
    {
        $tableName = $installer->getTable('plug_module_core_saved_card');
        if (!$installer->getConnection()->isTableExists($tableName)) {
            try {
                $savedCardTable = $installer->getConnection()
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
                        'plug_id',
                        Table::TYPE_TEXT,
                        21,
                        [
                            'nullable' => false
                        ],
                        'format: card_xxxxxxxxxxxxxxxx'
                    )
                    ->addColumn(
                        'owner_id',
                        Table::TYPE_TEXT,
                        21,
                        [
                            'nullable' => false
                        ],
                        'format: cus_xxxxxxxxxxxxxxxx'
                    )
                    ->addColumn(
                        'first_six_digits',
                        Table::TYPE_TEXT,
                        6,
                        [
                            'nullable' => false
                        ],
                        'card first six digits'
                    )
                    ->addColumn(
                        'last_four_digits',
                        Table::TYPE_TEXT,
                        4,
                        [
                            'nullable' => false
                        ],
                        'card last four digits'
                    )
                    ->addColumn(
                        'brand',
                        Table::TYPE_TEXT,
                        30,
                        [
                            'nullable' => false
                        ],
                        'card brand'
                    )
                    ->setComment('Saved Card Table')
                    ->setOption('charset', 'utf8');
                $installer->getConnection()->createTable($savedCardTable);
            } catch (\Zend_Db_Exception $e) {
            }
        }
        return $installer;
    }

    /**
     * @param SchemaSetupInterface $installer
     * @return SchemaSetupInterface
     */
    public function installCustomer(SchemaSetupInterface $installer)
    {
        $tableName = $installer->getTable('plug_module_core_customer');
        if (!$installer->getConnection()->isTableExists($tableName)) {
            try {
                $customer = $installer->getConnection()
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
                        'code',
                        Table::TYPE_TEXT,
                        100,
                        [
                            'nullable' => false
                        ],
                        'platform customer id'
                    )
                    ->addColumn(
                        'plug_id',
                        Table::TYPE_TEXT,
                        20,
                        [
                            'nullable' => false
                        ],
                        'format: cus_xxxxxxxxxxxxxxxx'
                    )
                    ->setComment('Customer Table')
                    ->setOption('charset', 'utf8');
                $installer->getConnection()->createTable($customer);
            } catch (\Zend_Db_Exception $e) {
            }
        }
        return $installer;
    }
}
