<?php
		
	/* 
	 * This is free and unencumbered software released into the public domain.
	 * 
	 * Anyone is free to copy, modify, publish, use, compile, sell, or
	 * distribute this software, either in source code form or as a compiled
	 * binary, for any purpose, commercial or non-commercial, and by any
	 * means.
	 *
	 * In jurisdictions that recognize copyright laws, the author or authors
	 * of this software dedicate any and all copyright interest in the
	 * software to the public domain. We make this dedication for the benefit
	 * of the public at large and to the detriment of our heirs and
	 * successors. We intend this dedication to be an overt act of
	 * relinquishment in perpetuity of all present and future rights to this
	 * software under copyright law.
	 *
	 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
	 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
	 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
	 * IN NO EVENT SHALL THE AUTHORS BE LIABLE FOR ANY CLAIM, DAMAGES OR
	 * OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
	 * ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
	 * OTHER DEALINGS IN THE SOFTWARE.
	 *
	 * For more information, please refer to <http://unlicense.org/>
	 */
	 
	class HostEntry
	{
		public $host = NULL;

		public $isWildcard = FALSE;	
		
		public function newEntry($host) 
		{
			$isWildcard = FALSE;
			
			if (HostEntry::validateHost($host, $isWildcard) === TRUE) {
				$newEntry = new HostEntry($host, $isWildcard);
				
				return $newEntry;
			}
			
			return NULL;
		}
		
		public function __toString() 
		{
			return $this->host;	
		}

		private function __construct($host, $isWildcard = FALSE) {
			$this->host = $host;
			
			$this->isWildcard = $isWildcard;
		}
		
		private function validateHost($host, &$isWildcard)
		{
			global $config;

		 	/* dnsmasq allows wildcard entries by prefixing a host with a
			 period. This constructor recognizes that and appends something
			 in front of the host during validation so that it passes. */
			if (strpos($host, '.') === 0) {
				$host = "a{$host}";
				
				$isWildcard = TRUE;
			} else {
				$isWildcard = FALSE;
			}

			/* This is the most lazy solution yet to validate a 
			host, but who wants to deal with regular expression? */
			if (filter_var("http://{$host}/index.htnl?q=1", FILTER_VALIDATE_URL) === FALSE) {
				return FALSE;
			}

			/* Maybe exclude host */
			if ($isWildcard === FALSE && $config['exclude_unresolved_hosts']) {
				if (HostEntry::hostHasResolvableAddress($host, 'A') === FALSE &&
					HostEntry::hostHasResolvableAddress($host, 'AAAA') === FALSE &&
					HostEntry::hostHasResolvableAddress($host, 'CNAME') === FALSE) 
				{
					return FALSE;
				}
			}
			
			return TRUE;
		}
		
		private function hostHasResolvableAddress($host, $type = 'A')
		{
			if ($type !== 'A' &&
				$type !== 'AAAA' &&
				$type !== 'CNAME' && 
				$type !== 'MX')
			{
				return FALSE;
			}
	
	    	$hostEscaped = escapeshellcmd($host);
			
			ob_start(); 
	        
	        /* dig is used to allow us to easily configure the resolver and 
		    timeout which PHP does not allow unless you use some type of addon. */
	        passthru("dig +time=2 +tries=2 {$host} {$type} @8.8.8.8"); 
	        
	        $lookupResult = ob_get_contents(); 
	   
			ob_end_clean(); 
			
			return (strpos($lookupResult, "ANSWER SECTION:") > 0);
		}
	}
