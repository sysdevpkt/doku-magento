<?php
namespace Doku\MerchantHosted\Setup;

use \Magento\Framework\Setup\SchemaSetupInterface;
use \Magento\Framework\Db\Ddl\Table;
use \Magento\Framework\Setup\ModuleContextInterface;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface{

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
		$installer->endSetup();

	}

}