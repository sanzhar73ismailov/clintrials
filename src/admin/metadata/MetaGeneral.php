<?php
declare ( strict_types = 1 );
namespace clintrials\admin\metadata;

class MetaGeneral {
	protected $name;
	protected $comment;
	protected $ddl;
	/**
	 * @return the $name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return the $comment
	 */
	public function getComment() {
		return $this->comment;
	}

	/**
	 * @return the $ddl
	 */
	public function getDdl() {
		return $this->ddl;
	}

	/**
	 * @param field_type $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @param field_type $comment
	 */
	public function setComment($comment) {
		$this->comment = $comment;
	}

	/**
	 * @param field_type $ddl
	 */
	public function setDdl($ddl) {
		$this->ddl = $ddl;
	}

	
	
}