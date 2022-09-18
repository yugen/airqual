# airqual


## Future improvements
* Break client Dashboard into separate components.
* End2End tests.
* Allow user to select a specific station associated with their location.
* Use a map ui to show stations near location and allow selection.

## Installation & Deployment
Requirements:
* Docker
* Docker Compose
* node/npm 14 (TODO: build js assets w/ DOCKER)

```
$ cd frontend
$ npm install
$ npm run build
$ cd ../
$ docker-compose up --build -d
```
