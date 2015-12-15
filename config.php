<?php

	$config = array(
		/* Begin Configuration */

		/* Define the write locations. 
			
		The key of the dictionary defines the format in which the 
		output will be written. The current possible values are 
		"dnsmasq" for address= format for the dnsmasq utility, 
		or "hosts" for a conventional hosts file format. */
		'write_locations' =>

		array(
			'dnsmasq' 	=> 	'/etc/dnsmasq.d/adblock.conf', 
			'hosts' 	=> 	'/var/www/html/hosts.txt'
		),
		
		/* Define the destination that is returned for each entry */
		'destination_ip_address' => '0.0.0.0',
		
		/* List of hosts files to aggregate from. The format of these
		hosts files must be a conventional hosts file or a list of 
		plain text hosts with nothing more. Each host in these hosts
		files are checked if its a valid domain name (by syntax alone),
		which means wildcards or other strange entries are ignored. */
		'hosts_list' =>
 
		array(
			'http://adblock.gjtech.net/?format=unix-hosts',
			'http://hosts-file.net/ad_servers.txt',
			'http://pgl.yoyo.org/adservers/serverlist.php?showintro=0&mimetype=plaintext',
			'http://winhelp2002.mvps.org/hosts.txt',
			'http://www.malwaredomainlist.com/hostslist/hosts.txt',
			'https://adaway.org/hosts.txt'
		),
		
		/* List of hosts to exclude from output */
		'excluded_hosts' =>

		array(
			'broadcasthost',
			'local',
			'localhost',
			'localhost.localdomain',
		),
		
		/* Do not include hosts in file that no longer resolve to an address */
		/* A host is removed if there is no A, AAAA, or CNAME record. */
		/* Use of this configuration option requires the 'dig' utility to 
		be installed as its used to perform the DNS lookup. */
		/* This feature exists more-so as a proof of concept and typically 
		should not be enabled. */
		'exclude_unresolved_hosts' => FALSE
		
		
		
		
		/* End Configuration */
	);
