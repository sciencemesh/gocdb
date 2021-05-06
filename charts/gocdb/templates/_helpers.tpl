{{/* vim: set filetype=mustache: */}}
{{/*
Expand the name of the chart.
*/}}
{{- define "gocdb.name" -}}
{{- default .Chart.Name .Values.nameOverride | trunc 63 | trimSuffix "-" }}
{{- end }}

{{/*
Create a default fully qualified app name.
We truncate at 63 chars because some Kubernetes name fields are limited to this (by the DNS naming spec).
If release name contains chart name it will be used as a full name.
*/}}
{{- define "gocdb.fullname" -}}
{{- if .Values.fullnameOverride }}
{{- .Values.fullnameOverride | trunc 63 | trimSuffix "-" }}
{{- else }}
{{- $name := default .Chart.Name .Values.nameOverride }}
{{- if contains $name .Release.Name }}
{{- .Release.Name | trunc 63 | trimSuffix "-" }}
{{- else }}
{{- printf "%s-%s" .Release.Name $name | trunc 63 | trimSuffix "-" }}
{{- end }}
{{- end }}
{{- end }}

{{/*
Create chart name and version as used by the chart label.
*/}}
{{- define "gocdb.chart" -}}
{{- printf "%s-%s" .Chart.Name .Chart.Version | replace "+" "_" | trunc 63 | trimSuffix "-" }}
{{- end }}

{{/*
Common labels
*/}}
{{- define "gocdb.labels" -}}
helm.sh/chart: {{ include "gocdb.chart" . }}
{{- if .Chart.AppVersion }}
app.kubernetes.io/version: {{ .Chart.AppVersion | quote }}
{{- end }}
app.kubernetes.io/managed-by: {{ .Release.Service }}
{{- end }}

{{- define "gocdb.databaseLabels" -}}
{{ include "gocdb.labels" . }}
{{ include "gocdb.databaseSelectorLabels" . }}
{{- end }}

{{- define "gocdb.webserverLabels" -}}
{{ include "gocdb.labels" . }}
{{ include "gocdb.webserverSelectorLabels" . }}
{{- end }}

{{/*
Selector labels
*/}}
{{- define "gocdb.selectorLabels" -}}
app.kubernetes.io/instance: {{ .Release.Name }}
{{- end }}

{{- define "gocdb.databaseSelectorLabels" -}}
{{- include "gocdb.selectorLabels" . }}
app.kubernetes.io/name: {{ include "gocdb.name" . }}-database
{{- end }}

{{- define "gocdb.webserverSelectorLabels" -}}
{{- include "gocdb.selectorLabels" . }}
app.kubernetes.io/name: {{ include "gocdb.name" . }}-webserver
{{- end }}

{{/*
Ingress API
*/}}
{{- define "gocdb.ingressAPIVersion" -}}
{{- if .Capabilities.APIVersions.Has "networking.k8s.io/v1" }}
{{- print "networking.k8s.io/v1" -}}
{{- else }}
{{- print "networking.k8s.io/v1beta1" -}}
{{- end }}
{{- end -}}

{{/*
Resource name helpers
*/}}
{{- define "gocdb.databasePVC" -}}
{{- if not .Values.database.persistentVolume.existingClaim }}
{{- include "gocdb.fullname" . }}-database-pvc
{{- else }}
{{- .Values.database.persistentVolume.existingClaim }}
{{- end}}
{{- end -}}
