#!/usr/bin/env bash
helm install -f values.yaml --set-file database.data=gocdb-data.sql gocdb ..
