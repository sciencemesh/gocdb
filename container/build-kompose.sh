#!/bin/bash
rm -rf ./k8s.gen
mkdir k8s.gen
kompose convert -f docker-compose.yaml --controller "deployment" --with-kompose-annotation="false" --out ./k8s.gen/ -v
