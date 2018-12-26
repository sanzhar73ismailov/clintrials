<?
declare ( strict_types = 1 );
namespace clintrials\admin\validation;

use clintrials\admin\metadata\Table;
use clintrials\admin\metadata\Trigger;
use clintrials\admin\DdlExecutor;
use Logger;
use Exception;

class Validator {
	private $logger;
	private $ddlExecutor;


	
	public function __construct(DdlExecutor $ddlExecutor) {
			$this->ddlExecutor = $ddlExecutor;
			$this->logger = Logger::getLogger(__CLASS__);
	}

	public function validate($entity) : ValidationResult {
		if($entity instanceof Table){
			return $this->validateTable($entity);
		}elseif ($entity instanceof Trigger){
			return $this->validateTrigger($entity);
		} else {
			throw new Exception("enitity of class " . get_class($entity) . " are not supported");
		}
		
	}

	private function validateTable(Table $table) : ValidationResult{
		$this->logger->trace("START");
		$validationResult = new ValidationResult();
		if(!$this->ddlExecutor->tableExists ($table->getName())) {
	        $validationResult->objectExists = false;
	        $validationResult->passed = false;
	        $validationResult->errors [] = "Table " . $table->getName(). " is not exist"; 
	     } else {
	     	$validationResult->objectExists = true;
	        //$validationResult = $this->ddlExecutor->tableMatched ( $table );

           
			$tableMetaFromDb = $this->ddlExecutor->getTableMetaFromDb($table);

			$columnsNamesXml = array ();
			$columnsNamesDb = array ();
			
			$columnsNamesXml = $table->getFieldsName();
			$columnsNamesDb = $tableMetaFromDb->getFieldsName();
			
			if (count ( $columnsNamesXml ) != count ( $columnsNamesDb )) {
				$validationResult->errors [] = sprintf ( "Table '%s' - number of columns is different: in XML %s, in DB %s", 
						$table->getName(), count ( $columnsNamesXml ), count ( $columnsNamesDb ) );
			}
			$resultDiff = array_diff($columnsNamesXml, $columnsNamesDb);
			if(count($resultDiff)){
				$validationResult->errors [] = sprintf ( "Table '%s' - XML has columns <code>%s</code> that are not available in DB", $table->getName(), implode(",", $resultDiff));
			}
			$resultDiff = array_diff($columnsNamesDb, $columnsNamesXml);
			if(count($resultDiff)){
				$validationResult->errors [] = sprintf ( "Table '%s' - DB has columns <code>%s</code> that are not available in XML", $table->getName(), implode(",", $resultDiff));
			}
			$columnsNameTypeCommentsXml = $table->getFieldNameTypeComments();
			$columnsNameTypeCommentsDb = $tableMetaFromDb->getFieldNameTypeComments();

			$this->logger->debug("show columnsNameTypeCommentsXml for table " . $table->getName() );
			foreach ($columnsNameTypeCommentsXml as $key => $value) {
				$this->logger->trace("$key => $value");
			}
			$this->logger->debug("show columnsNameTypeCommentsXml end");
			$this->logger->debug("show columnsNameTypeCommentsDb for table " . $table->getName() );
			foreach ($columnsNameTypeCommentsDb as $key => $value) {
				$this->logger->trace("$key => $value");
			}
			$this->logger->debug("show columnsNameTypeCommentsDb end");
			$resultDiff = array_diff($columnsNameTypeCommentsXml, $columnsNameTypeCommentsDb);
			$this->logger->debug('resultDiff=' . var_export($resultDiff,true));
			if(count($resultDiff)){
				$validationResult->errors [] = sprintf ( "Table '%s' - XML has columns with types and comments <code>%s</code> that are not available in DB", $table->getName(), implode(",", $resultDiff));
			}
			$resultDiff = array_diff($columnsNameTypeCommentsDb, $columnsNameTypeCommentsXml);
			if(count($resultDiff)){
				$validationResult->errors [] = sprintf ( "Table '%s' - DB has columns with types and comments <code>%s</code> that are not available in XML", $table->getName(), implode(",", $resultDiff));
			}

			if(count($validationResult->errors) == 0){
				$this->logger->trace("1");
				for ($i=0; $i < count($columnsNameTypeCommentsXml); $i++) { 
					$this->logger->trace("2-" . $i);
					 $colInXml = $columnsNameTypeCommentsXml[$i];
					 $colInDb = $columnsNameTypeCommentsDb[$i];
					 $this->logger->trace(var_export($colInXml, true));
					 $this->logger->trace(var_export($colInDb, true));
					 $this->logger->trace(var_export($colInXml==$colInDb, true));
					 $this->logger->trace(var_export($colInXml===$colInDb, true));
					 if($colInXml !== $colInDb){
					 	$this->logger->trace("3");
					 	$validationResult->errors [] = sprintf ( "Table '%s' - the order of columns in XML is different than in DB. In XML on position '%s' is column '<code>%s</code>', in DB is '<code>%s</code>'", $table->getName(), $i+1, $colInXml, $colInDb);
					 	break;
					 }
				}
				$validationResult->passed = count($validationResult->errors) == 0;
				$this->logger->trace("4");
			}




	     }

		$this->logger->trace("FINISH return: " . var_export($validationResult, true));
		return $validationResult;

	}

	private function validateTrigger(Trigger $trigger) : ValidationResult {
		 $this->logger->trace("START");
			$validationResult = new ValidationResult ();

			$triggerName = $trigger->getName();

			if(!$this->ddlExecutor->triggerExists($trigger)) {
			   $validationResult->objectExists = false;
	           $validationResult->errors [] = "Trigger " . $trigger->getName(). " is not exist"; 
			} else {
				$validationResult->objectExists = true;
				$statement = $this->ddlExecutor->getTriggerStatementByName($triggerName);
				$this->logger->debug ( "Statement = " . $statement );
				$this->logger->debug ('ddl: ' .  $trigger->getDdl() );
				$this->logger->debug ( "Statement substr = " . $this->getBeginEndSubstr($statement) );
				$ddlTrigSubstr = $this->getBeginEndSubstr($trigger->getDdl());
				$this->logger->debug ( "Ddl substr = " . $ddlTrigSubstr );
				if($ddlTrigSubstr !== $statement) {
					$validationResult->errors [] = sprintf("statements for trigger %s in XML and DB are not equal. In XML: <%s>, in DB: <%s>", $triggerName, $ddlTrigSubstr,$statement);
				}
			}
			$validationResult->passed = count($validationResult->errors) == 0;
			$this->logger->trace("FINISH return: " . var_export($validationResult, true));
			return $validationResult;
	}

	private function getBeginEndSubstr($str){
			$pos = (int) strpos($str, "BEGIN");
			return trim(substr($str, $pos));
	}

    

	



}