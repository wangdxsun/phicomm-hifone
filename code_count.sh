#!/bin/bash
echo -en "total\t"
git log --numstat --no-merges --since=2018-04-27 --until=2018-05-30 | awk 'BEGIN{add=0;subs=0;loc=0} {if(($1~/^[0-9]+/) && ($1<5000)){add += $1; subs += $2; loc += ($1 - $2) }} END {printf "%s\t%s\t%s\n", add, subs, loc }'; 
git log --format='%aN' | sort -u | while read name; 
do echo -en "$name\t"; 
git log --numstat --author="$name" --no-merges --since=2018-04-27 --until=2018-05-30 | awk 'BEGIN{add=0;subs=0;loc=0} {if(($1~/^[0-9]+/) && ($1<5000)){add += $1; subs += $2; loc += ($1 - $2) }} END {printf "%s\t%s\t%s\n", add, subs, loc }'; 
done;
