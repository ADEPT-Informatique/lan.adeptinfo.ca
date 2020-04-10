FROM nginx:1.16-alpine

RUN apk add --no-cache bash openssl

ADD nginx.conf /etc/nginx/conf.d/default.conf

ADD wait-for-it /opt/wait-for-it

RUN ["chmod", "+x", "/opt/wait-for-it/wait-for-it.sh"]
