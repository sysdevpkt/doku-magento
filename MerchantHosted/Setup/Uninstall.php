<?php
namespace Doku\MerchantHosted\Setup;

use Magento\Framework\Setup\SchemaSetupInterface;

class Uninstall implements UninstallInterface{

	public function uninstall(
		SchemaSetupInterface $setup
	){

		$installer = $setup;
		$installer->startSetup();
		$installer->getConnection()->dropTable($installer->getTable('doku_tokenization'));
		$installer->endSetup();

	}
	
}