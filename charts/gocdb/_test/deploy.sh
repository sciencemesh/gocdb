#!/usr/bin/env bash
helm delete gocdb
kubectl delete pvc data-gocdb-database-0
helm install -f values.yaml --set-file database.data=gocdb-data.sql gocdb ..
