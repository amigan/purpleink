<?php
/*
 * PurpleInk - c.AdminView.php - admin view
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


Class AdminView
{
	private $DB;
	private $tree;

	public function __construct()
	{
		$this->DB = DBConnectorFactory::create();
		$this->tree = TreeGrove::create();
		$this->tree->makeTree();
		
	}
	
	private function ulRec($t)
	{
		echo "<ul class='treetop'> p='" . $t->tree->path . "'\n";
		foreach($t->tree->children as $i) {
			echo "<li>\n";
			$this->ulRec($i);
			echo "</li>\n";
		}
		echo "</ul>\n";
	}

	public function outputTreeUL()
	{
		$this->ulRec($this->tree);
	}
}
