# lanadept.com contribution guide

## First things first
Thanks! It's fairly appreciated that you give some of your time to contribute to this project. Be sure that if your contributions are well made and they follow this guide, you will definitely make a difference on the LAN experience of hundreds of plays. Have fun coding! :)

## General architecture
The lanadept.com project follows a REST architecture, which means authorised clients make requests to a separated backend resource which is called an API.

 The repository us separated in 3 different projects:
  - `/api` The API that handles all of the backend logic
  - `/client-user` The client that handles every user or player actions such as seat reservation, tournament registration, general information, etc...
  - `/client-admin` The client that handles every administrative tasks such as LAN dates, LAN places parameters, tournament options, general rules, etc...
## API
The API is built with a lighter version of [Laravel](https://github.com/laravel/laravel) called [Lumen](https://github.com/laravel/lumen), which is build especially for APIs.

I highly recommend reading on your own their documentation, you will find that it's quite accessible and built for users to read it.

There are a few architectural choices that have been made by the creator of the project that are not directly tied to Lumen but who are certainly made easy. If you want to propose any changes regarding the choices that have been made, feel free to contact the repository owner!

### Structure
 - **app**
   - **Services**: Interface that defines all of the logic for an action
     - **Implementation**: Implementations for the service interfaces. Those classes will be injected and used by a controller 
    - **Repositories**: Interface that defines every data access
      - **Implementation**: Implementations for the repository interfaces. Those classes will be injected and used by a service 

### Tests
It would also be pretty nice to include some tests for every new feature you develop. They are not required when you complete one, you can always pat yourself on the back...  

### Other libraries
We are also using a few libraries / external resources:

 - **[Passport](https://packagist.org/packages/dusterio/lumen-passport)**: Oauth2 package to make logins more secure and tokens easy to manage
 - **[Dingo](https://github.com/dingo/api)**: API ressource management. Currently handles our routes.
 - **[seats.io](https://github.com/seatsio/seatsio-php)**: A very usefull seat managing API that provides both backend and frontend libraries to show seats availability
 - **[Laravel Cors](https://github.com/barryvdh/laravel-cors)** : A library that handles CORS related headaches
