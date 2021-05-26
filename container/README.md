# GOCDB Docker Container
Docker files to provide a containerized version of GOCDB (https://wiki.egi.eu/wiki/GOCDB/Documentation_Index) for the **CS3MESH4EOSC** project.

The `webserver` directory contains the sources of the actual GOCDB; the `database` directory contains SQL files used for initial deployment of the GOCDB database.

## Running the containers
Use the Helm templates provided in the `charts` directory to deploy the GOCDB in your Kubernetes cluster.

## Usage
GOCDB offers a comfortable web frontend to manage the topology of a mesh; it also offers various REST API endpoints to query and modify the topology data.

- The GOCDB frontend can be reached at: [/gocdb](http://localhost/gocdb)
- The public API can be reached at: [/gocdbpi/public](http://localhost/gocdbpi/public)
- The private API can be reached at: [/gocdbpi/private](http://localhost/gocdbpi/private)

For more details about GOCDB, visit the official documentation [here](https://wiki.egi.eu/wiki/GOCDB/Documentation_Index).

## Notes
To make setting up and working with the GOCDB easy, user authentication was removed. This renders some features unusable, like applying user roles.

## Contact
The provided container is for testing purposes only. It is neither efficient nor secure. Not every detail of GOCDB was tested. If you encounter any problems, feel free to contact me at [daniel.mueller@uni-muenster.de](mailto:daniel.mueller@uni-muenster.de).
