# Лабораторная работа №6

**Тема:** два контейнера для PHP-приложения без Docker Compose  
**Студент:** Bordeniuc Ivan  
**Группа:** I2402  

## Цель и идея

В работе нужно запустить PHP-сайт не в одном контейнере, а в двух: nginx принимает HTTP-запросы, а PHP-FPM выполняет PHP-код. Использовать `docker-compose` по условию нельзя.

## Файлы проекта

- `nginx/default.conf` - конфигурация nginx.
- `mounts/site/index.php` - демонстрационная PHP-страница.
- `mounts/site/styles.css` - простые стили страницы.
- `.gitignore` - правила для каталога сайта.

## Подготовка сети

Контейнеры подключаются к одной сети:

```text
docker network create internal
```

Сеть нужна, чтобы nginx мог обращаться к PHP-FPM по имени контейнера `backend`.

## Запуск backend

PHP-FPM запускается отдельно:

```text
docker run -d --name backend --network internal -v "${PWD}\mounts\site:/var/www/html" php:7.4-fpm
```

Контейнер не публикует порт на хост, потому что обращаться к нему должен только nginx.

## Запуск frontend

nginx запускается вторым контейнером:

```text
docker run -d --name frontend --network internal -p 80:80 -v "${PWD}\mounts\site:/var/www/html" -v "${PWD}\nginx\default.conf:/etc/nginx/conf.d/default.conf" nginx:1.23-alpine
```

Здесь публикуется порт 80, а также подключаются сайт и конфигурация nginx.

## Проверка результата

Проверка через браузер:

```text
http://localhost
```

Ожидаемый результат - страница с текстом о проверке связки nginx и PHP-FPM.

Проверка через терминал:

```text
curl.exe -I http://localhost
HTTP/1.1 200 OK
Server: nginx/1.23.4
Content-Type: text/html; charset=UTF-8
```

## Объяснение конфигурации nginx

Важная часть `nginx/default.conf`:

```nginx
location ~ \.php$ {
    fastcgi_pass backend:9000;
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    include fastcgi_params;
}
```

Именно `fastcgi_pass backend:9000` связывает nginx с контейнером PHP-FPM.

## Ответы на вопросы

**Как контейнеры взаимодействуют?**  
Они находятся в одной Docker-сети `internal`. Контейнер `frontend` отправляет PHP-запросы контейнеру `backend` по FastCGI.

**Как контейнеры видят друг друга?**  
Docker предоставляет внутренний DNS, поэтому имя контейнера `backend` работает как адрес внутри сети.

**Почему нужна своя конфигурация nginx?**  
Обычный nginx не выполняет PHP. В конфигурации нужно явно указать, что PHP-файлы следует передавать в PHP-FPM.

## Заключение

Лабораторная работа показывает базовую схему разделения веб-сервера и PHP-обработчика. Даже без Compose контейнеры можно связать через сеть Docker и общие bind mounts.

## Источники

- Docker Documentation: networks, bind mounts.
- nginx Documentation: FastCGI.
- PHP-FPM documentation.
