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

	function domains_from_list($listUrl) 
	{
		/* Perform request for contents. */
		$curlHandle = curl_init(); 

		curl_setopt($curlHandle, CURLOPT_URL, $listUrl); 
		curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, true); 
		curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, true); 
		curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, true);  
		curl_setopt($curlHandle, CURLOPT_TIMEOUT, 15); 
		curl_setopt($curlHandle, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64; rv:30.0) Gecko/20100101 Firefox/30.0'); 

		$listContents = curl_exec($curlHandle); 
		
		curl_close($curlHandle);
		
		if ($listContents === FALSE) {
		    return FALSE;
		}  
		
		/* Process returned result */
		$final_result = array();

		/* Have to use regular expression for splitting into
		an array because of many different line endings. */
		$lines = preg_split("/(\r\n|\n|\r)/", $listContents);
		
		foreach ($lines as $line) {
			$line = trim($line);
			
			/* If string is empty or starts with # (comment), 
			then ignore this entry. */
			if (strlen($line) === 0 || strpos($line, '#') === 0) {
				continue;
			}
			
			/* Attempt to parse dnsmasq address= lists */
			$isWildcardHost = FALSE;

			if (strpos($line, 'address=/') === 0) {
				$lineItems = explode('/', $line);
				
				if ($lineItems && count($lineItems) === 3) {
					$line = $lineItems[1];
					
					goto validate_address;
				}
			}

			/* Attempt to split up line using combinations of
			tab and space characters. */
			$lineItems = preg_split("/([\s\t]+)/", $line);
						
			/* If our string split up and we have 2 entries, then
			the file was probably a hosts file. We validate the 
			first item to ensure that it was an IP address to 
			back this assumption. */
			if ($lineItems && count($lineItems) === 2) {
				if (filter_var($lineItems[0], FILTER_VALIDATE_IP)) {
					$line = $lineItems[1];
				}
			}
			
validate_address:
			/* Check wether the remainder is a host */
			$hostEntry = HostEntry::newEntry($line);
			
			if ($hostEntry !== NULL) {
				$final_result[] = $hostEntry;
			}
		}
		
		return $final_result;
	}
