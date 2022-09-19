# airqual

## Future improvements
### Dev
* Extract api calls to a service module in frontend.
* Break client Dashboard into separate components.
* End2End tests with Cypress.
* Cache results from api.

### Features
* Currently, person can log in with either a google or github account, but those airqual accounts are not linked.  Provide a way for 
* Allow user to select a specific station associated with their location.
* Use a map ui to show stations near location and allow selection.
* Allow user to delete their account.

## Installation & Deployment
Requirements:
* Docker
* Docker Compose
* node/npm 14 (TODO: build js assets w/ DOCKER)
* Github and Google OAuth apps.
* An api token the air quality index (https://aqicn.org/api/)

```
$ cd backend
$ cp .env.example .env
```
You'll need to set the following environment variables in .env:
Github Oauth
* OAUTH_GITHUB_CLIENT_ID
* OAUTH_GITHUB_CLIENT_SECRET
* OUATH_GITHUB_REDIRECT

Google Oauth
* OAUTH_GOOGLE_CLIENT_ID
* OAUTH_GOOGLE_CLIENT_SECRET
* OUATH_GOOGLE_REDIRECT

AQI Api token
* AQICN_API_TOKEN

```
$ cd frontend
$ npm install
$ cd ../
$ docker-compose up --build -d
$ docker-compose exec backend php artisan migrate
```
