#!/bin/bash
# use ./driver1.sh <iterations> <url> <node> <apikey>
url=$1
node=$2
apikey=$3

d=$(date +%Y%m%d);
time=$(date +%H:%M:00)
newdate=$(echo $d | sed "s:\([0-9]\+\):2013:")
jstring=$(wget --user=cossmichg --password=microgrid -qO- "$url, 'date': '$d $time'}")
echo "wget --user=cossmichg --password=microgrid -qO- $url, 'date': '$d $time'}" 
echo $jstring

arg=$(echo $jstring | php -r '$msg=json_decode(file_get_contents("php://stdin"));if(isset($msg->powerOut)) $arg="powerOut:".$msg->powerOut; else $arg=""; if(isset($msg->powerin))  {if($arg!="")$arg=$arg.", "; $arg=$arg."powerIn: ".$msg->powerin;}echo $arg;')
echo "arg:"$arg
result=$(wget -qO- "http://localhost/emoncms/input/post.json?node=$node&json={$arg}&apikey=$apikey")
echo "http://localhost/emoncms/input/post.json?node=$node&json={$arg}&apikey=$apikey"

