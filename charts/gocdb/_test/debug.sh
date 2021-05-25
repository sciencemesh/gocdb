#!/usr/bin/env bash
helm install --dry-run --debug -f values.yaml --set database.data=";" gocdb ..
