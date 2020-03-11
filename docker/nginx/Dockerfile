FROM nginx:1.16-alpine

RUN apk add --no-cache bash

ADD wait-for-it /opt/wait-for-it

RUN mkdir /opt/lanadept

CMD /opt/wait-for-it/wait-for-it.sh --timeout=0 lumen:8000 && \
    /usr/sbin/nginx -g "daemon off;"