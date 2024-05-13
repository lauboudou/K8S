```
eval $(minikube -p minikube docker-env)

docker build . -t symfony-app

kubectl apply -f symfony-deployment.yaml
kubectl apply -f symfony-service.yaml

#Sur windows Exécuter ces commande pour changer les droits sur /var
kubectl get pods

#Recuperer le nom du déploiement <symfony-deployment*****>
kubectl exec -it symfony-deployment-7bf9cb647c-z66hg -- sh

#chmod sur le répertoire de travail /var
chmod -R 777 /var
exit

minikube service symfony-service

kubectl apply -f mysql-pvc.yaml
kubectl apply -f mysql-deployment.yaml
kubectl apply -f mysql-service.yaml

kubectl get pod
(Attendre 3 minute)

kubectl apply -f symfony-migration-job.yaml

Créer un utilisateur
```