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
	 
	require_once './config.php';

	require_once './includes/data-structure.php';
	require_once './includes/utility-functions.php';
	
	$domain_list = array();
	
	/* Loop through list of hosts file lists, downloading 
	their list of domains, comparing them to list of exempt 
	addresses, and adding to final list of addresses. */
	foreach ($adblock_lists as $list_url) 
	{
		echo "Downloading contents of hosts file at URL: '{$list_url}'...\n";

		$domains = domains_from_list($list_url);

		if (is_array($domains) === FALSE) {
			continue;
		}
		
		foreach ($domains as $domain) {
			if (in_array($domain, $excluded_domains)) {
				continue;	
			}	

			$domain_list[] = $domain;
		}
	}
	
	/* Remove duplicates from final list of domains */
	$domain_list = array_unique($domain_list);
	
	/* Sort list of domains */
	asort($domain_list);
	
	/* Format header of the resulting hosts file */
	echo "Writing hosts file(s)...\n";

	$domain_list_count = count($domain_list);
	$domain_list_count_formatted = number_format($domain_list_count);

	$domain_list_generation_date = gmdate('l, F d, Y - h:i:s A', time());
	
	$final_result_string_header = <<<EOT
#
# This hosts file is an aggregate of many hosts files.
#
# DO NOT rely on the existence of this hosts file.
# It is provided for the connivence of some, but it may cease to 
# exist at any time, without any warning.
#
# Warning: The hosts listed below have not been reviewed in any way.
#
# File generated: {$domain_list_generation_date}
# Total entry count: {$domain_list_count_formatted}
#
\n\n
EOT;

	foreach ($write_locations as $write_format => $write_location) {
		$final_result_string = $final_result_string_header;

		foreach ($domain_list as $domain) {
			if ($write_format === 'dnsmasq') {
				$final_result_string .= "address=/{$domain->host}/{$destination_ip_address}\n";
			} else {
				if ($domain->isWildcard === TRUE) {
					continue;
				}

				$final_result_string .= "{$destination_ip_address} {$domain->host}\n";
			}
		}
		
		if (file_put_contents($write_location, $final_result_string, LOCK_EX) === FALSE) {
			echo "Error: Failed to write contents to file: {$write_location} - Exiting!";

			exit(-1);
		}
	}
