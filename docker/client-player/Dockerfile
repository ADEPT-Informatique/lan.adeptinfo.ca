FROM node:alpine3.11

RUN apk add --no-cache bash

ADD wait-for-it /opt/wait-for-it

RUN ["chmod", "+x", "/opt/wait-for-it/wait-for-it.sh"]
