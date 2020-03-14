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

.PHONY: docker-composer-install
install-composer/vendor: api/composer.json
	docker run --rm -i --tty \
	  -u $$(id -u):$$(id -g) \
      -v $$(pwd)/api:/app \
      composer install

.PHONY: docker-composer-update
update-composer/vendor: api/composer.json
	docker run --rm -i --tty \
	  -u $$(id -u):$$(id -g) \
      -v $$(pwd)/api:/app \
      composer update

####################
## Docker targets ##
####################

# Définition du port à utiliser pour l'API.
ifeq ($(LANADEPT_API_PORT),)
export LANADEPT_API_PORT = 8000
endif

docker_compose_dev_files_args = -f docker-compose.yml -f docker-compose.dev.yml -f docker-compose.db.yml

.PHONY: prepare-docker-build
prepare-docker-build:
	cp -r tools/wait-for-it docker/nginx
	cp -r tools/wait-for-it docker/lumen

.PHONY: docker-build-dev
docker-build-dev: docker-kill-all prepare-docker-build install-composer/vendor
	docker-compose $(docker_compose_dev_files_args) build

.PHONY: docker-kill-all
docker-kill-all:
	docker-compose $(docker_compose_dev_files_args) kill

.PHONY: docker-run-dev
docker-run-dev: docker-kill-all docker-build-dev
	docker-compose $(docker_compose_dev_files_args) up ${DOCKER_COMPOSE_ARGS}

.PHONY: docker-rm-all
docker-rm-all: docker-kill-all
	docker-compose $(docker_compose_dev_files_args) rm -v -f

.PHONY: clean-db
clean-db: docker-rm-all docker-kill-all
	docker volume rm -f lanadeptinfoca_db_data