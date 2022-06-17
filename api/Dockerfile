FROM python:3.9.13-alpine
RUN apk add --virtual .build-dependencies \
    --no-cache \
    python3-dev \
    build-base \
    linux-headers \
    pcre-dev 
RUN apk add --no-cache pcre
COPY . /api
WORKDIR /api
RUN apk add libffi-dev
RUN python -m pip install --upgrade pip
RUN pip install --no-cache-dir -r /api/requirements.txt
RUN pip install uwsgi
RUN apk del .build-dependencies && rm -rf /var/cache/apk/*
EXPOSE 5002
CMD ["uwsgi", "--ini", "/api/wsgi.ini"]
