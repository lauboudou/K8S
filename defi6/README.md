Lier son terminal au docker de minikube
```
minikube docker-env
Pour Ubuntu : eval $(minikube -p minikube docker-env)
```

Réaliser les déploiements

```
cd nginx1
docker build . -t site1-nginx
docker images
kubectl apply -f  deployment.yaml
kubectl apply -f  service.yaml
```

cd ..

```
cd nginx2
docker build . -t site2-nginx
docker images
kubectl apply -f  deployment.yaml
kubectl apply -f  service.yaml
```

Vérifier que les pods ont été créés
```
kubectl get pod
```

Activer le ingress controller

```
minikube addons enable ingress
```

cd ..

```
kubectl apply -f  ingress.yaml
```


**In the context of Kubernetes Ingress resources, the term "backend" specifically refers to the destination service to which traffic is routed**

Ajouter au fichier host de l'adresse IP de minikube

**sur windows, l'@IP est localhost (127.0.0.1) pour les 2 pods (machines ou dockers deployés)
minikube ip


Modifier son fichier host (sur Ubuntu : /etc/hosts | sur Windows : C:\Windows\System32\Drivers\etc\hosts)
```
<IP_Minikube> <nom_host>
ouvrir le fichier hosts par code . dans une fenêtre shell 
#Ajouter ces lignes
127.0.0.1 site1.local
127.0.0.1 site2.local

```

Ouvrir dans le navigateur http://site1.local
Ouvrir dans le navigateur http://site2.local