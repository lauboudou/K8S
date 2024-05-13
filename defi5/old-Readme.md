# Symfony project

## Cloner le projet Symfony et modifier les variables d'environnements

```
git clone https://github.com/hmicn/k8s-base-app.git
```
Changer le fichier .env ligne 27 :
DATABASE_URL="mysql://root@mysql:3306/khuit?serverVersion=mariadb-10.3.31"

ou le dossier zippé

## Créer une image pour Symfony
```
FROM php:8.1.28-fpm

# Installation des dépendances
RUN apt-get update && apt-get install -y libpq-dev curl
RUN docker-php-ext-install pdo pdo_mysql

# Copier les fichiers de l'application
COPY k8s-base-app /var/www/html

# Définir le répertoire de travail
WORKDIR /var/www/html

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === 'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash
RUN apt install -y symfony-cli

# Exposer le port
EXPOSE 8000

CMD ["symfony", "server:start", "--no-tls"]
```
Construire l'image
```
docker build . -t symfony-app
```

## Créer un déploiement Symfony
Créer un fichier de déploiement pour symfony en exposant le port 8000

**symfony-deployment.yaml**
```
apiVersion: apps/v1
kind: Deployment
metadata:
  name: <nom>-deployment
spec:
  replicas: 1
  selector:
    matchLabels:
      app: <nom>
  template:
    metadata:
      labels:
        app: <nom>
    spec:
      containers:
      - name: <nom>
        image: symfony-app
        imagePullPolicy: Never
        ports:
        - containerPort: <port_container>

```

## Créer un service Symfony
Sur le port 30005

**symfony-service.yaml**
```
apiVersion: v1
kind: Service
metadata:
  name: <nom>-service
spec:
  type: NodePort
  ports:
    - port: <port_container>
      nodePort: <port_service>
  selector:
    app: <nom>
```

Appliquer les fichiers
```
kubectl apply -f symfony-deployment.yaml
kubectl apply -f symfony-service.yaml
```

## Lancer le service
```
minikube service <nom>-service
```

## Créer un volume MySQL
Cela permet de faire persister la donnée même si un container est arrêté.
```
apiVersion: v1
kind: PersistentVolumeClaim
metadata:
  name: mysql-pvc
spec:
  accessModes:
    - ReadWriteOnce
  resources:
    requests:
      storage: 1Gi
```

## Créer un déploiement MySQL

```
apiVersion: apps/v1
kind: Deployment
metadata:
  name: mysql-deployment
spec:
  replicas: 1
  selector:
    matchLabels:
      app: mysql
  template:
    metadata:
      labels:
        app: mysql
    spec:
      containers:
      - name: mysql
        image: mysql:5.7
        env:
         - name: MYSQL_ALLOW_EMPTY_PASSWORD
           value: "true"
         - name: MYSQL_DATABASE
           value: "khuit"
        ports:
        - containerPort: 3306
        volumeMounts:
        - name: mysql-persistent-storage
          mountPath: /var/lib/mysql
      volumes:
      - name: mysql-persistent-storage
        persistentVolumeClaim:
          claimName: mysql-pvc
```

## Créer un service MySQL

```
apiVersion: v1
kind: Service
metadata:
  name: mysql
spec:
  ports:
  - port: 3306
  selector:
    app: mysql
  clusterIP: None
```

Appliquer les fichiers (peut mettre jusqu'à 3 minutes)
```
kubectl apply -f mysql-pvc.yaml
kubectl apply -f mysql-deployment.yaml
kubectl apply -f mysql-service.yaml
kubectl get pods
```

## Créer un job pour la migration

**symfony-migration-job.yaml**
```
apiVersion: batch/v1
kind: Job
metadata:
  name: symfony-migration-job
spec:
  template:
    spec:
      containers:
      - name: symfony-migration-container
        image: symfony-app
        imagePullPolicy : Never
        command: ["/bin/sh"]
        args: ["-c", "php bin/console doctrine:migrations:diff -n --allow-empty-diff && php bin/console doctrine:migrations:migrate -n --allow-no-migration"]
        env:
        - name: DATABASE_URL
          value: "mysql://root@mysql/khuit"
      restartPolicy: Never
  backoffLimit: 4
```

Appliquer la migration
```
kubectl apply -f symfony-migration-job.yaml
```