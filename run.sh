#!/bin/bash

php call.php

while IFS='' read -r line || [[ -n "$line" ]]; do

	./harvest.sh ${line}

	message="Hello"
	./sendemail.sh "$line" /home/agris/pm/automatedOAI/output.log.txt ${message}

done < "generated/commands"
