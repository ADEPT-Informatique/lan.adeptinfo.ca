###############
##  Angular  ##
###############

.PHONY: docker-run-clients
docker-run-clients: install-node-first ng-build-core install-node-second

install-node-first: clients/projects
	docker run --rm -t -u $$(id -u):$$(id -g) \
		-v $$(pwd)/clients:/opt/lanadept/clients \
		-w /opt/lanadept/clients node:10 \
		npm install --ignore-scripts

ng-build-core: clients/projects
	docker run --rm -t -u $$(id -u):$$(id -g) \
		-v $$(pwd)/clients:/opt/lanadept/clients \
		-w /opt/lanadept/clients \
		node:10 \
		bash -c "/opt/lanadept/clients/node_modules/@angular/cli/bin/ng build core"

install-node-second: clients
	docker run --rm -t -u $$(id -u):$$(id -g) \
		-v $$(pwd)/clients:/opt/lanadept/clients \
		-w /opt/lanadept/clients/projects/admin node:10 \
		npm install

###########
## Lumen ##
###########

.PHONY: docker-composer-install
install-composer/vendor: api/composer.json
	docker run --rm -i --tty \
	  -u $$(id -u):$$(id -g) \
      -v $$(pwd)/api:/app \
      composer install --prefer-source --no-interaction -o

.PHONY: docker-composer-update
update-composer/vendor: api/composer.json
	docker run --rm -i --tty \
	  -u $$(id -u):$$(id -g) \
      -v $$(pwd)/api:/app \
      composer update --prefer-source --no-interaction -o

####################
## Docker targets ##
####################

# Définition du port à utiliser pour l'API.
ifeq ($(LANADEPT_API_PORT),)
export LANADEPT_API_PORT = 8000
endif

# UID for mapping permissions to disk and running commands.
ifeq ($(LANADEPT_UID),)
export LANADEPT_UID = $(shell id -u)
endif

docker_compose_dev_files_args = -f docker-compose.yml -f docker-compose.dev.yml -f docker-compose.db.yml

.PHONY: prepare-docker-build
prepare-docker-build:
	cp -r tools/wait-for-it docker/nginx
	cp -r tools/wait-for-it docker/lumen
	cp -r tools/wait-for-it docker/client-admin

.PHONY: docker-build-dev
docker-build-dev: docker-kill-all prepare-docker-build install-composer/vendor docker-run-clients
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