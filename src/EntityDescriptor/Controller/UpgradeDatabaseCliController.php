<?php

namespace Orpheus\EntityDescriptor\Controller;

use Exception;
use Orpheus\EntityDescriptor\EntityDescriptor;
use Orpheus\EntityDescriptor\PermanentEntity;
use Orpheus\EntityDescriptor\SqlGenerator\SqlGeneratorMySql;
use Orpheus\InputController\CliController\CliController;
use Orpheus\InputController\CliController\CLIRequest;
use Orpheus\InputController\CliController\CLIResponse;
use Orpheus\SqlAdapter\SqlAdapter;

/**
 * Class UpgradeDatabaseCliController
 * Controller to upgrade database using cli
 *
 */
class UpgradeDatabaseCliController extends CliController {
	
	/**
	 * @param CLIRequest $request The input CLI request
	 * @return CLIResponse
	 * @throws Exception
	 */
	public function run($request): CLIResponse {
		
		$generator = new SqlGeneratorMySql();
		
		$query = '';
		/** @var PermanentEntity $entityClass */
		foreach( PermanentEntity::listKnownEntities() as $entityClass ) {
			$entityDescriptor = EntityDescriptor::load($entityClass::getTable(), $entityClass);
			$entityQuery = strip_tags($generator->matchEntity($entityDescriptor, $entityClass::getSqlAdapter()));
			if( $entityQuery ) {
				$query .= ($query ? "\n\n" : '') . $entityQuery;
			}
		}
		
		if( !$query ) {
			return new CLIResponse(0, 'No changes');
		}
		
		$this->printLine(sprintf("Available changes:
%s\n", $query));
		
		$answer = $this->requestInputLine('Do you want to apply changes ? [Y/n] ', false);
		
		if( $answer && strtolower($answer) !== 'y' ) {
			return new CLIResponse(0, 'Aborting changes');
		}
		echo 'Applying changes... ';
		
		$defaultAdapter = SqlAdapter::getInstance();
		$defaultAdapter->query($query, PDOEXEC);
		
		$this->printLine('Done!');
		
		return new CLIResponse(0, 'All changes were applied.');
	}
	
	
}
