# GOCDB
The GOCDB is used to store and maintain all mesh metadata of the **ScienceMesh** project. It offers a convenient web interface to manage all data of the mesh with ease.

## Install
To install the GOCDB, use the following command:
```
helm install gocdb .
```

## Uninstall
To remove the GOCDB, use the following command:
```
helm delete gocdb
```

## Configuration
The following configurations may be set:

| Setting | Description | Default |
| --- | --- | --- |
| `replicaCount` | How many replicas to run. | `1` |
| `image.repository` | Name of the image to run, without the tag. | `omnivox/gocdb` |
| `image.tag` | The image tag to use. | `latest` |
| `image.pullPolicy` | 	The kubernetes image pull policy. | `Always` |
| `url` | The main URL of the GOCDB instance (without the trailing /gocdb). | `""` |
| `defaultScope` | The default GOCDB scope. | `""` |
| `apiKey` | The API key for the GOCDB PI. | `(randomly generated)` |
| `service.type` | The kubernetes service type to use. | `ClusterIP` |
| `service.port` | The service port to use. | `8080` |
| `ingress.enabled` | Whether to create an ingress resource for the GOCDB. | `false` |
| `ingress.hostname` | The ingress hostname. | `""` |
| `ingress.path` | The ingress path. | `/` |
| `ingress.tls` | The ingress TLS configuration (YAML). | `[]` |
| `ingress.annotations` | Any additional ingress resource annotations. | `{}` |
| `env` | Dictionary of environment variables passed to the container in NAME:value form. | `{}` |

The GOCDB uses a MariaDB database to store its data. Its configuration can be accessed using the `database` scope. Refer to the official [MariaDB Helm chart documentation](https://github.com/bitnami/charts/tree/master/bitnami/mariadb) for a list of all available settings. Additionally, the following GOCDB specific settings are available:

| Setting | Description | Default |
| --- | --- | --- |
| `database.gocdbUser.name` | The username of the GOCDB database user. | `gocdbuser` |
| `database.gocdbUser.password` | The password of the GOCDB database user. | `gocdbpwd` |
| `database.data` | SQL data to use to deploy the GOCDB database. | `""` |

If you want to deploy existing data stored in a SQL file, you can use a command like this:
```
helm install --set-file database.data=gocdb-data.sql gocdb .
```

### Deployment naming
If you use a name other than `gocdb` for your deployment, you also need to modify the `database.initdbScriptsConfigMap` setting to match your deployment name:
```
...
database:
  initdbScriptsConfigMap: <deployment-name>-database-config
...
```

## Notes
Since the current version of the GOCDB is not final yet, the chart templates will likely change in the future. Also note that the GOCDB is in a _beta state_, which means that it is neither secure nor that all features are working correctly.
