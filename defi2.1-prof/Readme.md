# Lier le repository docker minikube à son terminal
minikube -p minikube docker-env | Invoke-Expression

# Construire l'image
sudo docker build . -t html-bootstrap-nginx

# Créer le déploiement
kubectl apply -f html-bootstrap-deployment.yaml

# Créer le service
kubectl apply -f html-bootstrap-service.yaml

# Lancer le service dans mon navigateur
minikube service html-bootstrap-service