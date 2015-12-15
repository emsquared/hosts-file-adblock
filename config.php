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
		
	/* Define the write locations. 
		
	The key of the dictionary defines the format in which the 
	output will be written. The current possible values are 
	"dnsmasq" for address= format for the dnsmasq utility, 
	or "hosts" for a conventional hosts file format. */
	$write_locations = array(
		'dnsmasq' 	=> 	'/etc/dnsmasq.d/adblock.conf', 
		'hosts' 	=> 	'/var/www/html/hosts.txt'
	);
	
	/* Define the destination that is returned for each entry */
	$destination_ip_address = '0.0.0.0';
	
	/* List of hosts files to aggregate from. The format of these
	hosts files must be a conventional hosts file or a list of 
	plain text hosts with nothing more. Each host in these hosts
	files are checked if its a valid domain name (by syntax alone),
	which means wildcards or other strange entries are ignored. */
	$adblock_lists = array(
		'http://adblock.gjtech.net/?format=unix-hosts',
		'http://hosts-file.net/ad_servers.txt',
		'http://pgl.yoyo.org/adservers/serverlist.php?showintro=0&mimetype=plaintext',
		'http://winhelp2002.mvps.org/hosts.txt',
		'http://www.malwaredomainlist.com/hostslist/hosts.txt',
		'https://adaway.org/hosts.txt'
	);
	
	/* List of hosts to exclude from output */
	$excluded_domains = array(
		'broadcasthost',
		'local',
		'localhost',
		'localhost.localdomain',
	);
