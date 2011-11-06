<?php
/*
 * PurpleInk - c.DB.php - database interface
 * 
 * Copyright (c) 2011, Dan Ponte
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are
 * met:
 * 
 * 	* Redistributions of source code must retain the above copyright notice,
 * 	  this list of conditions and the following disclaimer.
 * 
 * 	* Redistributions in binary form must reproduce the above copyright
 * 	  notice, this list of conditions and the following disclaimer in the
 * 	  documentation and/or other materials provided with the distribution.
 * 
 * 	* Neither the name of the author nor the names of its contributors
 * 	  may be used to endorse or promote products derived from this software
 * 	  without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS
 * IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED
 * TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A
 * PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED
 * TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

interface iDBConnector
{
	public function selectSite($siteID);
	public function getPageByPath($path);
	public function getTplByName($name);
	public function getPagesByParent($id);
}


class DBConnector implements iDBConnector
{
	private $m; /* MongoDB handle */
	private $master, $site; /* Databases */
	private $useMaster;
	private $siteSelected, $mast_db_name;
	private $cSettings, $cUsers, $cTemplates, $cVersions, $cPages; /* site collections */

	public function __construct()
	{
		try {
			$this->m = new Mongo;
		} catch (MongoConnectionException $e) {
			die('Error connecting to DB');
		}
		$this->mast_db_name = Config::get('mast_db_name');
		$mast_db_name = $this->mast_db_name;
		$this->master = $this->m->$mast_db_name;
		$this->siteSelected = false;
	}

	public function selectSite($siteID)
	{
		$site = $this->master->sites->findOne(array('_id' => $siteID));
		if(count($site) === 0) {
			throw new Exception('No such site');
		}
		$site = $site['dbname'];
		$site = $this->m->$site;
		$this->site = $site;
		$this->siteSelected = true;
		$this->cSettings = $this->site->settings;
		$this->cUsers = $this->site->users;
		$this->cTemplates = $this->site->templates;
		$this->cVersions = $this->site->versions;
		$this->cPages = $this->site->pages;
	}

	public function getTplByName($name)
	{
		$rt = $this->cTemplates->findOne(array('name' => $name));
		return $rt['body'];
	}

	public function getPageByPath($path)
	{
		if(!$this->siteSelected) {
			throw new Exception('No site selected');
		}

		return ar2ob($this->cPages->findOne(array('path' => $path)));
	}

	public function getPagesByParent($parent)
	{
		return $this->cPages->find(array('parent' => new MongoID($parent)));
	}
}

class DBConnectorFactory
{
	private static $_inst;

	public static function create()
	{
		if(!isset(self::$_inst)) {
			self::$_inst = new DBConnector;
		}

		return self::$_inst;
	}
}

?>
