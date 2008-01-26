<?php
/**
 * Detects location
 *
 * PHP versions 4/5
 *
 * @category   System
 * @package    Smartest
 */

 
/*
 * netgeo.php
 *
 * @(#) $Header: /home/mlemos/cvsroot/netgeo/netgeo.php,v 1.3 2003/12/12 16:48:18 mlemos Exp $
 *
 */

class NetGeo
{
	var $error="";
	var $timeout=0;
	var $http_user_agent="NetGeo IP locator service interface class - http://www.phpclasses.org/netgeoclass";

	Function GetAddressLocation($address,&$location,$query="getRecord")
	{
		$location=array();
		if(ereg('^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$',$address))
			$ip=$address;
		else
		{
			if(!strcmp($ip=@gethostbyname($address),$address))
			{
				$this->error="Could not resolve host name \"".$address."\".";
				return(0);
			}
		}
		if(!strcmp($ip,"127.0.0.1"))
		{
			$this->error="$ip is not a valid public Internet address!";
			return(0);
		}
		$domain="netgeo.caida.org";
		if(!($connection=($this->timeout ? @fsockopen($domain,80,$errno,$error,$this->timeout) : @fsockopen($domain,80))))
		{
			switch($error=($this->timeout ? strval($errno) : "??"))
			{
				case "-3":
					$this->error="-3 socket could not be created";
					break;
				case "-4":
					$this->error="-4 dns lookup on hostname \"".$host_name."\" failed";
					break;
				case "-5":
					$this->error="-5 connection refused or timed out";
					break;
				case "-6":
					$this->error="-6 fdopen() call failed";
					break;
				case "-7":
					$this->error="-7 setvbuf() call failed";
					break;
				default:
					$this->error=$error." could not connect to the host \"".$domain."\"";
					break;
			}
			return(0);
		}
		if($this->timeout
		&& (function_exists($function="stream_set_timeout")
		|| function_exists($function="socket_set_timeout")
		|| function_exists($function="set_socket_timeout")))
			$function($connection,$this->timeout);
		$uri="/perl/netgeo.cgi?method=".UrlEncode($query)."&target=".UrlEncode($ip);
		if(!fputs($connection,"GET $uri HTTP/1.0\r\n"
			."Host: $domain\r\n"
			."User-Agent: ".$this->http_user_agent."\r\n"
			."\r\n"))
		{
			$this->error="could not retrieve the NetGeo site request response";
			fclose($connection);
			return(0);
		}
		for($page=array();!feof($connection);)
		{
			if(GetType($data=fgets($connection,1000))!="string")
			{
				$this->error="could not retrieve the NetGeo site request response";
				fclose($connection);
				return(0);
			}
			$page[]=$data;
		}
		fclose($connection);
		for($line=0;$line<count($page);$line++)
		{
			$data=strtok($page[$line],"<\r\n");
			if(!strcmp(strtok($data,"="),"VERSION"))
				break;
		}
		if($line>=count($page))
		{
			$this->error="could not understand NetGeo service response";
			return(0);
		}
		for($line++;$line<count($page);$line++)
		{
			$attribute=strtok(strtok($page[$line],"<\r\n"),":");
			if(strcmp($attribute,""))
				$location[$attribute]=trim(strtok("<\r\n"));
		}
		return(1);
	}

	Function CalculateDistance($longitude_1,$latitude_1,$longitude_2,$latitude_2)
	{
		$long1=$longitude_1*M_PI/180;
		$lat1=$latitude_1*M_PI/180;
		$long2=$longitude_2*M_PI/180;
		$lat2=$latitude_2*M_PI/180;
		return(111*180/M_PI*acos(sin($lat1)*sin($lat2)+cos($lat1)*cos($lat2)*cos($long2-$long1)));
	}
};

?>