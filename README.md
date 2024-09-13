## Первое тестовое задание для вакансии PHP разработки (бот тг)
### Бот выполняет следующие действие:
#### 1) Первый запуск
При первом запуске, когда пользователь нажимает "START", бот добавляет пользователя в базу данных с балансом 0.00$;
#### 2) Действие бота
Возможные действия бота: прибавка, уменьшение и вывод баланса (при запуске скрипта botbalance.php):
###### а) Прибавление баланса
Для прибавки баланса пользователь должен написать целое или десятичную дробь (пример: "100", "0.01", число указывается без ковычек);
###### б) Уменьшение баланса
Для уменьшения баланса пользователь должен написать целое или десятичную дробь. Число не может быть отрицательным, поэтому если пользователь пытается списать с баланса число больше, чем у него на счете, бот напишет ошибку (пример: "-100","-0.01", число указывается без ковычек);
###### в) Вывод баланса
Для вывода баланса, пользователь должен написать боту "/balance" (без ковычек);
#### 3) Ответ бота
В ответ пользователю бот присылает сообщение с остатком на его счёте;

## Инструкция к запуску:
### Для запуска бота требуется установленный php и mysql (на устройстве с которого запускает скрипт), а также создать бота через бота в телеграме @BotFather.
#### 1) Загрузка бд
Для начала нужно загрузить резервную копию базы данных (присутствует в архиве) или выполнить sql запрос в mysql:
##### 1.Через панель phpmyadmin
###### а) Бекап бд telegram_bot_db
создать базу данных telegram_bot_db и портировать туда бд ;
###### б) Создание бд из запроса
перейти в панель SQL (ссылка на прямую: http://localhost/phpmyadmin/index.php?route=/server/sql);
и прописать: 
CREATE TABLE telegram_bot_db.users (
  id int(11) NOT NULL AUTO_INCREMENT,
  telegram_id bigint(20) NOT NULL,
  balance decimal(10, 2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (id)
)
ENGINE = INNODB,
AUTO_INCREMENT = 2,
AVG_ROW_LENGTH = 16384,
CHARACTER SET utf8mb4,
COLLATE utf8mb4_general_ci;

ALTER TABLE telegram_bot_db.users
ADD UNIQUE INDEX telegram_id (telegram_id);
##### 2. Через dbforge
создать базу данных telegram_bot_db и портировать туда бд;
#### 2) Добавление API включа бота
После этого, нужно открыть bot.php или botbalance.php в любом текстовом редакторе и в поле "$bot = new BotApi('');" прописать API бота, который будет получен при создании бота.
#### 3) Запуск бота
Для запуска бота, требуется открыть "Командную строку" или PowerShell в Windows или любой терминал в Linux и перейти в корневой каталог проета, куда он был выгружен:
C:\xampp\htdocs\sites\5 (пример)
#### 4) Запуск скирпта
Далее нужно выполнить скрипт:
###### а) php bot.php 
(версия для тестового задания);
###### б) php botbalance.php 
(версия с возможностью проверки баланса через команду /balance);
#### 5) Открыть приложение
Далее требуется открыть приложение Telegram или Web версию Telegram по ссылке: "https://web.telegram.org/";
#### 6) Поиск бота
После перехода в приложение или на сайт, нужно открыть поиск и прописать имя бота, которые вы получили при создании бота;
#### 7) Первый запуск и варианты действия
При первом запуска бота, требуется нажать на "Start", далее можно отправлять как целые числа: 100 или -100, так и с дробями (через знак "."): 0.1 или -0,1.
