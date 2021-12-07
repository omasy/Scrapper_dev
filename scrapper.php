<?php 
if (!class_exists('WebScrapper')) {
    /**
     * The public-facing functionality of the plugin
     * Class ERE_Public
     */
	 
	 include 'phpQuery/phpQuery/phpQuery.php'; // here we include the php query
	 
	 class WebScrapper{
		 /**
		  * The class WebScrapper Blocks
		  * Begins here
		  */
		  
		  public function scrap($url, $query = null){
			  // HERE WE START PROCESSING THE SCRAPPER FUNCTIONALITY
			  $contents = array();
			  $html = "";
			  // Here we start processing
			  if(filter_var($url, FILTER_VALIDATE_URL)){
				  $html = $this->crawler($url);
				  if(strlen($html) > 0){
					  if(strpos($html, "<html>") >= 0 && strpos($html, "<body>") >= 0){
						  $document = phpQuery::newDocumentHTML($html);
					  	  $title = $document->find('title');
					      // Now lets check the type of extraction we need
					      // by checking the query argument
					      if($query != null){
							  $content = $this->scrapByQuery($html, $query);
						  }
					      else{
							  $content = $this->scrapByNoQuery($html); 
					      }
					  
					      // NOW LETS CHECK CONTENT AND LOAD TO CONTENTS
					      if(!empty($content)){
							  $contents["url"] = $url;
						      $contents["title"] = $title;
							  $contents["query"] = $query;
						      $contents["content"] = $content;
					      }
				      }
			      }  
			  }
			  
			  // Here we return contents
			  return $contents;
			  // END OF METHOD
		   }
		  
		  
		  /**
           * promote
           */
		   
		   public function crawler($url){
			  $userAgent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)';
			  $timeout = 120;
			  $retry = 5;
			  $ch = curl_init();
			  // Now lets try processing
			  for($i=0; $i<$retry; $i++){ 
				  $options = array(
    				CURLOPT_URL            => $url,
    				CURLOPT_SSL_VERIFYHOST => 0,
					CURLOPT_SSL_VERIFYPEER => 0,
					CURLOPT_USERAGENT      => $userAgent,
    				CURLOPT_FOLLOWLOCATION => true,
    				CURLOPT_ENCODING       => "",
    				CURLOPT_AUTOREFERER    => true,
    				CURLOPT_CONNECTTIMEOUT => $timeout,
    				CURLOPT_TIMEOUT        => $timeout,
    				CURLOPT_MAXREDIRS      => 10,
					CURLOPT_RETURNTRANSFER => true,
				  );
				  curl_setopt_array( $ch, $options );
				  $data = curl_exec($ch);
				  // Here we return available option
				  if (curl_errno($ch)) {
					  $data = curl_error($ch); 
				  	  continue; // here we continue
			  	  }
				  else{
					  break; // we break for success
				  }
			  } // end of loop
			  
			  curl_close($ch);
			  // Here we return
			  return $data;
		  }
		  
		  
		  /**
           * promote
           */
		   
		   private function scrapByQuery($content, $query){
			   // HERE WE START PROCESSING THE SCRAP CONTENT BY QUERY
			   $wcontent = array();
			   $dataSet = array();
			   $container = "";
			   $results = null;
			   $index = 0;
			   // NOW LETS START PROCESSING
			   if(!empty($content) && !empty($query)){
				   $document = phpQuery::newDocumentHTML($content);
				   if(is_array($query)){
					   // Now lets get query by loop
					   foreach($query as $k=>$v){
						   if($k == "container"){
							   $container = $v;
							   break;
						   }
					   } // end of loop
					   
					   // HERE WE HAVE INSTRUCTION LETS KEEP PROCESSING
					   if(!empty($container)){
						   // Here we find container first
						   $results = $document->find($container);
						   if(!empty($result)){
							   foreach($results as $result){
								   foreach($query as $key=>$value){
									   // Here we process to get content
									   if($key != "container"){
										   if(strpos($value, "*") >= 0){
											   $qpart = explode("*", $value);
								   		       $selector = $qpart[0];
								               $fetch = $qpart[1];
								               if($fetch == "text"){
												   $point = pq($result, $document)->find($selector)->text();
								   		       }
								               else{
												   if(strpos($fetch, "~") >= 0){
													   $apart = explode("~", $fetch);
										               if(strpos($apart[0], "attr") >= 0){
														   $point = pq($result, $document)->find($selector)->attr($apart[1]); 
										               }   
									               }
								               }
								               // Now lets check point data
								               if(!empty($point)){
												   $dataSet[$key] = $point;
								               }
							               } 
									   } // end of check
						           } // end of loop
								   
								   // NOW LETS PROCESS FURHER CONSIDERING INSTRUCTION
						           if(!empty($dataSet)){
									   $wcontent[$index] = $dataSet;
									   $index++;
							       }
							   } // end of main loop
						   }
					   }
				   }
			   }
			   
			   // Here we return
			   return $wcontent;
		   }
		   
		   
		   /**
           * promote
           */
		   
		   private function scrapByNoQuery($content){
			   
		   }
		  
	 }
	 
	 
	 /************* HERE WE CREATE THE NO QUERY SCRAPPER CLASS ******************/
	 if(!class_exists('NoQueryScrapper')){
		 class NoQueryScrapper{	
				/**
           	    * promote
                */
				
				public function getLinks($instruction){
					
				}
				
				
				/**
           	    * promote
                */
		   	   
			    public function headCheck($instruction){
				   
			    }
			   
			   
			    /**
           	    * promote
                */
				
				public function navCheck($instruction){
					
				}
				
				
				/**
           	    * promote
                */
				
				public function hasDesc($instruction){
					
				}
		 }
	 } // end of no query class check
} // end of web scrapper class check
?>