#!/bin/bash

echo "Expecting: SET OAITARGET FROM UNTIL CC as params"

SET=$1
FORMAT="oai_dc"
OAITARGET=$2
OAIFORMAT="oai_dc"

CC=$5

ROOT=/home/agris
HARVESTER=./harvestOnDates.jar
JAVA=/usr/bin/java
OUTPUTDIR=/home/agris/pm/automatedOAI/data/${CC}/${SET}

mkdir ${OUTPUTDIR}
mkdir ${OUTPUTDIR}/log

FROM=$3
UNTIL=$4

${JAVA} -jar ${HARVESTER} ${OAITARGET} ${OUTPUTDIR}/ ${OAIFORMAT}  ${UNTIL} ${FROM} ${SET} > ./output.log.txt

echo ${SET}" done"
