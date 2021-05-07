#!/usr/bin/env bash
helm install -f testdata/values-test.yaml --set-file database.initialData=testdata/gocdb-data.sql gocdb .
