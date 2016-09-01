#!/bin/bash

sendemail -f AMM@agroknow.com -t mihalis.papakonstadinou@agroknow.com -u "Harvested $1" -m $3 -s smtp.agroknow.com:587 -xu amm.harvester@agroknow.com -xp OvDjYDgL3m -a $2

