# GOCDB Docker Container
Docker files to provide a containerized version of GOCDB (https://wiki.egi.eu/wiki/GOCDB/Documentation_Index) for the **CS3MESH4EOSC** project.

The `html` directory contains all modified HTML files of the GOCDB that differ from the original version.

Various configuration files can be found in `config`; these are mainly used to configure the web server of the GOCDB and usually don't need to be modified.

## Running the containers
Use the Helm templates provided in the `charts` directory to deploy the GOCDB in your Kubernetes cluster.

## Usage
GOCDB offers a comfortable web frontend to manage the topology of a mesh; it also offers various REST API endpoints to query and modify the topology data.

- The GOCDB frontend can be reached at: [/gocdb](http://localhost/gocdb)
- The public API can be reached at: [/gocdbpi/public](http://localhost/gocdbpi/public)
- The private API can be reached at: [/gocdbpi/private](http://localhost/gocdbpi/private)

For more details about GOCDB, visit the official documentation [here](https://wiki.egi.eu/wiki/GOCDB/Documentation_Index).

## Changes made
The following changes were made to the original GOCDB code:
- Set up `lib/Doctrine/bootstrap_doctrine.php`, allowing configuration via environment variables
- Add simple username/password authentication
- Allow login via a simple form (located under `/gocdb/login/`)
- Fix bug in `lib/Gocdb_Services/Role.php` not updating the Role record ID: After calling `em->persist`, `em->flush` has to be called to set generated values properly
    - Also apply the same fix to a bunch of other files, just in case...
- The GOCDBAuthProvider used a non-existing class; fix by passing the required reference in the constructor

## Notes
To make setting up and working with the GOCDB easy, user authentication was removed. This renders some features unusable, like applying user roles.

## Contact
The provided container is for testing purposes only. It is neither efficient nor secure. Not every detail of GOCDB was tested. If you encounter any problems, feel free to contact me at [daniel.mueller@uni-muenster.de](mailto:daniel.mueller@uni-muenster.de).
