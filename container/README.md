# GOCDB Docker Container
Docker files to provide a containerized version of GOCDB (https://wiki.egi.eu/wiki/GOCDB/Documentation_Index) for the **CS3MESH4EOSC** project.

The `html` directory contains all modified HTML files of the GOCDB that differ from the original version. The `html.dev` directory contains the entire source code of the GOCDB and can be used for custom modifications. To extract all modified files, use the `isolate-modified-files.py` script; these are placed in `html.mod`. It is also possible to reapply all modified files located under `html` to `html.dev` using the `apply-modified-files.py` script.

Various configuration files can be found in `config`; these are mainly used to configure the web server of the GOCDB and usually don't need to be modified.

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
