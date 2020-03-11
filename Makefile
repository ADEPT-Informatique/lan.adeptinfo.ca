###############
##  Angular  ##
###############

#angular/node_modules: angular/package.json
#	docker run --rm -t -u $$(id -u):$$(id -g) \
#		-v $$(pwd)/angular:/opt/angular \
#		-w /opt/angular node:9 \
#		yarn install





###########
## Lumen ##
###########

.PHONY: docker-composer
composer/vendor: api/composer.json
	docker run --rm -i --tty \
	  -u $$(id -u):$$(id -g) \
      -v $$(pwd)/api:/app \
      composer install

####################
## Docker targets ##
####################

# Définition du port à utiliser pour l'API.
ifeq ($(LANDEPT_WEBSITE_PORT),)
export LANADEPT_WEBSITE_PORT = 8000
endif

docker_compose_dev_files_args = -f docker-compose.yml -f docker-compose.dev.yml -f docker-compose.db.yml

.PHONY: prepare-docker-build
prepare-docker-build:
	cp -r tools/wait-for-it docker/nginx
	cp -r tools/wait-for-it docker/lumen

.PHONY: docker-build-dev
docker-build-dev: docker-kill-all prepare-docker-build composer/vendor
	docker-compose $(docker_compose_dev_files_args) build

.PHONY: docker-kill-all
docker-kill-all:
	docker-compose $(docker_compose_dev_files_args) kill

.PHONY: docker-run-dev
docker-run-dev: docker-kill-all docker-build-dev
	docker-compose $(docker_compose_dev_files_args) up ${DOCKER_COMPOSE_ARGS}