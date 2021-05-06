#!/usr/bin/env bash
helm install --dry-run --debug -f values-test.yaml gocdb .
