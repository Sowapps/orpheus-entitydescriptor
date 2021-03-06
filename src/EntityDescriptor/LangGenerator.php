<?php
/**
 * LangGenerator
 */

namespace Orpheus\EntityDescriptor;

use Orpheus\Publisher\Exception\InvalidFieldException;

/**
 * The lang generator class is used to generate lang file from an entity descriptor
 * 
 * @author Florent Hazard <contact@sowapps.com>
 *
 */
class LangGenerator {
	
	/**
	 * Some values to test if field is valid
	 * 
	 * @var array
	 */
	public static $testedValues = array(null, '', '0', 'string', '1.997758887755445', '-974455277432344345647573654743352', '974455277432344345647573654743352');

	/**
	 * Get all exception string this entity could generate
	 * 
	 * @param	EntityDescriptor $ed
	 * @return	InvalidFieldException[] A set of exception
	 */
	public function getRows(EntityDescriptor $ed) {
		$r = array();
		foreach( $ed->getFieldsName() as $field ) {
			$r += $this->getErrorsForField($ed, $field);
		}
		return array_unique($r);
	}
	
	/**
	 * Get all exception this field could generate
	 * 
	 * @param	EntityDescriptor $ed
	 * @param	string $field
	 * @return	InvalidFieldException[]
	 */
	public function getErrorsForField(EntityDescriptor $ed, $field) {
		$r = array();
		foreach( static::$testedValues as $value ) {
			try {
				$ed->validateFieldValue($field, $value);
			} catch( InvalidFieldException $e ) {
				$r[$e->getKey()] = $e;
			}
		}
		return $r;
	}
	
}