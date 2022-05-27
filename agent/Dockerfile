FROM python:3-alpine
RUN apk add --virtual .build-dependencies \
    --no-cache \
    python3-dev \
    build-base \
    linux-headers \
    pcre-dev 
RUN apk add --no-cache pcre
WORKDIR /agent
COPY . /agent
RUN apk update && apk add g++ gcc libxml2 libxslt-dev
RUN python -m pip install --upgrade pip
RUN pip install -r /agent/requirements.txt
RUN pip install uwsgi
RUN apk del .build-dependencies && rm -rf /var/cache/apk/*
EXPOSE 5003
CMD ["uwsgi", "--ini", "/agent/wsgi.ini"]