<?php
	class QueryProcessor{
		// SETTING PUBLIC VARIABLES
		private $Processed;
		private $Query;
		
		/******** HERE WE CONSTRUCT CLASS ***********/
		public function __construct($query){
			$this->Query = $query;
			$this->process();
		}
		
		/******* HERE WE CONSTRUCT THE PROCESS ******/
		private function process(){
			// HERE WE START CONSTRUCTING THE PROCESS
			$query = preg_replace('/\s+/', '', $this->Query);
			$processed = array();
			$trimmed = "";
			if(!empty($query)){
				if(strpos($query, "q{") >= 0 && strpos($query, "}") >= 0){
					$trimmed = substr($query, 2, (strlen($query) - 1));
					// Lets check if trimmed contains delimeter
					if(strpos($trimmed, ";") >= 0){
						$queryPaths = explode(";", $trimmed);
						// NOW LETS CHECK PARTS
						foreach($queryPaths as $qu){
							$qlower = strtolower($qu);
							if(strpos($qlower, "search") >= 0){
								$processed["search"] = $this->query($qlower, "search");
							}
							else if(strpos($qlower, "content") >= 0){
								$processed["content"] = $this->query($qlower, "content");
							}
						}
						
						// NOW LETS ASSIGN TO ENVIRONMENT
						$this->Processed = $processed;
					} // end delimeter check
				}
			}
		}
		
		/******* CONSTRUCTING THE GET METHOD ********/
		public function get($instruction){
			// HERE WE START PROCESSING THE GET
			$processed = $this->Processed;
			$returned = null;
			if(!empty($processed)){
				if(!empty($instruction)){
					if(strpos($instruction, ".") >= 0){
						$brkins = explode(".", $instruction);
						// Now lets get values
						if(array_key_exists($brkins[0], $processed)){
							if(array_key_exists($brkins[1], $processed[$brkins[0]])){
								$returned = $processed[$brkins[0]][$brkins[1]];
							}
						}
					}
					else{
						// Here only one instruction passed
						if(array_key_exists($instruction, $processed)){
							$returned = $processed[$instruction];
						}
					}
				}
			}
			
			// Here we return
			return $returned;
		}
		
		/****** HERE WE CONSTRUCT ALL GETS **********/
		private function query($q, $t){
			// HERE WE PROCESS THE GET SEARCH
			$repeatTrack = array();
			$queryRows = array();
			$trimmed = "";
			$rIndex = 2;
			if(!empty($q) && !empty($t)){
				if(strpos($q, "->") >= 0){
					$trimmed = str_replace($t."->", "", $q);
					if(strpos($trimmed, ",") >= 0){
						$splitDel = explode(",", $trimmed);
						foreach($splitDel as $qu){
							$rpart = explode(":", $qu);
							if(!in_array($repeatTrack)){
								// If item is not in array
								$queryRows[$rpart[0]] = $rpart[1];
								$repeatTrack[] = $rpart[0];
							}
							else{
								// If item is in array
								$queryRows[$rpart[0]."_".$rIndex] = $rpart[1];
								$rIndex++;
							}
						}
					}
				}
			}
			
			// Here we return
			return $queryRows;
		}
		
		
		/******* CONSTRUCTING THE NODE METHOD *******/
		// Node selector syntax: nodes_1 > title, nodes > desc
		public function nodes($selector = null){
			// HERE WE CONSTRUCT THE NODE SELECTOR ALGORITHM
			$returned = null;
			$nodes = array();
			// Lets get all queries
			if(!empty($selector)){
				if($selector != null && strpos($selector, ">") >= 0){
					$search = $this->get("search");
					$content = $this->get("content");
					$universal = array_merge($search, $content);
					if(!empty($universal)){
						foreach($universal as $key=>$value){
							if(strpos($key, "nodes") >= 0){
								$nodes[$key] = $value;	
							}
						} // end of loop
				
						// Lets check if node has value
						if(!empty($nodes)){
							$trim = preg_replace('/\s+/', '', $selector);
							$options = explode(">", $trim);
							foreach($nodes as $keys=>$values){
								if($options[0] == $keys){
									$nodePointer = $this->nodeProcessor($values);
									if(!empty($nodePointer)){
										$returned = $nodePointer[$options[1]];	
									}
									break;
								}
							}
						}
					}
				}
			}
			
			// Here we return
			return $returned;
		}
		
		
		/********* HERE WE CONSTRUCT THE NODE PROCESSOR *********/
		private function nodeProcessor($nodes){
			// HERE WE START CONSTRUCTING
			$pointer = array();
			if(!empty($nodes)){
				if(strpos($nodes, "(") >= 0 && strpos($nodes, ")") >= 0){
					if(strpos($nodes, "@") >= 0 && strpos($nodes, "-") >= 0){
						$sanitize = strpos($nodes, 1, (strlen($nodes) - 1));
						$splitNodes = explode("-", $nodes);
						foreach($splitNodes as $node){
							$parts = explode("@", $node);
							$pointer[$parts[0]] = $parts[1];
						} // end of loop
					}	
				}
			}
			
			// Here we return
			return $pointer;
		}
		
		// END OF CLASS
	}
?>