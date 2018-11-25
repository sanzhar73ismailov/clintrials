<?php
declare ( strict_types = 1 );
namespace clintrials\admin\validation;

use clintrials\admin\metadata\Trigger;
use clintrials\admin\DdlExecutor;
use Logger;

class TriggerValidation {
		private $logger;
		private $trigger;

		public function __construct(DdlExecutor $ddlExecutor, Trigger $trigger) {
			$this->logger = Logger::getLogger(__CLASS__);
			$this->trigger = $trigger;
			$this->ddlExecutor = $ddlExecutor;
		}

	
		public function validate() : ValidationResult {
            $this->logger->trace("START");
			$validationResult = new ValidationResult ();
			$validationResult->passed = true;
			$trigger = $this->trigger;

		    $triggerName = $trigger->getName();
		    if (is_null($triggerName) or trim($triggerName)=="") {
		    	$validationResult->passed = false;
				$validationResult->errors [] = "trigger name is nuu or empty";
		    }
			$statement = $this->ddlExecutor->getTriggerStatementByName($triggerName);
			$this->logger->debug ( "Statement = " . $statement );
			$this->logger->debug ('ddl: ' .  $trigger->getDdl() );
			$this->logger->debug ( "Statement substr = " . $this->getBeginEndSubstr($statement) );
			$ddlTrigSubstr = $this->getBeginEndSubstr($trigger->getDdl());
			$this->logger->debug ( "Ddl substr = " . $ddlTrigSubstr );

			if($ddlTrigSubstr !== $statement) {
				$validationResult->passed = false;
				$validationResult->errors [] = sprintf("statements for trigger %s in XML and DB are not equal. In XML: <%s>, in DB: <%s>", $triggerName, $ddlTrigSubstr,$statement);
			}
			$this->logger->trace("FINISH return: " . var_export($validationResult, true));
			return $validationResult;
		}

		private function getBeginEndSubstr($str){
			$pos = (int) strpos($str, "BEGIN");
			return trim(substr($str, $pos));
		}

}