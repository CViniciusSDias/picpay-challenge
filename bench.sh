#!/usr/bin/env sh

sleep 5

echo
echo "FPM"
wrk -t16 -c100 -d5s --latency http://web:8080/users

sleep 30

echo
echo "Swoole"
wrk -t16 -c100 -d5s --latency http://web/users
