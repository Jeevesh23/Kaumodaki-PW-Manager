# Kaumodaki PW Manager

## About
This is a project made by me(Mahendra) and Jeevesh, as an attempt to improve our skills in Web Technologies and PHP.  
I've always been interested in password managers(Bitwarden and KeepassXC) and the role they play in securing environments.  
Implementing it was challenging (that is also because I do not really understand security in depth YET).  
We DO NOT take any RESPONSIBILITY for our SHITTY coding practices and your LOSS of DATA.
Enjoy!:smirk:  

## Installation and Usage
1. Install [Docker](https://www.docker.com/get-started/) on your system.  
1. Git clone our repository.
    - Via HTTPS
    ```
    git clone https://github.com/SorcierMaheP/Kaumodaki-PW-Manager.git
    ```
    - via SSH
    ```
    git clone git@github.com:SorcierMaheP/Kaumodaki-PW-Manager.git
    ```  
1. Rename or copy the file `env.example` to `.env`. To fill in the `.env`, you'll need the following websites/info:-
    1. [Leak Lookup Search API](https://leak-lookup.com/)
    1. [OpenCage Geocoding API](https://opencagedata.com/)
    1. AES Key:- Any randomly generated 32 bit key should be set by the server admin.
    1. Email, Email App Password:- Use any email and its corresponding generated app password. This email will be used for SMTP.
    1. [Stripe](https://stripe.com/) Secret Key, Stripe Price ID:- Visit the website to create an item and the corresponding price ID.  
1. Run the following command in the root of the folder where the repository has been cloned:-
    ```
    docker compose up --build
    ```
    This will accordingly build the required containers in some time.  
1. The website can be accessed at `localhost:8000` and phpmyadmin interface at `localhost:8080`. For all the legal routes possible, see `routes.php` .  
1. Nice! You should be all set! Any further customizations can be done to `apache.Dockerfile` and `docker-compose.yaml`.  
