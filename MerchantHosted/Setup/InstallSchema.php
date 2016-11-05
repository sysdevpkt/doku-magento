<?php
namespace Doku\MerchantHosted\Setup;

use \Magento\Framework\Setup\SchemaSetupInterface;
use \Magento\Framework\Db\Ddl\Table;
use \Magento\Framework\Setup\ModuleContextInterface;
use \Magento\Framework\Setup\InstallSchemaInterface;

class InstallSchema implements InstallSchemaInterface{

	public function install(
		SchemaSetupInterface $setup,
		ModuleContextInterface $context
	){

		$installer = $setup;
		$installer->startSetup();

		$table = $installer->getConnection()
			->newTable($installer->getTable('doku_tokenization'))
			->addColumn(
				'id',
				Table::TYPE_INTEGER,
				null,
				['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
				'Id'
				)
			->addColumn(
				'customer_id',
				Table::TYPE_INTEGER,
				null,
				['nullable' => false],
				'Customer Id'
				)
			->addColumn(
				'card_no',
				Table::TYPE_TEXT,
				50,
				['nullable' => false],
				'Card Number'
			)
			->addColumn(
				'token',
				Table::TYPE_TEXT,
				255,
				['nullable' => false],
				'Token'
				)
			->setComment('Doku Tokenization Table');

		$installer->getConnection()->createTable($table);

		$table = $installer->getConnection()
			->newTable($installer->getTable('doku_orders'))
			->addColumn(
				'id',
				Table::TYPE_INTEGER,
				null,
				['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
				'Id'
			)
			->addColumn(
				'store_id',
				Table::TYPE_INTEGER,
				null,
				['nullable' => false],
				'Store Id'
			)
			->addColumn(
				'order_id',
				Table::TYPE_TEXT,
				10,
				['nullable' => false],
				'Order Id'
			)
			->addColumn(
				'invoice_no',
				Table::TYPE_TEXT,
				50,
				['nullable' => false],
				'Invoice Number'
			)
			->addColumn(
				'payment_channel_id',
				Table::TYPE_TEXT,
				2,
				['nullable' => false],
				'Payment Channel Id'
			)->addColumn(
				'paycode_no',
				Table::TYPE_TEXT,
				50,
				['nullable' => false],
				'Pay Code Number'
			)
			->setComment('Doku Orders Table');

		$installer->getConnection()->createTable($table);
		$installer->endSetup();

	}

}