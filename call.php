<?php

	//$xml=file_get_contents("http://akstem.com/export/collections/oai");

	$xml=file_get_contents("http://akstem.com/export/crm/collection/oai");

	$p = xml_parser_create();
	xml_parse_into_struct($p, $xml, $vals, $index);
	xml_parser_free($p);

	$fp=fopen("conf/setup.conf","w");

	$gp=fopen("generated/commands","w");

	for($i=0;$i<count($index['OAI-LINK']);$i++)
	{
		if(!isset($vals[$index['OAI-LINK'][$i]]['value']))
			continue;

		$vals[$index['CENTERCODE'][$i]]['value']=str_replace(" ","",$vals[$index['CENTERCODE'][$i]]['value']);
		$vals[$index['CENTERCODE'][$i]]['value']=str_replace("\n","",$vals[$index['CENTERCODE'][$i]]['value']);
		$vals[$index['CENTERCODE'][$i]]['value']=str_replace("\r","",$vals[$index['CENTERCODE'][$i]]['value']);

		$vals[$index['OAI-LINK'][$i]]['value']=str_replace(" ","",$vals[$index['OAI-LINK'][$i]]['value']);

		$oai=$vals[$index['OAI-LINK'][$i]]['value'];
		$dnid=$vals[$index['NID'][$i]]['value'];
		$countrycode=substr($vals[$index['CENTERCODE'][$i]]['value'],0,2);
		$centercode=substr($vals[$index['CENTERCODE'][$i]]['value'],2,1);

		//if($countrycode!="FR")
		//	continue;

		echo "|".$oai."|";

		$fromp=fopen("conf/".$countrycode.$centercode.".from","r");
		if ( !$fromp )
			$from="1000-01-01";
		else
			$from=fgetss($fromp,256);
		fclose($fromp);
		$until=date('Y-m',strtotime("-1months")).'-15';

		$newfrom=date('Y-m',strtotime("-1months")).'-16';
		$fromp=fopen("conf/".$countrycode.$centercode.".from","w");
		fwrite($fromp,$newfrom);
		fclose($fromp);

		//if($countrycode!='RU')
		//	continue;

		$oai=preg_replace('/\?(.*)/i','',$oai);

		$availablesets=file_get_contents($oai."?verb=ListSets");

		$p = xml_parser_create();
        	xml_parse_into_struct($p, $availablesets, $svals, $sindex);
        	xml_parser_free($p);

		//print_r($sindex);

		$setspecs=array();

		//echo "I am about to call:"."http://akstem.com/export/crm/collection/xml/".$countrycode."/".$centercode;

		$p = xml_parser_create();
                xml_parse_into_struct($p,
			file_get_contents("http://akstem.com/export/crm/collection/xml/".$countrycode."/".$centercode),
			$cvals, $cindex);
                xml_parser_free($p);

		for($j=0;$j<count($cindex['SETS']);$j++)
		{
			$sets=$cvals[$cindex['SETS'][$j]]['value'];
			$sets=str_replace("\n","",$sets);
			$sets=str_replace("\r","",$sets);
			$sets=preg_replace("/ \'/i",'',$sets);

			for($k=0;$k<count($sindex['SETNAME']);$k++)
			{
				if($sets==$svals[$sindex['SETNAME'][$k]]['value'])
				{
					$setspecs[$j]=$svals[$sindex['SETSPEC'][$k]]['value'];
					break;
				}
			}

			echo "|".$sets."|";
		}

		//print_r($setspecs);

		//$buffer=preg_replace('/\?(.*)/i','',$buffer);
		$buffer=$oai."\t".$dnid."\t".$countrycode."\t".$centercode."\n";
		$$buffer=preg_replace('/\?(.*)/i','',$buffer);
		echo $buffer;

		$command="";

		for($j=0;$j<count($setspecs);$j++)
		{
			if($setspecs[$j]=="" || $oai==""
				|| $from=="" || $until=="")
				continue;

			$command=$setspecs[$j]." ".$oai." ".$from." ".$until." ".$countrycode.$centercode."\n";
			fwrite($gp,$command);
		}

		fwrite($fp,$buffer);
	}
	fclose($fp);
	fclose($gp);
?>

