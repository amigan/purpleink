<?php
/*
 * PurpleInk - c.PurpleInk.php - main PurpleInk class
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


require_once 'c.DB.php';
require_once 'c.Page.php';
require_once 'c.Tree.php';

class Twig_Loader_PurpleInk implements Twig_LoaderInterface
{
	private $DB;

	public function __construct()
	{
		$this->DB = DBConnectorFactory::create();
	}

	public function getSource($name)
	{
		return $this->DB->getTplByName($name);
	}

	public function getCacheKey($name)
	{
		return $name;
	}

	public function isFresh($name, $time)
	{
		return false;
	}
}

class TwigFact
{
	static public $_inst;
	static public $_twl;

	static public function create()
	{
		if(!isset(self::$_inst)) {
			self::$_twl = new Twig_Loader_PurpleInk;
			self::$_inst = new Twig_Environment(self::$_twl, array(
				));
		}
		return self::$_inst;
	}

	static public function getTwl()
	{
		if(!isset(self::$_twl)) {
			self::create();
		}
		return self::$_twl;
	}
}


class PurpleInk
{
	protected $DB;
	protected $twLoad;
	protected $T;

	function __construct($site)
	{
		$this->DB = DBConnectorFactory::create();
		$this->DB->selectSite($site);
		$this->T = TwigFact::create();
		$this->twLoad = TwigFact::getTwl();
	}

	function rend($path)
	{
		$page = new Page($path, $this->T);
		$tr = TreeGrove::create();
		$tr->makeTree();
		$page->render();
	}
}

?>
