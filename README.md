## Paste this script into database

```bash
create database `patients`;

create table `patients`.`patient` (
 `id`    int(10) unsigned not null auto_increment,
 `name`  varchar(200) collate utf8_bin not null,
 `sex`  varchar(20)  collate utf8_bin not null,
 `religion`  varchar(20)  collate utf8_bin not null,
 `phone`  int(13)  unsigned not null,
 `address`  varchar(200) collate utf8_bin not null,
 `nik`  int(13)  unsigned not null,
 PRIMARY KEY (`id`)
)
```
## for run the project
```bash
php -S localhost:8000 -t / .htrouter.php
```