-- Adminer 4.6.3 PostgreSQL dump

DROP TABLE IF EXISTS "authors";
DROP SEQUENCE IF EXISTS authors_author_id_seq;
CREATE SEQUENCE authors_author_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "public"."authors" (
    "author_id" integer DEFAULT nextval('authors_author_id_seq') NOT NULL,
    "firstname" character varying(20) NOT NULL,
    "lastname" character varying(20) NOT NULL,
    "about" text NOT NULL,
    CONSTRAINT "authors_pkey" PRIMARY KEY ("author_id")
) WITH (oids = false);

INSERT INTO "authors" ("author_id", "firstname", "lastname", "about") VALUES
(3,	'Виктор',	'Пелевин',	'Виктор Пелевин – современный русский прозаик, прославившийся в 90-е романами «Омон Ра», «Чапаев и Пустота» и «Generation „П“». Обладатель Малой Букеровской премии за сборник «Синий фонарь», «Национального бестселлера» за роман «ДПП NN» и ряда других наград.
'),
(1,	'Андрей',	'Курпатов',	'Андрей Курпатов – врач-психотерапевт, президент Высшей школы методологии, основатель и ведущий интеллектуального ток-шоу «Игры разума», автор более сотни научных работ, создатель системной поведенческой психотерапии и методологии мышления.'),
(2,	'Дэн!111',	'Браун',	'Дэн Браун – американский писатель и журналист, чьи произведения переводились на пятьдесят языков и издавались многомиллионными тиражами в различных странах мира...'),
(10,	'Дэн2',	'Браун',	'Дэн Браун – американский писатель и журналист, чьи произведения переводились на пятьдесят языков и издавались многомиллионными тиражами в различных странах мира.'),
(11,	'Дэн2',	'Браун',	'Дэн Браун – американский писатель и журналист, чьи произведения переводились на пятьдесят языков и издавались многомиллионными тиражами в различных странах мира.'),
(12,	'Дэн2',	'Браун',	'Дэн Браун – американский писатель и журналист, чьи произведения переводились на пятьдесят языков и издавались многомиллионными тиражами в различных странах мира.');

DROP TABLE IF EXISTS "authors_books";
CREATE TABLE "public"."authors_books" (
    "book" integer NOT NULL,
    "author" integer NOT NULL,
    CONSTRAINT "authorsBooks_book_author" PRIMARY KEY ("book", "author"),
    CONSTRAINT "authorsBooks_author_fkey" FOREIGN KEY (author) REFERENCES authors(author_id) ON UPDATE CASCADE ON DELETE CASCADE NOT DEFERRABLE,
    CONSTRAINT "authorsBooks_book_fkey" FOREIGN KEY (book) REFERENCES books(book_id) ON UPDATE CASCADE ON DELETE CASCADE NOT DEFERRABLE
) WITH (oids = false);

INSERT INTO "authors_books" ("book", "author") VALUES
(3,	2),
(4,	1),
(5,	3),
(11,	3),
(5,	1),
(5,	2),
(89,	1),
(89,	2),
(90,	1),
(90,	2);

DROP TABLE IF EXISTS "books";
DROP SEQUENCE IF EXISTS books_book_id_seq;
CREATE SEQUENCE books_book_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "public"."books" (
    "book_id" integer DEFAULT nextval('books_book_id_seq') NOT NULL,
    "title" character varying(255) NOT NULL,
    "shortdescription" text NOT NULL,
    "price" double precision NOT NULL,
    "category" integer NOT NULL,
    CONSTRAINT "articles_pkey" PRIMARY KEY ("book_id"),
    CONSTRAINT "books_category_fkey" FOREIGN KEY (category) REFERENCES categories(category_id) ON UPDATE CASCADE ON DELETE CASCADE NOT DEFERRABLE
) WITH (oids = false);

INSERT INTO "books" ("book_id", "title", "shortdescription", "price", "category") VALUES
(3,	'Происхождение видов',	'«Происхождение» – пятая книга американского писателя Дэна Брауна о гарвардском профессоре, специалисте по религиозной символике Роберте Лэнгдоне. В этот раз все начинается с, возможно, одного из наиболее знаковых событий в истории: наконец-то стало известно, откуда произошло человечество. Футуролог Эдмонд Кирш, совершивший невероятное открытие, был всего лишь в шаге от того, чтобы полностью изменить представление современников о мире. Однако его речи не суждено было прозвучать в стенах Музея Гуггенхайма. Ученого убили на глазах гостей. И начался хаос…',	11.31,	3),
(5,	'Омон Ра',	'Первый роман Виктора Пелевина, написанный в 1991 году. Представляет собой полупародию на воспитательные романы советской эпохи и по жанру близок к триллеру. Характерно внимание к деталям, которые в финале складываются в одну картину.',	35,	1),
(11,	'Empire V',	'Восьмой роман Виктора Пелевина.',	25,	3),
(4,	'Происхождение видов',	'«Происхождение» – пятая книга американского писателя Дэна Брауна о гарвардском профессоре, специалисте по религиозной символике Роберте Лэнгдоне. В этот раз все начинается с, возможно, одного из наиболее знаковых событий в истории: наконец-то стало известно, откуда произошло человечество. Футуролог Эдмонд Кирш, совершивший невероятное открытие, был всего лишь в шаге от того, чтобы полностью изменить представление современников о мире. Однако его речи не суждено было прозвучать в стенах Музея Гуггенхайма. Ученого убили на глазах гостей. И начался хаос',	11.31,	3),
(89,	'Происхождение видов333',	'«Происхождение» – пятая книга американского писателя Дэна Брауна о гарвардском профессоре, специалисте по религиозной символике Роберте Лэнгдоне. В этот раз все начинается с, возможно, одного из наиболее знаковых событий в истории: наконец-то стало известно, откуда произошло человечество. Футуролог Эдмонд Кирш, совершивший невероятное открытие, был всего лишь в шаге от того, чтобы полностью изменить представление современников о мире. Однако его речи не суждено было прозвучать в стенах Музея Гуггенхайма. Ученого убили на глазах гостей. И начался хаос…',	11.35,	3),
(87,	'Происхождение видов333',	'«Происхождение» – пятая книга американского писателя Дэна Брауна о гарвардском профессоре, специалисте по религиозной символике Роберте Лэнгдоне. В этот раз все начинается с, возможно, одного из наиболее знаковых событий в истории: наконец-то стало известно, откуда произошло человечество. Футуролог Эдмонд Кирш, совершивший невероятное открытие, был всего лишь в шаге от того, чтобы полностью изменить представление современников о мире. Однако его речи не суждено было прозвучать в стенах Музея Гуггенхайма. Ученого убили на глазах гостей. И начался хаос…',	11.31,	3),
(88,	'Происхождение видов333',	'«Происхождение» – пятая книга американского писателя Дэна Брауна о гарвардском профессоре, специалисте по религиозной символике Роберте Лэнгдоне. В этот раз все начинается с, возможно, одного из наиболее знаковых событий в истории: наконец-то стало известно, откуда произошло человечество. Футуролог Эдмонд Кирш, совершивший невероятное открытие, был всего лишь в шаге от того, чтобы полностью изменить представление современников о мире. Однако его речи не суждено было прозвучать в стенах Музея Гуггенхайма. Ученого убили на глазах гостей. И начался хаос…',	11.31,	3),
(90,	'Происхождение видов333',	'«Происхождение» – пятая книга американского писателя Дэна Брауна о гарвардском профессоре, специалисте по религиозной символике Роберте Лэнгдоне. В этот раз все начинается с, возможно, одного из наиболее знаковых событий в истории: наконец-то стало известно, откуда произошло человечество. Футуролог Эдмонд Кирш, совершивший невероятное открытие, был всего лишь в шаге от того, чтобы полностью изменить представление современников о мире. Однако его речи не суждено было прозвучать в стенах Музея Гуггенхайма. Ученого убили на глазах гостей. И начался хаос…',	11.31,	3);

DROP TABLE IF EXISTS "booksOrders";
CREATE TABLE "public"."booksOrders" (
    "order" integer NOT NULL,
    "book" integer NOT NULL,
    CONSTRAINT "articles_authors_pkey" PRIMARY KEY ("order", "book"),
    CONSTRAINT "authorsOrders_book_fkey" FOREIGN KEY (book) REFERENCES books(book_id) ON UPDATE CASCADE ON DELETE CASCADE NOT DEFERRABLE,
    CONSTRAINT "booksOrders_order_fkey" FOREIGN KEY ("order") REFERENCES orders(order_id) ON UPDATE CASCADE ON DELETE CASCADE NOT DEFERRABLE
) WITH (oids = false);

INSERT INTO "booksOrders" ("order", "book") VALUES
(2,	3),
(2,	5),
(4,	11);

DROP TABLE IF EXISTS "categories";
DROP SEQUENCE IF EXISTS categories_category_id_seq;
CREATE SEQUENCE categories_category_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "public"."categories" (
    "category_id" integer DEFAULT nextval('categories_category_id_seq') NOT NULL,
    "name" text NOT NULL,
    CONSTRAINT "categories_pkey" PRIMARY KEY ("category_id")
) WITH (oids = false);

INSERT INTO "categories" ("category_id", "name") VALUES
(1,	'история
'),
(2,	'Проза'),
(3,	'научно-популярная литература'),
(4,	'Стихи'),
(5,	'Приключения');

DROP TABLE IF EXISTS "orders";
DROP SEQUENCE IF EXISTS orders_order_id_seq;
CREATE SEQUENCE orders_order_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "public"."orders" (
    "order_id" integer DEFAULT nextval('orders_order_id_seq') NOT NULL,
    "orderdate" text DEFAULT 'current_timestamp' NOT NULL,
    "user" integer NOT NULL,
    "status" character varying NOT NULL,
    CONSTRAINT "order_pk" PRIMARY KEY ("order_id"),
    CONSTRAINT "orders_user_fkey" FOREIGN KEY ("user") REFERENCES users(user_id) ON UPDATE CASCADE ON DELETE CASCADE NOT DEFERRABLE
) WITH (oids = false);

INSERT INTO "orders" ("order_id", "orderdate", "user", "status") VALUES
(2,	'27.01.2019',	2,	'pending'),
(4,	'25.01.2019',	1,	'pending'),
(5,	'25.01.2019',	1,	'pending'),
(6,	'21.01.2019',	2,	'pending'),
(8,	'21.01.2019',	3,	'pending'),
(7,	'21.01.2019',	3,	'pending');

DROP TABLE IF EXISTS "users";
DROP SEQUENCE IF EXISTS users_user_id_seq;
CREATE SEQUENCE users_user_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "public"."users" (
    "user_id" integer DEFAULT nextval('users_user_id_seq') NOT NULL,
    "firstname" character varying(30) NOT NULL,
    "lastname" character varying(30) NOT NULL,
    "email" character varying(30) NOT NULL,
    "phonenumber" character varying(50) NOT NULL,
    "roles" character(25),
    "username" character(25),
    "password" text,
    CONSTRAINT "users_pkey" PRIMARY KEY ("user_id")
) WITH (oids = false);

INSERT INTO "users" ("user_id", "firstname", "lastname", "email", "phonenumber", "roles", "username", "password") VALUES
(1,	'Ольга',	'Лицкевич',	'olha.litskevich@gmail.com',	'375291761491',	NULL,	NULL,	NULL),
(7,	'Оля',	'Лицкевич',	'olha.litskevich@gmail.com',	'+375291761495',	NULL,	NULL,	NULL),
(8,	'Оля',	'Лицкевич',	'olha.litskevich@gmail.com',	'+375291761495',	NULL,	NULL,	NULL),
(9,	'Оля',	'Лицкевич',	'olha.litskevich@gmail.com',	'3752++917614945',	NULL,	NULL,	NULL),
(13,	'Оля',	'Лицкевич',	'olha.litskevich@gmail.com',	'375291761491',	NULL,	NULL,	NULL),
(3,	'Olga',	'Litsk',	'olha.litskevich@gmail.com',	'375291761495',	NULL,	NULL,	NULL),
(14,	'Оля',	'Лицкевич',	'olha.litskevich@gmail.com',	'375291761491',	NULL,	NULL,	NULL),
(15,	'Оля7777',	'Лицкевич',	'olha.litskevich@gmail.com',	'3752++91761495',	NULL,	NULL,	NULL),
(16,	'Оля888',	'Лицкевич',	'olha.litskevich@gmail.com',	'+375291761495',	NULL,	NULL,	NULL),
(17,	'Ольга',	'Лицкевич',	'olha.litskevich@gmail.com',	'+375291761491',	'0                        ',	NULL,	NULL),
(10,	'Оля',	'Лицкевич',	'olha.litskevich@gmail.com',	'375291761491',	NULL,	NULL,	NULL),
(18,	'Ольга',	'Лицкевич',	'olha.litskevich@gmail.com',	'+375291761491',	'USER_ROLE                ',	'olga                     ',	'1111'),
(2,	'Оля',	'Лицкевич',	'olha.litskevich@gmail.com',	'375291761491',	'ROLE_ADMIN               ',	'olga_user                ',	'5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg=='),
(4,	'Ольга100',	'Лицкевич',	'olha.litskevich@gmail.com',	'375291761491',	'ROLE_USER                ',	'olga_user                ',	'qwerty'),
(5,	'Ольга100',	'Лицкевич',	'olha.litskevich@gmail.com',	'375291761491',	'ROLE_USER                ',	'olga_user                ',	'qwerty');

-- 2019-02-11 21:17:27.946503+00
