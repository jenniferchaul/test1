#Création de l'application via la commande "composer create-project symfony/website-skeleton lenomduprojet"

Création de mon dossier sur insomnia pour mes futurs test 


Limiter rate : https://symfony.com/doc/current/rate_limiter.html

Installation de limiter rate avec : `composer require symfony/rate-limiter`

création d'un fichier rate_limiter.yaml dans config/packages.

Inclure les paramétrage suivant :

`framework:
    rate_limiter:
        anonymous_api:
            policy: 'sliding_window'
            limit: 5
            interval: '1 minute'`